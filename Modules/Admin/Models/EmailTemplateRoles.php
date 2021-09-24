<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplateRoles extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['email_template_id','role_id'];

    public function roleName()
    {
         return $this->belongsTo('Spatie\Permission\Models\Role', 'role_id', 'id');
    }

}
