<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseSettingsFinanceControllers extends Model
{
    use SoftDeletes;
    protected $table   = 'expense_settings_finance_controllers';
    public $timestamps = true;
    protected $fillable = ['financial_controller'];
     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
