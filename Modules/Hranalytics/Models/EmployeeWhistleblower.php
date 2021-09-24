<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeWhistleblower extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = [
        'employee_id',
        'customer_id',
        'whistleblower_subject',
        'whistleblower_category_id',
        'whistleblower_priority_id',
        'policy_id',
        'geo_location_lat',
        'geo_location_long',
        'whistleblower_documentation',
        'created_by',
        'status',
        'reg_manager_notes'
    ];

    public function user(){

        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id')->withTrashed();

    }
    public function customer(){

        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();

    }
    public function whistleblowerStatusLookup(){

        return $this->belongsTo('Modules\Admin\Models\WhistleblowerStatusLookup', 'status', 'id')->withTrashed();

    }
    public function whsitleblowerCategories(){

        return $this->belongsTo('Modules\Admin\Models\EmployeeWhistleblowerCategories', 'whistleblower_category_id', 'id')->withTrashed();

    }
    public function whsitleblowerPriorites(){

        return $this->belongsTo('Modules\Admin\Models\EmployeeWhistleblowerPriorities', 'whistleblower_priority_id', 'id')->withTrashed();

    }

    public function policy(){

        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingPolicies', 'policy_id', 'id');

    }

    public function createdby(){

        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();

    }
}
