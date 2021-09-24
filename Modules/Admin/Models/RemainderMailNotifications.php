<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class RemainderMailNotifications extends Model {

    protected $fillable = ['notification_type', 'model', 'document_id', 'user_id', 'expiry_date'];

}
