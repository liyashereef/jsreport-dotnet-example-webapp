<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegisterCourse extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['course_id', 'employee_id', 'status'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

}
