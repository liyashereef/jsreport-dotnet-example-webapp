<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecMyersBriggsIndicator extends Model
{
    protected $connection = 'mysql_rec';
    protected $table = 'rec_myers_briggs_indicators';
    protected $fillable = [];
}
