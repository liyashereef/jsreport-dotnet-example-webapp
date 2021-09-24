<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformSchedulingCustomQuestionOption extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'custom_question_option',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function UniformSchedulingCustomQuestionAnswer()
    {
        return $this->hasMany(
            'Modules\Admin\Models\UniformSchedulingCustomQuestionAnswer',
            'uniform_scheduling_custom_option_id', 'id'
        );
    }

}
