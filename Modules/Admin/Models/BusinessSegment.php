<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BusinessSegment extends Model
{
    use SoftDeletes;

    public $table = 'business_segments';
    protected $fillable = ['segmenttitle','status','createdby'];
    protected $dates = ['deleted_at'];
}
