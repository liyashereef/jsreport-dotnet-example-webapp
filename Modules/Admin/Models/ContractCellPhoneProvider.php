<?php

namespace Modules\Admin\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ContractCellPhoneProvider extends Model
{
    use SoftDeletes;

    public $table = 'contract_cell_phone_providers';
    protected $fillable = ['providername','status','createdby'];
    protected $dates = ['deleted_at'];
}
