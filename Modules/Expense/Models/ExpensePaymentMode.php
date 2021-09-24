<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpensePaymentMode extends Model
{
    use SoftDeletes;
    protected $table   = 'expense_payment_modes';
    public $timestamps = true;
    protected $fillable = ['id','mode_of_payment', 'reimbursement'];
    protected $dates = ['deleted_at'];
}
