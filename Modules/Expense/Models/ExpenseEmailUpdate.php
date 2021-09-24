<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseEmailUpdate extends Model
{
     use SoftDeletes;
    protected $table   = 'expense_email_updates';
    public $timestamps = true;
    protected $fillable = ['interval'];
     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
