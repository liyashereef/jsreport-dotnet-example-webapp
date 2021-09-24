<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ContractBillingRateChange extends Model
{
    use SoftDeletes;

    public $table = 'contract_billing_rate_changes';
    protected $fillable = ['ratechangetitle','status','createdby'];
    protected $dates = ['deleted_at'];
}
