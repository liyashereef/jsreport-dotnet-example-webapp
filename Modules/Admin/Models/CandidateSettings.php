<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateSettings extends Model
{
    public $timestamps = true;

    protected $fillable = ['generic_password', 'encrypted_password'];
}