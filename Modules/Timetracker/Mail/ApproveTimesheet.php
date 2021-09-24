<?php

namespace  Modules\Timetracker\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApproveTimesheet extends Mailable
{
    use Queueable, SerializesModels;
    
    public $admin;
    public $guard;
    public $supervisor;


    
    /**
     * 
     * Create a new message instance.
     * 
     * @param type $adminName
     * @param type $guardName
     * @param type $supervisorName
     * @return void
     */
    public function __construct($adminName, $guardName, $supervisorName)
    {
        $this->admin = $adminName;
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
        return $this->subject("Timesheet Approval")->markdown('timetracker::mail.approve-timesheet');
    }
}
