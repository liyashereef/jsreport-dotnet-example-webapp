<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseAllowableForUser extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id','reporting_to_id','max_allowable_expense'];

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }
    public function reportingUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'reporting_to_id', 'id');
    }
}
