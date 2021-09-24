<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfpTrackingStage extends Model
{
    use SoftDeletes;
    protected $fillable = ['rfp_details_id','rfp_process_steps_id','completion_date','notes','entered_by_id'];
    protected $dates = ['deleted_at'];
    public function tracking_process()
    {
        return $this->belongsTo('Modules\Admin\Models\RfpProcessStepLookups', 'rfp_process_steps_id', 'id');
    }
    public function entered_by()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'entered_by_id', 'id')->withTrashed();
    }
}
