<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCustomerUniformKit extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_customer_uniform_kits';
    public $timestamps = true;
    protected $fillable = ['customer_id','kit_name'];

    public function customerUniformKitMappings()
    {

        return $this->hasMany('Modules\Recruitment\Models\RecCustomerUniformKitMapping', 'kit_id', 'id');
    }
    public function customer()
    {

        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }
}
