<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientSurvey extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "client_id", "client_contact_user",
        "rating", "notes", "payperiod", "created_by"
    ];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'client_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'client_contact_user', 'id')->withTrashed();
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    public function pay_period()
    {
        return $this->hasOne('Modules\Admin\Models\PayPeriod', 'id', 'payperiod');
    }
}
