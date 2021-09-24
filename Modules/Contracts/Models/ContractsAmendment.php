<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class ContractsAmendment extends Model
{
    use SoftDeletes;
    protected $fillable = ['contract_id','amendment_description','amendment_attachment_id','created_by'];
    protected $dates = ['deleted_at'];

    public function savecontractamendment($data){
        $this->contract_id = $data["contract_id"];
        $this->amendment_description=$data["amendment_description"];
        $this->amendment_attachment_id = $data["amendment_attachment_id"];
        $this->created_by = Auth::user()->id;
        $this->save();
    }

    public function getCreateduser(){
        return $this->hasOne('Modules\Admin\Models\User','id','created_by')->withTrashed();
    }
}
