<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDevice extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id','device_type','device_token','description','app_id'];

    public function user(){
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }

}
