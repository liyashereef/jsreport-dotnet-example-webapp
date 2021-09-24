<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTemplateUseridMapping extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['template_email_id','user_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

     public function templateSettings()
    {
        return $this->belongsTo('Modules\Admin\Models\CustomerTemplateEmail', 'template_email_id', 'id');
    }

    public function userDetails()
    {
         return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }

}
