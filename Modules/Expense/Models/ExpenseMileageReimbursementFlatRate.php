<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseMileageReimbursementFlatRate extends Model
{
    use SoftDeletes;
    public $table = 'expense_mileage_reimbursement_flat_rates';

    public $timestamps = true;
    protected $fillable = ['id','flat_rate','user_id','is_active','created_at'];
    protected $dates = ['deleted_at'];

    
    public function createdBy()
    {
        return $this->hasOne('Modules\Admin\Models\User', 'id', 'user_id')->withTrashed();
    }
}
