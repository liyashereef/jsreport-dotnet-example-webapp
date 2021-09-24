<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailNotificationHelper extends Model
{

    /**
     * Relation to template Form
     *
     * @return type
     */
    use SoftDeletes;


    protected $fillable = ['email_notification_type_id','replace_string','replace_value'];
    protected $dates = ['deleted_at'];
    
}
