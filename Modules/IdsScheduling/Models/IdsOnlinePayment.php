<?php

namespace Modules\IdsScheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsOnlinePayment extends Model
{
    use SoftDeletes;
    protected $fillable = ['entry_id','amount','transaction_id','status',
    'started_time','end_time','payment_intent','balance_transaction_id','email',
    'entry_id_updated_at','deleted_at'];

    public function idsEntries(){
        return $this->belongsTo('Modules\IdsScheduling\Models\IdsEntries', 'entry_id')->withTrashed();
    }
}
