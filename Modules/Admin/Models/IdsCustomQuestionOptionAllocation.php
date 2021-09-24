<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsCustomQuestionOptionAllocation extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'ids_custom_questions_id',
        'ids_custom_option_id',
        'Ids_option_sort_order',
        'other_value',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function idsCustomQuestions()
    {
        return $this->belongsTo(IdsCustomQuestion::class,'ids_custom_questions_id');
    }

    public function idsCustom_questionsTrashed()
    {
        return $this->belongsTo(IdsCustomQuestion::class,'ids_custom_questions_id')->withTrashed();
    }

    public function idsCustomOption()
    {
        return $this->belongsTo(IdsCustomQuestionOption::class,'ids_custom_option_id');
    }

    public function idsCustomOptionTrashed()
    {
        return $this->belongsTo(IdsCustomQuestionOption::class,'ids_custom_option_id')->withTrashed();
    }
}
