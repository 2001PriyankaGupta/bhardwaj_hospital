<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveAppliedNotification extends Notification
{
    use Queueable;

    protected $leave;
    protected $applicant;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($leave, $applicant, $type = 'doctor')
    {
        $this->leave = $leave;
        $this->applicant = $applicant;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $title = ucfirst($this->type) . " Leave Applied";
        $message = "{$this->applicant->name} has applied for " . ucfirst($this->leave->leave_type) . " leave from {$this->leave->start_date->format('d M Y')} to {$this->leave->end_date->format('d M Y')}.";
        
        return [
            'title' => $title,
            'message' => $message,
            'leave_id' => $this->leave->id,
            'applicant_id' => $this->applicant->id,
            'applicant_name' => $this->applicant->name,
            'applicant_type' => $this->type,
            'type' => 'leave_application',
            'action_url' => $this->type === 'doctor' ? route('admin.doctors.leaves', $this->applicant->id) : route('admin.staff.index'), // Adjusted later if staff leave exists
        ];
    }
}
