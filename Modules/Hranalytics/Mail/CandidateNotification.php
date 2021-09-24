<?php

namespace Modules\Hranalytics\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CandidateNotification extends Mailable
{
    use Queueable, SerializesModels;

    private $template, $model,$filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($model, $template,$filename)
    {
        $this->template = $template;
        $this->model = $model;
        $this->filename = $filename;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       return $this->subject('Application Submitted Successfully' )->markdown('hranalytics::' . $this->template)->with(['candidate' => $this->model])->attach($this->filename,
                [
                    'as' => 'candidate_application.pdf',
                    'mime' => 'application/pdf',
                ]);
    }
}
