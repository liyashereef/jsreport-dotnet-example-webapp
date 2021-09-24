<?php

namespace Modules\Hranalytics\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OpenShiftApproval extends Mailable
{

    use Queueable,
        SerializesModels;

    public $template, $model, $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($model, $template)
    {
        $this->template = $template;
        $this->model = $model;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Openshift Approval')->markdown('hranalytics::' . $this->template)->with(['openshift' => $this->model]);
    }

}
