<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategoryLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['name','short_name','is_category_taxable','tax_id','description','is_tip_enabled'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $table = 'expense_category_lookups';

    public function taxMaster()
    {
        return $this->hasOne('Modules\Expense\Models\ExpenseTaxMaster', 'id', 'tax_id')->with('taxMasterLog');
    }
}
