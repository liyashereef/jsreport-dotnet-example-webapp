<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusFinalizedData extends Model
{
    protected $table = "bonus_finalized_data";
    use SoftDeletes;
    protected $fillable = [
        "bonus_pool_id",
        "user_id",
        "no_of_shifts_taken",
        "no_of_calls_made",
        "average_wage",
        "average_wage_gross_up",
        "average_notice",
        "average_notice_gross_up",
        "reliability_score",
        "total_adjustment",
        "adjusted_bonus",
        "unadjusted_bonus",
        "rank",
        "created_by"
    ];

    /**
     * The user that belongs to employee allocation
     *
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }
}
