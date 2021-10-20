<?php

namespace Modules\IdsScheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsTransactionHistory extends Model
{
    use SoftDeletes;
    protected $fillable = ['entry_id','ids_payment_method_id','ids_online_payment_id','online_refund_id','amount',
    'transaction_type','user_id','refund_status','refund_note'];

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id')->withTrashed();
    }
    public function idsPaymentMethod()
    {
        return $this->belongsTo('Modules\IdsScheduling\Models\IdsPaymentMethods', 'ids_payment_method_id')->withTrashed();
    }
    public function refund()
    {
        return $this->belongsTo('Modules\IdsScheduling\Models\IdsOnlineRefund', 'online_refund_id');
    }
}
