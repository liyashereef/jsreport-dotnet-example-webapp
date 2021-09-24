<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapacityToolTaskFrequencyLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['value','sequence_number'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function answerable()
    {
        //morphMany(string $related, string $name, string $type = null, string $id = null, string $localKey = null)
        return $this->morphMany('Modules\CapacityTool\Models\CapacityTool', 'answer','answer_type','answer','id');
    }    
}
