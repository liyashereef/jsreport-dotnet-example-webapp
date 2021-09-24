<?php

namespace Modules\Timetracker\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $new_pass;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($new_pass)
    {
        $this->new_pass = $new_pass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('timetracker::mail.forgot-password');
    }
}
