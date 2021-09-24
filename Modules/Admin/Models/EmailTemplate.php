<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use SoftDeletes;
    protected $fillable = ['type_id','email_subject','email_body'];
    protected $dates = ['deleted_at'];

     public function templates()
    {
        return $this->hasOne('Modules\Admin\Models\CustomerTemplateEmail', 'template_id', 'id');
    }
}
