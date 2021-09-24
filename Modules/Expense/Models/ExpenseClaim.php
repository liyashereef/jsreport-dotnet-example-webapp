<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseClaim extends Model
{
    use SoftDeletes;

    protected $table   = 'expense_claims';

    protected $dates = ['date'];

    protected $fillable = [
        'date', 'amount', 'attachment_id', 'expense_category_id','cost_center_id', 'reimbursed', 'status_id'
        , 'approved_by', 'financial_controller_id', 'approver_comments', 'finance_comments',
        'created_by', 'expense_gl_codes_id', 'description', 'no_attachment_reason', 'project_id','payment_mode_id',
    'claim_reimbursement','participants','attachment','billable','tax_percentage','tax_amount','tip'
    ];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'project_id', 'id')->withTrashed();
    }
    public function expenseGlCode()
    {
        return $this->belongsTo('Modules\Expense\Models\ExpenseGlCode', 'expense_gl_codes_id', 'id')->withTrashed();
    }

    public function expenseCategory()
    {
        return $this->belongsTo('Modules\Expense\Models\ExpenseCategoryLookup', 'expense_category_id', 'id')->withTrashed();
    }

    public function expenseAllowable()
    {
        return $this->belongsTo('Modules\Expense\Models\ExpenseAllowableForUser', 'created_by', 'user_id')->withTrashed();
    }

    public function attachmentDetails()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }
    public function created_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }
    public function approved_by_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'approved_by', 'id')->withTrashed();
    }
    public function finance_controller()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'financial_controller_id', 'id')->withTrashed();
    }
    public function status()
    {
        return $this->belongsTo('Modules\Expense\Models\ExpenseStatus', 'status_id', 'id');
    }
    public function mode_of_payment()
    {
        return $this->belongsTo('Modules\Expense\Models\ExpensePaymentMode', 'payment_mode_id', 'id')->withTrashed();
    }
    public function cost_center()
    {
        return $this->belongsTo('Modules\Expense\Models\ExpenseCostCenterLookup', 'cost_center_id', 'id')->withTrashed();
    }
}
