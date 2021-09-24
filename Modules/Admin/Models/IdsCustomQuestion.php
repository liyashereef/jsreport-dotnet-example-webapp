<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsCustomQuestion extends Model
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

    public function IdsCustomQuestionAnswers()
    {
        return $this->hasMany(
            'Modules\Admin\Models\IdsCustomQuestionAnswer',
            'ids_custom_questions_id', 'id'
        );
    }

    public function IdsCustomQuestionAllocation()
    {
        return $this->hasMany(
            'Modules\Admin\Models\IdsCustomQuestionOptionAllocation',
            'ids_custom_questions_id', 'id'
        );
    }
}
