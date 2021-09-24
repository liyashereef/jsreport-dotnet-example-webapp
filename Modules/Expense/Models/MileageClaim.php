<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MileageClaim extends Model
{
    use SoftDeletes;

    protected $table   = 'mileage_claims';

    protected $dates = ['date'];

    protected $fillable = [
        'date', 'amount', 'starting_location', 'destination','starting_km', 'ending_km','total_km', 'status_id'
        , 'approved_by', 'financial_controller_id', 'approver_comments', 'finance_comments',
        'created_by', 'vehicle_type', 'description', 'associate_with_client', 'project_id',
'claim_reimbursement','billable', 'vehicle_id'
    ];


    public function created_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'project_id', 'id')->withTrashed();
    }

    public function expenseAllowable()
    {
        return $this->belongsTo('Modules\Expense\Models\ExpenseAllowableForUser', 'created_by', 'user_id')->withTrashed();
    }
    public function status()
    {
        return $this->belongsTo('Modules\Expense\Models\ExpenseStatus', 'status_id', 'id');
    }
    public function approved_by_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'approved_by', 'id')->withTrashed();
    }
    
    public function finance_controller()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'financial_controller_id', 'id')->withTrashed();
    }

    public function vehicle()
    {
        return $this->belongsTo('Modules\Vehicle\Models\Vehicle', 'vehicle_id', 'id')->withTrashed();
    }
    
}