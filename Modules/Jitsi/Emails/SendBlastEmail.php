<?php

namespace Modules\Jitsi\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBlastEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $body_message;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($body_message)
    {
        //
        $this->body_message = $body_message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $body_message = $this->body_message;
        // return $this->markdown('emails.sendBlastMail')
        //     ->with(['body_message' => $body_message]);
        return $this->from('mail@example.com', 'Mailtrap')
            ->subject('Test Queued Email')
            ->view('emails.sendBlastMail');
    }
}
