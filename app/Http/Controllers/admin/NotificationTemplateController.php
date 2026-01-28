<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\ScheduledMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationTemplateController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $templates = NotificationTemplate::latest()->get();
        $scheduledMessages = ScheduledMessage::with('template')
            ->upcoming()
            ->pending()
            ->latest()
            ->get();

        return view($user->user_type.'.notification.index', compact('templates', 'scheduledMessages'));
    }

    public function create()
    {
            $user = Auth::user();
        return view($user->user_type.'.notification.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:sms,email,push',
            'subject' => 'required_if:type,email|nullable|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $template = NotificationTemplate::create([
            'name' => $request->name,
            'type' => $request->type,
            'subject' => $request->subject,
            'content' => $request->content,
            'variables' => $request->variables ? explode(',', $request->variables) : [],
            'status' => $request->has('status')
        ]);

        return redirect()->route($user->user_type.'.notifications.index')
            ->with('success', 'Template created successfully!');
    }

    public function edit(NotificationTemplate $notification)
    {
        $user = Auth::user();
        return view($user->user_type.'.notification.edit', compact('notification'));
    }


    public function update(Request $request, NotificationTemplate $notification)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:sms,email,push',
            'subject' => 'required_if:type,email|nullable|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $notification->update([
            'name' => $request->name,
            'type' => $request->type,
            'subject' => $request->subject,
            'content' => $request->content,
            'variables' => $request->variables ? explode(',', $request->variables) : [],
            'status' => $request->has('status')
        ]);

        return redirect()->route($user->user_type.'.notifications.index')
            ->with('success', 'Template updated successfully!');
    }

    public function destroy(NotificationTemplate $notification)
    {
        $user = Auth::user();
        $notification->delete();

        return redirect()->route($user->user_type.'.notifications.index')
            ->with('success', 'Template deleted successfully!');
    }

    public function showScheduleForm(NotificationTemplate $notification)
    {
        $user = Auth::user();
        return view($user->user_type.'.notification.schedule', compact('notification'));
    }

    public function scheduleMessage(Request $request, NotificationTemplate $notification)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'recipients' => 'required|string',
            // Accept HTML5 datetime-local format (YYYY-MM-DDTHH:MM)
            'scheduled_at' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:now'],
            'variables' => 'nullable|array'
        ], [
            'scheduled_at.date_format' => 'Please provide a valid date & time in your local machine format (YYYY-MM-DDTHH:MM).',
            'scheduled_at.after_or_equal' => 'The scheduled time must be now or a future time.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Normalize recipients: split by comma, semicolon, or newline, trim and remove empty entries
        $rawRecipients = preg_split('/[,;\r\n]+/', $request->recipients);
        $recipients = array_values(array_filter(array_map('trim', $rawRecipients), function($r) {
            return $r !== '' && $r !== null;
        }));

        // Validate recipients based on template type and collect invalid ones
        $invalidRecipients = [];
        foreach ($recipients as $recipient) {
            if (!$this->validateRecipient($recipient, $notification->type)) {
                $invalidRecipients[] = $recipient;
            }
        }

        if (!empty($invalidRecipients)) {
            // If someone accidentally included empty entries, suggest how to enter multiple recipients
            $msg = 'Invalid recipients: ' . implode(', ', $invalidRecipients);
            $msg .= '. Use commas, semicolons, or new lines to separate multiple recipients.';
            return redirect()->back()
                ->withErrors(['recipients' => $msg])
                ->withInput();
        }

        // Parse scheduled_at using the expected datetime-local format
        try {
            $scheduledAt = Carbon::createFromFormat('Y-m-d\TH:i', $request->scheduled_at, config('app.timezone'));
            // Normalize to server timezone object
            $scheduledAt = $scheduledAt->setTimezone(config('app.timezone'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['scheduled_at' => 'Invalid date format'])->withInput();
        }

        ScheduledMessage::create([
            'template_id' => $notification->id,
            'recipients' => $recipients,
            'variables' => $request->variables ?? [],
            'scheduled_at' => $scheduledAt,
            'status' => 'pending'
        ]);

        return redirect()->route($user->user_type.'.notifications.index')
            ->with('success', 'Message scheduled successfully for ' . $scheduledAt->format('M d, Y H:i'));
    }

    private function validateRecipient($recipient, $type)
    {
        switch ($type) {
            case 'email':
                return filter_var($recipient, FILTER_VALIDATE_EMAIL);
            case 'sms':
                // Accept international or local numbers: 8-15 digits, optional leading +
                return preg_match('/^\+?[0-9]{8,15}$/', $recipient);            case 'push':
                // Push token validation
                return !empty($recipient) && strlen($recipient) > 10;
            default:
                return false;
        }
    }

    public function cancelScheduledMessage(ScheduledMessage $scheduledMessage)
    {
        $user = Auth::user();
        $scheduledMessage->update(['status' => 'cancelled']);

        return redirect()->route($user->user_type.'.notifications.index')
            ->with('success', 'Scheduled message cancelled!');
    }


    public function forceSendMessage(ScheduledMessage $scheduledMessage)
    {

        $scheduledMessage->update([
            'scheduled_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Message will be sent in the next scheduled run!');
    }
}
