<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientConcern extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['user_id', 'customer_id', 'severity_id', 'concern', 'created_by', 'updated_by','status_lookup_id','reg_manager_notes'];

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
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    /**
     * Updated by - user relation
     */
    public function severityLevel()
    {
        return $this->belongsTo('Modules\Admin\Models\SeverityLookup', 'severity_id', 'id')->withTrashed();
    }

    /**
     * Created by - user relation
     */
    public function createdUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    /**
     * Updated by - user relation
     */
    public function updatedUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'updated_by', 'id')->withTrashed();
    }
}
