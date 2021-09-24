<?php

namespace Modules\Uniform\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'variant_name',
        'uniform_product_id',
        'created_by	',
        'updated_by	',
    ];
}
