<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformSchedulingCustomQuestionOptionAllocation extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = [
        'uniform_scheduling_custom_question_id',
        'uniform_scheduling_custom_option_id',
        'option_sort_order',
        'other_value',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function uniformSchedulingCustomQuestions()
    {
        return $this->belongsTo(UniformSchedulingCustomQuestion::class,'uniform_scheduling_custom_question_id');
    }

    public function uniformSchedulingCustomQuestionsTrashed()
    {
        return $this->belongsTo(UniformSchedulingCustomQuestion::class,'uniform_scheduling_custom_question_id')
        ->withTrashed();
    }

    public function uniformSchedulingCustomQuestionOption()
    {
        return $this->belongsTo(UniformSchedulingCustomQuestionOption::class,'uniform_scheduling_custom_option_id');

    }

    public function uniformSchedulingCustomQuestionOptionTrashed()
    {
        return $this->belongsTo(UniformSchedulingCustomQuestionOption::class,'uniform_scheduling_custom_option_id')->withTrashed();
    }

}
