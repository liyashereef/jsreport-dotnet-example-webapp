<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLogMeta extends Model
{
    protected $fillable = ['visitor_log_id', 'key', 'value'];

    public function visitorLog()
    {
        return $this->belongsTo(VisitorLogDetails::class, 'visitor_log_id');
    }

    public function getFormattedKeyAttribute()
    {
        return  str_replace('_', ' ', ucwords($this->key, '_'));
    }
}
