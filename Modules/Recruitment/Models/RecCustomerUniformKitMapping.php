<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCustomerUniformKitMapping extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_customer_uniform_kit_mappings';
    public $timestamps = true;
    protected $fillable = ['kit_id','item_id','quantity'];

    public function uniformItems()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecUniformItems', 'item_id', 'id');
    }
}
