<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingCourse extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['reference_code', 'training_category_id', 'course_title', 'course_description', 'course_objectives', 'course_file', 'course_external_url', 'status'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation to training category table
     * @return type
     */
    public function training_category()
    {
        return $this->belongsTo('Modules\Admin\Models\TrainingCategory', 'training_category_id', 'id');
    }
}
