<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordSetting extends Model
{
    public $timestamps = true;

    protected $fillable = ['generic_password', 'encrypted_password', 'active'];

}
