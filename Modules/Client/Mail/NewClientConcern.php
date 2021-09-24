<?php

namespace Modules\Client\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewClientConcern extends Mailable
{

    use Queueable,
        SerializesModels;

    public $template, $model,$subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$model, $template)
    {
        $this->subject = $subject;
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
        return $this->subject($this->subject)->markdown('client::' . $this->template)->with(['body' => $this->model]);
    }

}
