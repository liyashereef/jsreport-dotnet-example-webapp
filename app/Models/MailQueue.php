<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailQueue extends Model
{
    protected $table = 'mail_queue';

    protected $fillable = [
        'mail_time', 'from', 'to', 'cc', 'bcc', 'subject', 'message', 'created_by', 'send_time',
        'active', 'send_status', 'attachment_id', 'model_name', "s3_bucket_name", "s3_repo_filename",'is_multiple_attachment'
    ];

    public function attachmentDetails()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }
    public function multipleAttachmentDetails()
    {
        return $this->hasMany('Modules\Admin\Models\MailQueueMultipleAttachments', 'mail_queue_id', 'id');
    }
}
