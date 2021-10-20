<?php

namespace Modules\IdsScheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsOnlineRefund extends Model
{
    use SoftDeletes;
    protected $fillable = ['entry_id','ids_online_refund_id','ids_online_payment_id','amount',
    'ids_online_charge_id','user_id','refund_status','refund_start_time','refund_end_time','balance_transaction_id'];

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id')->withTrashed();
    }
    public function idsOnlinePayment()
    {
        return $this->belongsTo('Modules\IdsScheduling\Models\IdsOnlinePayment', 'ids_online_payment_id')->where('status', 1);
    }
    public function idsTransactionHistory()
    {
        return $this->hasOne('Modules\IdsScheduling\Models\IdsTransactionHistory', 'online_refund_id');
    }
}
