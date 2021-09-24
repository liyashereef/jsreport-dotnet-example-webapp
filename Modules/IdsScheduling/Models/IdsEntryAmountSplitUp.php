<?php

namespace Modules\IdsScheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsEntryAmountSplitUp extends Model
{
    use SoftDeletes;
    protected $fillable = ['type','entry_id','service_id','tax_percentage','rate'];

    public function ids_entries()
    {
        return $this->belongsTo('Modules\IdsScheduling\Models\IdsEntries', 'entry_id')->withTrashed();
    }
}

