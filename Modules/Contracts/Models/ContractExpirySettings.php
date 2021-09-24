<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractExpirySettings extends Model
{
    use SoftDeletes;

    protected $fillable = ['alert_period_1','alert_period_2','alert_period_3','email_1_time','email_2_time','email_3_time'];
}
