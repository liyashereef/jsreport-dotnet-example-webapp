<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{

    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    use HasApiTokens;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'alternate_email', 'username', 'password', 'role_id', 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Relationship: employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function employee()
    {
        return $this->hasOne('Modules\Admin\Models\Employee', 'user_id'); //
    }

    /**
     * Relationship: trashedEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     *
     */
    public function trashedEmployee()
    {
        return $this->hasOne('Modules\Admin\Models\Employee', 'user_id')->withTrashed();
    }

    /**
     * Relationship: employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function trainingUserCourseAllocation()
    {
        return $this->hasMany('Modules\LearningAndTraining\Models\TrainingUserCourseAllocation', 'user_id'); //
    }
    /**
     * Relationship: employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function trainingUserTeam()
    {
        return $this->hasMany('Modules\LearningAndTraining\Models\TrainingUserTeam', 'user_id'); //
    }

    /**
     * Get full name of user
     *
     * @return void
     */
    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    public function getNameWithEmpNoAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name) . ' (' . $this->trashedEmployee()->first()->employee_no . ')';
    }




}
