<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerTypeLookup extends Model
{

    /**
     * Relation to template Form
     *
     * @return type
     */
    public function templateForm()
    {
        return $this->hasMany(' Modules\Supervisorpanel\Models\TemplateForm', 'answer_type_id', 'id');
    }
}