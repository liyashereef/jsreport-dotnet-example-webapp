<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class OfficeAddress extends Model
{
    use SoftDeletes;
    //
    public $table = 'office_addresses';
    protected $fillable = ['addresstitle','address','status','createdby'];
    protected $dates = ['deleted_at'];
}
