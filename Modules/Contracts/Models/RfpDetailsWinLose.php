<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;

class RfpDetailsWinLose extends Model
{
    protected $fillable = ['rfp_details_id','status','rfp_debrief_attended','rfp_debrief_attended_no',
    'did_we_take_it','did_we_take_it_no','offered_by_the_client_no'];
}
