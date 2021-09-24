<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateForm extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'template_id', 'position', 'question_category_id', 'parent_position',
        'question_text', 'answer_type_id', 'multi_answer', 'show_if_yes',
        'score_yes', 'score_no', 'created_by', 'updated_by',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation to template
     *
     * @return type
     */
    public function template()
    {
        return $this->belongsTo('Modules\Admin\Models\Template');
    }

    /**
     * Relation to Question Category
     *
     * @return type
     */
    public function questionCategory()
    {
        return $this->belongsTo('Modules\Admin\Models\TemplateQuestionsCategory', 'question_category_id', 'id')->withTrashed();
    }

    /**
     * Relation to Answer Type
     *
     * @return type
     */
    public function answerType()
    {
        return $this->belongsTo('Modules\Admin\Models\AnswerTypeLookup', 'answer_type_id', 'id');
    }


    public function customerReport()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\CustomerReport', 'element_id', 'id');
    }
}
