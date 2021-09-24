<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformSchedulingCustomQuestion extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'question',
        'display_order',
        'is_required',
        'has_other',
        'is_active',
        'deactivated_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function uniformSchedulingCustomQuestionAnswer()
    {
        return $this->hasMany(
            'Modules\Admin\Models\UniformSchedulingCustomQuestionAnswer',
            'uniform_scheduling_custom_question_id', 'id'
        );
    }

    public function uniformSchedulingCustomQuestionOptionAllocation()
    {
        return $this->hasMany(
            'Modules\Admin\Models\UniformSchedulingCustomQuestionOptionAllocation',
            'uniform_scheduling_custom_question_id', 'id'
        );
    }
}
