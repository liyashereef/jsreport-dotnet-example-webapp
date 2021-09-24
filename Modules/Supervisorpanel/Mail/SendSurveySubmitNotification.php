<?php

namespace Modules\SupervisorPanel\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendSurveySubmitNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $template;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Submit Survey')->markdown('supervisorpanel::' . $this->template);
    }
}
