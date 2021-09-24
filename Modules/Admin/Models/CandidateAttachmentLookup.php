<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateAttachmentLookup extends Model
{
    //
    public $timestamps = true;

    protected $fillable = ['attachment_name', 'job_id'];
}
