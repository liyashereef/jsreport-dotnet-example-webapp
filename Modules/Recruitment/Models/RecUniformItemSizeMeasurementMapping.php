<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecUniformItemSizeMeasurementMapping extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_uniform_item_size_measurement_mappings';
    public $timestamps = true;
    protected $fillable = ['item_name_id', 'size_name_id', 'measurement_name_id', 'min', 'max'];

    public function uniformItem()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecUniformItems', 'item_name_id', 'id');
    }

    public function uniformSize()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecUniformSizes', 'size_name_id', 'id');
    }

    public function uniformMeasurementPoint()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecUniformMeasurementPoint', 'measurement_name_id', 'id');
    }
}
