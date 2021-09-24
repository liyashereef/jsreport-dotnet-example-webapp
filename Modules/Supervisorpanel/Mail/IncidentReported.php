<?php

namespace Modules\SupervisorPanel\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IncidentReported extends Mailable
{

    use Queueable,
        SerializesModels;

    public $template, $model,$filepath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($model, $template,$filepath)
    {
        $this->model = $model;
        $this->template = $template;
        $this->filepath = $filepath; 
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New incident Reported at ' . $this->model->customer->client_name)->markdown('supervisorpanel::' . $this->template)->with(['model' => $this->model])->attach( $this->filepath, [ 
                         'as' => $this->model->attachment, 
                         'mime' => 'application/pdf', 
                    ]); ;
    }

}
