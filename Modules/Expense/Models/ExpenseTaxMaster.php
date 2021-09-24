<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseTaxMaster extends Model
{
    use SoftDeletes;

    protected $table    = 'expense_tax_masters';
    protected $fillable = ['name','short_name'];

    public function taxMasterLog()
    {
        return $this->hasOne('Modules\Expense\Models\ExpenseTaxMasterLog','tax_master_id');
    }
}
