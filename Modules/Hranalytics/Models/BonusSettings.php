<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusSettings extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "start_date",
        "end_date",
        "bonus_amount",
        "wagecap_percentage",
        "shiftcap_percentage",
        "noticecap_percentage",
        "created_by",
        "updated_by",
        "active"
    ];
}
