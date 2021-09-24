<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecProcessTab extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_process_tabs';
    public $timestamps = true;
    protected $fillable = ['system_name','display_name','order','instructions','detailed_help'];
}
