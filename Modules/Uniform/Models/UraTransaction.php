<?php

namespace Modules\Uniform\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\User;

class UraTransaction extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'created_by',
        'employee_shift_payperiod_id',
        'employee_shift_report_entry_id',
        'uniform_order_id',
        'transaction_type',
        'ura_operation_id',
        'ura_rate_id',
        'revoked',
        'hours',
        'amount',
        'balance',
        'notes'
    ];

    public function uraRate()
    {
        return $this->belongsTo(UraRate::class, 'ura_rate_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();;
    }

    public function operationType()
    {
        return $this->belongsTo(UraOperationType::class, 'ura_operation_id');
    }
}
