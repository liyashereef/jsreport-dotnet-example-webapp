<?php

namespace Modules\KPI\Models;

use Illuminate\Database\Eloquent\Model;

class KpiCache extends Model
{
    protected $fillable = ['key','value','query'];

    public function getDecodedValueAttribute(){
        return json_decode($this->value,JSON_UNESCAPED_SLASHES);
    }

    public function incrementHit(){
        $this->hit += 1;
        $this->save();
    }
}
