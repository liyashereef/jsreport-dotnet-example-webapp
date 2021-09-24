<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class UserCertificateExpirySettings extends Model
{
    protected $fillable = ['settings_type','parameter','value'];
}
