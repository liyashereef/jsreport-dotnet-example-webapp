<?php

namespace Modules\Osgc\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourseMail extends Mailable
{

    use Queueable,
        SerializesModels;

    public $template, $model,$fileDetails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($model, $template,$subject,$params,$fileDetails)
    {
        $this->model = $model;
        $this->template = $template;
        $this->fileDetails = $fileDetails; 
        $this->params = $params; 
        $this->subject = $subject; 
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $result=$this->subject($this->subject)->markdown('osgc::' . $this->template)->with(['model' => $this->model,'params'=>$this->params]);
        if($this->fileDetails)
        {
            $file=base64_encode($this->fileDetails);
            $result=$result->attachData(base64_decode($file), "CourseCertificate.pdf", [
                'mime' => 'application/pdf',
            ]);
            
        }
        
    }

}
