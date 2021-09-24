<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class MailQueueMultipleAttachments extends Model
{
    protected $fillable = [
        "mail_queue_id","attachment_id","s3_bucket_name", "s3_repo_filename"
    ];
}
