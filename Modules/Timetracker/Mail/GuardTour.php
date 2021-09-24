<?php

namespace Modules\Timetracker\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuardTour extends Mailable
{
    use Queueable, SerializesModels;

    public $employeeShift;
    public $areaManager;
    public $guard_tour_counts;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($areaManager, $employee_shift, $guard_tour_counts)
    {
        $this->employeeShift = $employee_shift;
        $this->areaManager = $areaManager;
        $this->guard_tour_counts = $guard_tour_counts;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Guard Tour Missing")->markdown('timetracker::mail.guard-tour');
    }
}
