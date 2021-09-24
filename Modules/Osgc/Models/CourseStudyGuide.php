<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CourseStudyGuide extends Model
{
    use SoftDeletes;
    protected $table='osgc_study_guide';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['course_section_id','file_name'];
    
}
