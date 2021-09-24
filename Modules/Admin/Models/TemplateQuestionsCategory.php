<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateQuestionsCategory extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'description', 'average', 'safety_type',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation to template Form
     *
     * @return type
     */
    public function templateForm()
    {
        return $this->hasMany('Modules\Admin\Models\TemplateForm', 'question_category_id', 'id');
    }

    public function templateFormReports()
    {
        return $this->hasMany('Modules\Admin\Models\TemplateForm', 'question_category_id', 'id')->has('customerReport');
    }

}
