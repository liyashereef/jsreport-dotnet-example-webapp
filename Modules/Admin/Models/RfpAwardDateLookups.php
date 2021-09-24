<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfpAwardDateLookups extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $table  = 'rfp_award_dates';
    protected $fillable = ['id','award_dates'];
}
