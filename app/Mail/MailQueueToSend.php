<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MailQueue;
use App\Repositories\AttachmentRepository;
use App\Models\Attachment;

class MailQueueToSend extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $mailQueue, $attchPath, $attchName, $s3BucketName, $s3FileName;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MailQueue $mailQueue,$attchPath, $attchName, $s3BucketName,$s3FileName)
    {
        $this->mailQueue = $mailQueue;
        $this->attchPath = $attchPath;
        $this->attchName = $attchName;
        $this->s3BucketName = $s3BucketName;
        $this->s3FileName = $s3FileName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        if($this->attchPath != ''){
            return $this->subject($this->mailQueue->subject)
                    ->attach($this->attchPath, [
                        'as' => $this->attchName,
                    ])
                    ->markdown('emails.mailQueueToSend')->with([
                        'bodymessage' => $this->mailQueue->message,
                        'bodytitle' => $this->mailQueue->subject
                        ]);
        }
        if($this->s3BucketName != '' && !empty($this->s3FileName)){
            $arr= $this->subject($this->mailQueue->subject);
           $test=$this->s3FileName;
            foreach($this->s3FileName as $file)
            {
                $arr=$arr->attachFromStorageDisk($this->s3BucketName , $file);
            }
            
            
            $arr=$arr->markdown('emails.mailQueueToSend')->with([
                'bodymessage' => $this->mailQueue->message,
                'bodytitle' => $this->mailQueue->subject
                ]);
            return $arr;
        }

            return $this->subject($this->mailQueue->subject)
            ->markdown('emails.mailQueueToSend')->with([
                'bodymessage' => $this->mailQueue->message,
                'bodytitle' => $this->mailQueue->subject
                ]);

    }
}
