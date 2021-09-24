<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseMileageReimbursementSlabRate extends Model
{
    use SoftDeletes;
    public $table = 'expense_mileage_reimbursement_slab_rates';
    public $timestamps = true;

    protected $fillable = ['starting_kilometer','ending_kilometer','cost'];
    protected $dates = ['deleted_at'];

}
