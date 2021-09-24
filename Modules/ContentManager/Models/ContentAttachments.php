<?php

namespace Modules\ContentManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentAttachments extends Model
{
    use SoftDeletes;
    protected $fillable = ["content_id", "attachment_title", "attachment_type", "attachment_file", "sequence"];
}
