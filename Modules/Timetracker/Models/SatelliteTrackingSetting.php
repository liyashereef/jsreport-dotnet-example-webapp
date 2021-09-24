<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SatelliteTrackingSetting extends Model
{
    use SoftDeletes;

    const RANGE_MAX = 100;
    const RANGE_MIN = 0;

    protected $fillable = [
        'color',
        'min',
        'max',
    ];

    public static function colors(){
        return [
            1 => "green",
            2 => "yellow",
            3 => "red",
        ];
    }
}
