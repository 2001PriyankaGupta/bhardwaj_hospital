<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DoctorWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $doctor;
    public $plainPassword;

    public function __construct($doctor, $plainPassword)
    {
        $this->doctor = $doctor;
        $this->plainPassword = $plainPassword;
    }

    public function build()
    {
        return $this->subject('Welcome to Our Medical Platform - Account Details')
                    ->view('emails.doctor-welcome')
                    ->with([
                        'doctor' => $this->doctor,
                        'password' => $this->plainPassword,
                    ]);
    }
}