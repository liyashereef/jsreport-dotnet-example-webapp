<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseGlCode extends Model
{
     use SoftDeletes;
    protected $table   = 'expense_gl_codes';
    public $timestamps = true;
    protected $fillable = ['gl_code', 'short_name', 'description', 'grouping', 'pnl_subcode', 'pnl_item'];
     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
