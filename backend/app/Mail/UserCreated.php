<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $registration;
    public $password;

    /**
     * Create a new message instance.
     *
     * @param $userName
     * @param $registration
     * @param $password
     */
    public function __construct($userName, $registration, $password)
    {
        $this->userName = $userName;
        $this->registration = $registration;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.registeredUser');
    }
}
