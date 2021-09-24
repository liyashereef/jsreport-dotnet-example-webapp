<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSalutations extends Model
{
    use SoftDeletes;
    protected $fillable = ['salutation', 'created_by','updated_by'];
}
