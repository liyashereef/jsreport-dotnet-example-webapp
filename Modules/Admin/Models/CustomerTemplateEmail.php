<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTemplateEmail extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['template_id','customer_id','send_to_areamanagers','send_to_supervisors','role_based'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function usersIdMapping()
    {
        return $this->hasMany('Modules\Admin\Models\CustomerTemplateUseridMapping', 'template_email_id', 'id');
    }

    public function roleIdMapping()
    {
        return $this->hasMany('Modules\Admin\Models\EmailTemplateRoles', 'email_template_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }
    public function type()
    {
        return $this->belongsTo('Modules\Admin\Models\EmailNotificationType', 'template_id', 'id');
    }
}
