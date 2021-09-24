<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class JobTicketSetting extends Model
{
    use SoftDeletes;
    protected $fillable = ['setting', 'value'];
}
