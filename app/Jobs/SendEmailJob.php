<?php
namespace App\Jobs;

use App\Mail\SendEmailTest;
use App\Mail\DailyReportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $users;
    protected $filePath;
    /**
    * Create a new job instance.
    *
    * @return void
    */
    public function __construct($users, $filePath)
    {
        $this->users = $users;
        $this->filePath = $filePath;

    }
    /**
    * Execute the job.
    *
    * @return void
    */
    public function handle()
    {
        $email = new DailyReportMail($this->filePath);
        Mail::to($this->users)->send($email);
    }
}
