<?php

namespace Modules\Jitsi\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Jitsi\Emails\SendBlastEmail;
use Illuminate\Support\Facades\Mail;
use Modules\Jitsi\Models\EmailBlastLog;

class SendBlastEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $send_mail, $body_message, $mailSubject;
    protected $userDetails;
    public $tries = 5;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userDetails, $send_mail, $mailSubject, $body_message)
    {
        $this->send_mail = $send_mail;
        $this->body_message = $body_message;
        $this->mailSubject = $mailSubject;
        $this->userDetails = $userDetails;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // $email = new SendBlastEmail($this->body_message);
            $body_message = $this->body_message;
            $mailSubject = $this->mailSubject;
            $userDetails = $this->userDetails;
            // $html = view('emails.sendBlastMail', compact('body_message'))->render();

            $transport = (new \Swift_SmtpTransport($userDetails["mail_host"], $userDetails["mail_port"]))
                ->setUsername($userDetails["mail_username"])
                ->setPassword($userDetails["mail_password"]);

            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($transport);
            // Create a message
            $message = (new \Swift_Message($mailSubject))
                ->setFrom([$userDetails["mail_from_address"] => $userDetails["mail_from_name"]])
                ->setTo([$this->send_mail])
                ->setBody($body_message, 'text/html');

            // Se   nd the message
            $result = $mailer->send($message);

            if ($result) {
                $userDetails = $this->userDetails;
                $identifier = $userDetails["identifier"];
                if ($identifier > 0) {
                    $user_id = $userDetails["user"];
                    // EmailBlastFailedJob::insert([
                    //     "identifier" => $identifier,
                    //     "sendMail" => $sendMail,
                    //     "user" => $user,
                    //     "created_at" => \Carbon::now()
                    // ]);
                    $emailIdCollection = EmailBlastLog::find($identifier);
                    $emailRecip = $emailIdCollection->email_recipient;
                    $emailRecipcollection = $emailRecip;
                    $emailRecipcollection[$user_id]["failed"] = false;

                    $emailIdUpdate = EmailBlastLog::find($identifier);
                    $emailIdUpdate->email_recipient = (object)$emailRecipcollection;
                    $emailIdUpdate->save();
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function failed(Exception $exception)
    {
        $userDetails = $this->userDetails;
        $identifier = $userDetails["identifier"];
        if ($identifier > 0) {
            $user_id = $userDetails["user"];
            $emailIdCollection = EmailBlastLog::find($identifier);
            $emailRecip = $emailIdCollection->email_recipient;
            $emailRecipcollection = $emailRecip;
            $emailRecipcollection[$user_id]["failed"] = true;

            $emailIdUpdate = EmailBlastLog::find($identifier);
            $emailIdUpdate->email_recipient = (object)$emailRecipcollection;
            $emailIdUpdate->save();
        }
    }
}
