<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Days extends Model
{
    protected $fillable = ['name'];

    public function getTodayIdAttribute() {
        return date("N", strtotime("now"));
    }
}
