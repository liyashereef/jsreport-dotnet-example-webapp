<?php

namespace Modules\Reports\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecrutingAnalyticsReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $template, $model, $filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template, $filepath, $filename)
    {
        $this->template = $template;
        $this->filepath = $filepath;
        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Recruiting Analytics Report')->markdown('reports::' . $this->template)->attach(
            $this->filepath,
            [
                'as' => $this->filename,
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }
}
