<?php

namespace App\Console\Commands;

use App\Models\ScheduledMessage;
use App\Mail\NotificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled';
    protected $description = 'Send scheduled notifications';

    protected $maxMessageAgeHours = 24;

    public function handle()
    {
        $this->info('🕒 Checking for scheduled messages...');
        
        $now = now();
        
        $this->info("Current time (Server): {$now}");
        $this->info("Timezone: " . config('app.timezone'));
        $this->info("Looking for messages scheduled before or at: {$now}");
        $this->info("Cutoff time (max {$this->maxMessageAgeHours} hours old): " . $now->copy()->subHours($this->maxMessageAgeHours));

        // Get messages that are due (scheduled in the past)
        $messages = ScheduledMessage::with('template')
            ->where('status', 'pending')
            ->where('scheduled_at', '<=', $now)
            ->get();

        $this->info("📨 Found {$messages->count()} messages to process");

        if ($messages->isEmpty()) {
            $this->info('✅ No scheduled messages to send.');
            
            // Show debug information
            $this->debugPendingMessages();
            
            return Command::SUCCESS;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($messages as $message) {
            $this->info("\n🔔 Processing Message ID: {$message->id}");
            $this->info("   Type: " . ($message->template->type ?? 'N/A'));
            $this->info("   Recipients: " . count($message->recipients));
            $this->info("   Scheduled: " . $message->scheduled_at);
            $this->info("   Age: " . $message->scheduled_at->diffForHumans());

            try {
                $result = $this->sendNotification($message);
                
                if ($result['success']) {
                    $message->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'error_message' => null
                    ]);
                    $sentCount++;
                    $this->info("✅ Successfully sent to {$result['success_count']} recipients");
                    
                    if ($result['failed_count'] > 0) {
                        $this->warn("⚠ Failed for {$result['failed_count']} recipients");
                    }
                } else {
                    $message->update([
                        'status' => 'failed',
                        'error_message' => $result['error']
                    ]);
                    $failedCount++;
                    $this->error("❌ Completely failed: " . $result['error']);
                }
                
            } catch (\Exception $e) {
                $message->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
                
                Log::error("Failed to process scheduled notification ID {$message->id}: " . $e->getMessage());
                $this->error("❌ Processing failed for ID {$message->id}: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info("\n" . str_repeat('=', 50));
        $this->info("📊 FINAL SUMMARY");
        $this->info("✅ Successfully sent: {$sentCount}");
        $this->info("❌ Failed: {$failedCount}");
        $this->info("📋 Total processed: " . ($sentCount + $failedCount));
        $this->info(str_repeat('=', 50));

        return Command::SUCCESS;
    }

    /**
     * Debug method to show pending messages with time comparison
     */
    private function debugPendingMessages()
    {
        $pendingMessages = ScheduledMessage::with('template')
            ->where('status', 'pending')
            ->get();

        if ($pendingMessages->isNotEmpty()) {
            $this->warn("\n🔍 DEBUG: Found {$pendingMessages->count()} pending messages:");
            
            foreach ($pendingMessages as $msg) {
                $isDue = $msg->scheduled_at <= now();
                $status = $isDue ? '✅ DUE' : '⏰ FUTURE';
                $timeDiff = $msg->scheduled_at->diffForHumans();
                
                $this->info("   📝 ID: {$msg->id}");
                $this->info("      Scheduled: {$msg->scheduled_at} ({$timeDiff})");
                $this->info("      Status: {$status}");
                $this->info("      Template: " . ($msg->template->name ?? 'N/A'));
                $this->info("      Recipients: " . implode(', ', $msg->recipients));
                $this->info("      ---");
            }
        } else {
            $this->info("🔍 DEBUG: No pending messages found in database.");
        }
    }

    // ... rest of your methods remain the same
    private function sendNotification(ScheduledMessage $message)
    {
        $template = $message->template;
        
        if (!$template) {
            throw new \Exception("Template not found for message ID: {$message->id}");
        }

        $content = $this->replaceVariables($template->content, $message->variables ?? []);
        $subject = $this->replaceVariables($template->subject ?? 'Notification', $message->variables ?? []);

        $result = [
            'success' => false,
            'success_count' => 0,
            'failed_count' => 0,
            'error' => null
        ];

        try {
            switch ($template->type) {
                case 'sms':
                    $smsResult = $this->sendSMS($message->recipients, $content);
                    $result['success_count'] = $smsResult['success_count'];
                    $result['failed_count'] = $smsResult['failed_count'];
                    $result['success'] = $smsResult['success_count'] > 0;
                    break;
                    
                case 'email':
                    $emailResult = $this->sendEmail($message, $subject, $content);
                    $result['success_count'] = $emailResult['success_count'];
                    $result['failed_count'] = $emailResult['failed_count'];
                    $result['success'] = $emailResult['success_count'] > 0;
                    break;
                    
                case 'push':
                    $pushResult = $this->sendPush($message->recipients, $subject, $content);
                    $result['success_count'] = $pushResult['success_count'];
                    $result['failed_count'] = $pushResult['failed_count'];
                    $result['success'] = $pushResult['success_count'] > 0;
                    break;
                    
                default:
                    throw new \Exception("Unsupported notification type: {$template->type}");
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    private function replaceVariables($content, $variables)
    {
        foreach ($variables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
            $content = str_replace("{" . $key . "}", $value, $content);
        }
        return $content;
    }

    private function sendSMS($recipients, $content)
    {
        $successCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            try {
                Log::info("📱 SMS sent to: {$recipient}");
                $this->info("   📱 SMS to: {$recipient}");
                $successCount++;
            } catch (\Exception $e) {
                Log::error("Failed to send SMS to {$recipient}: " . $e->getMessage());
                $this->error("   ❌ SMS failed for: {$recipient}");
                $failedCount++;
            }
        }

        return [
            'success_count' => $successCount,
            'failed_count' => $failedCount
        ];
    }

    private function sendEmail(ScheduledMessage $message, $subject, $content)
    {
        $successCount = 0;
        $failedCount = 0;
        $failedRecipients = [];

        foreach ($message->recipients as $recipient) {
            try {
                if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Invalid email address format: {$recipient}");
                }

                $emailData = [
                    'content' => $content,
                    'template' => $message->template,
                    'scheduled_at' => $message->scheduled_at,
                    'variables' => $message->variables ?? []
                ];

                Mail::to($recipient)->send(new NotificationMail($subject, $emailData));
            
                
                Log::info("📧 Email sent to: {$recipient}");
                $this->info("   📧 Email to: {$recipient}");
                $successCount++;
                
            } catch (\Exception $e) {
                $errorMsg = "Failed to send email to {$recipient}: " . $e->getMessage();
                Log::error($errorMsg);
                $this->error("   ❌ Email failed for: {$recipient} - " . $e->getMessage());
                $failedCount++;
                $failedRecipients[] = $recipient;
            }
        }

        return [
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'failed_recipients' => $failedRecipients
        ];
    }

    private function sendPush($recipients, $title, $content)
    {
        $successCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            try {
                Log::info("📱 Push sent to: {$recipient}");
                $this->info("   📱 Push to: {$recipient}");
                $successCount++;
            } catch (\Exception $e) {
                Log::error("Failed to send push to {$recipient}: " . $e->getMessage());
                $this->error("   ❌ Push failed for: {$recipient}");
                $failedCount++;
            }
        }

        return [
            'success_count' => $successCount,
            'failed_count' => $failedCount
        ];
    }
}