<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\IdsScheduling\Models\IdsEntries;

class IdsCustomQuestionAnswer extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'ids_entry_id',
        'ids_custom_questions_id',
        'ids_custom_questions_str',
        'ids_custom_option_id',
        'ids_custom_option_str',
        'other_value',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function idsEntry()
    {
        return $this->belongsTo(IdsEntries::class,'ids_entry_id');
    }

    public function idsEntryTrashed()
    {
        return $this->belongsTo(IdsEntries::class,'ids_entry_id')->withTrashed();
    }

    public function idsCustomQuestions()
    {
        return $this->belongsTo(UniformSchedulingCustomQuestions::class,'ids_custom_questions_id');
    }

    public function idsCustomQuestionsTrashed()
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
