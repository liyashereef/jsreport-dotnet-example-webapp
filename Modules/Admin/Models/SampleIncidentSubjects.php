<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class SampleIncidentSubjects extends Model
{
    use SoftDeletes;
    protected $fillable = ['subject_id', 'subject', 'category_id', 'priority_id', 'incident_response_time', 'sop'];

  
}