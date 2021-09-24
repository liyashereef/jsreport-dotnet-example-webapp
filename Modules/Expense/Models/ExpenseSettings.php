<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseSettings extends Model
{
    protected $table   = 'expense_settings';
    public $timestamps = true;
    protected $fillable = ['sent_statement_attachment'];
    protected $dates = ['deleted_at'];

}
