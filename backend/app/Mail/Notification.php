<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $text;


    /**
     * Create a new message instance.
     *
     * @param $userName
     * @param $registration
     * @param $password
     */
    public function __construct($userName, $text)
    {
        $this->userName = $userName;
        $this->text = $text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.notificationEmail');
    }
}
