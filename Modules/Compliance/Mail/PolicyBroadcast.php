<?php

namespace Modules\Compliance\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PolicyBroadcast extends Mailable
{

    use Queueable,
        SerializesModels;

    public $template, $model;

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
        return $this->markdown('compliance::' . $this->template)->with(['request' => $this->model]);
    }

}
