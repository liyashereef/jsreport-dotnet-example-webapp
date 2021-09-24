<?php

namespace Modules\Timetracker\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeTimeoffRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $authorityName;
    public $timeOffRequest;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($authorityName, $timeOffRequest)
    {
        $this->authorityName = $authorityName;
        $this->timeOffRequest = $timeOffRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Employee Timeoff Request")->markdown('timetracker::mail.employeeTimeoff.employee-time-off-request')->with(['timeOffRequest' => $this->timeOffRequest, 'authorityName' => $this->authorityName]);
    }
}
