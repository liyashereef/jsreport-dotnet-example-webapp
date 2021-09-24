<?php

namespace Modules\Timetracker\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubmitTimesheet extends Mailable
{
    use Queueable, SerializesModels;
    
    public $guard;
    public $supervisor;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($guardName,$supervisorName)
    {
        $this->guard = $guardName;
        $this->supervisor = $supervisorName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Timesheet Submission")->markdown('timetracker::mail.submit-timesheet');
    }
}
