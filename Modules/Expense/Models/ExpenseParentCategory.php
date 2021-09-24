<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseParentCategory extends Model
{
    use SoftDeletes;
    protected $table   = 'expense_parent_categories';
    public $timestamps = true;
    protected $fillable = ['id','parent_category_name','short_name'];
    protected $dates = ['deleted_at'];
}
