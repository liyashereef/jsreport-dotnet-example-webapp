<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecUniformItems extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_uniform_items';
    public $timestamps = true;
    protected $fillable = ['item_name'];
    public function uniformItemMeasurementMapping()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecUniformItemSizeMeasurementMapping', 'item_name_id', 'id');
    }
}
