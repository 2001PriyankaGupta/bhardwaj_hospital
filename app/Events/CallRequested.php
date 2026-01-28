<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $appointmentId;
    public $patient;

    public function __construct($appointmentId, $patient)
    {
        $this->appointmentId = $appointmentId;
        $this->patient = $patient;
    }

    public function broadcastWith()
    {
        return [
            'appointment_id' => $this->appointmentId,
            'patient' => $this->patient,
        ];
    }

    public function broadcastOn()
    {
        // doctor.{doctorId} channel will be used by frontend to listen
        return new PrivateChannel('doctor.' . ($this->patient->doctor_id ?? '0'));
    }
}
