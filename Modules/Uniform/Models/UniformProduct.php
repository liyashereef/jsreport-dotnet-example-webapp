<?php

namespace Modules\Uniform\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Expense\Models\ExpenseTaxMasterLog;

class UniformProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'selling_price',
        'vendor_price',
        'tax_id',
        'image_path',
        'created_by',
        'updated_by',
    ];

    public function variants()
    {
        return $this->hasMany(UniformProductVariant::class, 'uniform_product_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(UniformProductImage::class, 'uniform_product_id', 'id');
    }

    public function taxMasterLog(){
        return $this->belongsTo(ExpenseTaxMasterLog::class,'tax_id')->withTrashed();
    }
}
