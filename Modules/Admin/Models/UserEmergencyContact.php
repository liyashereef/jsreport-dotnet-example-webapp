<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserEmergencyContact extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'name', 'relation_id', 'full_address', 'primary_phoneno', 'alternate_phoneno', 'created_by', 'updated_by'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Relationship: user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id'); //
    }
    /**
     * Relationship: user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function relation()
    {
        return $this->belongsTo('Modules\Admin\Models\UserEmergencyContactRelation', 'relation_id', 'id'); //
    }
}
