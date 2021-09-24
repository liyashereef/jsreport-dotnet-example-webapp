<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class LineOfBusiness extends Model
{
    use SoftDeletes;

    public $table = 'line_of_businesses';
    protected $fillable = ['lineofbusinesstitle','status','createdby'];
    protected $dates = ['deleted_at'];
}
