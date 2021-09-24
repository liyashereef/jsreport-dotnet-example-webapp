<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseStatus extends Model
{

    protected $table   = 'expense_status';

    protected $dates = ['date'];

    protected $fillable = ['status'];

}