<?php

namespace Modules\Uniform\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Expense\Models\ExpenseTaxMasterLog;

class UniformOrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uniform_order_id',
        'item_price',
        'quantity',
        'tax_id',
        'tax_rate',
        'tax_amount',
        'uniform_product_id',
        'uniform_product_variant_id',
        'total_price_with_tax',
        'unit_price',
    ];

    public function product()
    {
        return $this->belongsTo(UniformProduct::class, 'uniform_product_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(UniformProductVariant::class, 'uniform_product_variant_id')->withTrashed();
    }

    public function taxMasterLog(){
        return $this->belongsTo(ExpenseTaxMasterLog::class,'tax_id')->withTrashed();
    }
}
