<?php

namespace App\Jobs;

use App\Mail\UserCreated;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private  $data;
    private  $pass;

    /**
     * Create a new job instance.
     *
     * @param User $data
     * @param String $pass
     */
    public function __construct(User $data, String $pass)
    {
        $this->data = $data;
        $this->pass = $pass;
    }

    /**
     * Execute the job - Send Email.
     * @return void
     */
    public function handle()
    {
        Mail::to($this->data['email'])->send(new UserCreated($this->data['fullName'], $this->data['registration'], $this->pass));
    }
}
