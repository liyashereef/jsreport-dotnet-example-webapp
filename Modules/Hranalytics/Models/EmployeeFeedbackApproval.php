<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeFeedbackApproval extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "feedback_id",
        "notes",
        "status",
        "created_by"
    ];



    public function userstatus()
    {
        return $this->belongsTo('Modules\Admin\Models\WhistleblowerStatusLookup', 'status')->withTrashed();
    }

    /**
     * The user that belongs to employee allocation
     *
     */
    public function create_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }
}
