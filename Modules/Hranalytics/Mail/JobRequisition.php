<?php

namespace Modules\Hranalytics\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobRequisition extends Mailable
{

    use Queueable,
        SerializesModels;

    public $template, $model, $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($model, $template, $subject)
    {
        $this->template = $template;
        $this->model = $model;
        $this->subject = $subject ?? 'Job Requisition';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->markdown('hranalytics::' . $this->template)->with(['job' => $this->model]);
    }

}
