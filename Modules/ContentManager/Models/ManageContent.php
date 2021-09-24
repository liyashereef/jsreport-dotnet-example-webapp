<?php

namespace Modules\ContentManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageContent extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'key',
        'video',
        'attachment',
        'expiry_date'
    ];

    public function ContentAttachments()
    {
        return $this->hasMany("Modules\ContentManager\Models\ContentAttachments", "content_id");
    }
}
