<?php

namespace Modules\Uniform\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformProductImage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uniform_product_id',
        'image_path',
        'created_by',
        'updated_by',
    ];
}
