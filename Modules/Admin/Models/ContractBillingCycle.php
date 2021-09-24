<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ContractBillingCycle extends Model
{
    use SoftDeletes;

    public $table = 'contract_billing_cycles';
    protected $fillable = ['title','sequence','status','createdby'];
    protected $dates = ['deleted_at'];
}
