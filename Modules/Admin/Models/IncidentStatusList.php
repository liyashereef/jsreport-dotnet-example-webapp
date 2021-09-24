<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentStatusList extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['status'];   
    
    
    public function incidentStatusLog()
    {
        return $this->hasOne('App\Models\IncidentStatusLog');
    }
}
