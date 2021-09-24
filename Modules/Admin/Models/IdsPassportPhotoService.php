<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsPassportPhotoService extends Model
{
    use SoftDeletes;
    protected $appends = ['name_rate'];
    protected $fillable = ['name','rate','description'];

    /**
     * Get full name of user
     *
     * @return void
     */
    public function getNameRateAttribute()
    {
        return ucfirst($this->name) . ' - ' .'$'.$this->rate;
    }


}

