<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLogTypeLookup extends Model
{

    public $timestamps = true;

    protected $fillable = ['type'];

}
