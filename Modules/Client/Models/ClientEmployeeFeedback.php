<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientEmployeeFeedback extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'client_employee_feedbacks';

    protected $fillable = ['user_id', 'employee_rating_lookup_id', 'client_feedback', 'customer_id', 'created_by', 'updated_by', 'feedback_id','status_lookup_id','reg_manager_notes'];

    /**
     * User relation
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }

    public function whistleblowerStatusLookup(){

        return $this->belongsTo('Modules\Admin\Models\WhistleblowerStatusLookup', 'status_lookup_id', 'id')->withTrashed();

    }

    /**
     * Created by - user relation
     */
    public function createdUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    /**
     * Created by - user relation
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    /**
     * Updated by - user relation
     */
    public function updatedUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'updated_by', 'id')->withTrashed();
    }

    /**
     * User rating lookup relation
     */
    public function userRating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'employee_rating_lookup_id', 'id');
    }

    public function clientFeedbacks()
    {
        return $this->belongsTo('Modules\Admin\Models\ClientFeedbackLookup', 'feedback_id', 'id')->withTrashed();
    }

}
