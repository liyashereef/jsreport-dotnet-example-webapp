<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogScreeningTemplateQuestion extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $table = 'visitor_log_screening_template_questions';
    protected $fillable = ['visitor_log_screening_template_id','question','answer','created_by','updated_by'];

}
