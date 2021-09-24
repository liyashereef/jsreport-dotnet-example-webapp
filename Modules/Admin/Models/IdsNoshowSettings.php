<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsNoshowSettings extends Model
{
    use SoftDeletes;
    protected $fillable = ['notice_hours','cancellation_penalty','is_active','created_by','updated_by'];
}
