<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientContactInformation extends Model
{
    use SoftDeletes;
    protected $table = "client_contact_information";
    protected $fillable = ['primary_contact', 'contact_name', 'contact_jobtitle', 'contact_emailaddress', 'contact_phoneno', 'contact_cellno', 'contact_faxno', 'contractid'];
    protected $dates = ['deleted_at'];

    public function positiontitle()
    {
        return $this->hasOne('Modules\Admin\Models\PositionLookup', 'id', 'contact_jobtitle')->withTrashed();
    }

    public function users()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'primary_contact', 'id')->with('employee')->withTrashed();
    }
}
