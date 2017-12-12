<?php

namespace App\Jobs;

use App\Mail\Notification;
use App\Mail\UserCreated;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use function Psy\debug;

class SendEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected  $data;
    protected  $text;

    /**
     * Create a new job instance.
     *
     * @param User $data
     * @param String $pass
     */
    public function __construct($data, $text)
    {
        $this->data = $data;
        $this->text = $text;

    }

    /**
     * Execute the job - Send Email.
     * @return void
     */
    public function handle()
    {
        Mail::to($this->data['email'])
            ->send(new Notification(
                $this->data['fullName'], $this->text
            ));
    }
}
