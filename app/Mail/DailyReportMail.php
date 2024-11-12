<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->view('dailyReport')
                    ->subject('Your Daily Task Report')
                    ->attach($this->filePath, [
                        'as' => 'daily-tasks-report.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
