<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeWhistleblowerLogs extends Model
{
    use SoftDeletes;
    protected $fillable = ["whistle_blower_id", 'status_id', 'created_by'];

    public function createdby()
    {

        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }
}
