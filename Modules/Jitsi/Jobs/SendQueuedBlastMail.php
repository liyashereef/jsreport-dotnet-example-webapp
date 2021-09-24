<?php

namespace Modules\Jitsi\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Admin\Models\User;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\EmailAccountsMaster;
use Modules\Jitsi\Jobs\SendBlastEmailJob;

use Modules\Jitsi\Models\EmailBlastLog;

class SendQueuedBlastMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        $nonDispatchedMails = EmailBlastLog::where(["dispatched" => false])->get();
        foreach ($nonDispatchedMails as $mails) {
            # code...
            $identifier = $mails->_id;
            $mailRecipients = $mails->email_recipient;
            $mail_from = $mails->mail_from;
            if ($mail_from > 0) {
                $emailDetails = EmailAccountsMaster::find($mail_from);
                $mail_host = $emailDetails->smtp_server;
                $mail_username = $emailDetails->user_name;
                $mail_password = $emailDetails->password;
                $mail_encryption = $emailDetails->encryption;
                $mail_from_name = $emailDetails->display_name;
                $mail_from_address = $emailDetails->email_address;
                $mail_port = $emailDetails->port;
            } else {

                $mail_host = config("mail.host");
                $mail_username = config("mail.username");
                $mail_password = config("mail.password");
                $mail_encryption = config("mail.encryption");
                $mail_from_name = config("mail.from.name");
                $mail_from_address = config("mail.from.address");
                $mail_port = config("mail.port");
            }
            $details = [
                "name" => "",
                "mail_host" => $mail_host,
                "mail_username" => $mail_username,
                "mail_password" => $mail_password,
                "mail_encryption" => $mail_encryption,
                "mail_from_name" => $mail_from_name,
                "mail_from_name" => $mail_from_name,
                "mail_port" => $mail_port,
                "mail_from_address" => $mail_from_address,
                "identifier" => $identifier
            ];
            $mailSubject = $mails->subject;
            $html = \View::make('emails.sendBlastMail', [
                'mailMessage' => $mails->message
            ]);
            // $html = $mailMessage;

            $html = $html->render();
            $start = \Carbon::now()->addSeconds(BLASTMAIL_START);

            foreach ($mailRecipients as $key => $usr) {

                # code...
                $userArray = $usr;
                $userDetail = User::find($userArray["user_id"]);
                $details["user"] = $userDetail->id;
                $job = new SendBlastEmailJob(
                    $details,
                    $userDetail->email,
                    $mailSubject,
                    $html
                );
                $start = $start->addSeconds(BLASTMAIL_LOOP);
                $job->delay($start);
                dispatch($job);
            }
            $emailBlast = EmailBlastLog::find($identifier);
            $emailBlast->dispatched = true;
            $emailBlast->save();
        }
    }
}
