<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardroute extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['routename','description','createdby','status'];
    protected $dates = ['deleted_at'];

    public function getAll(){
        return $this->all();
    }

    public function store($routename,$routedesc,$userid,$status){
        $this->routename = $routename;
        $this->description = $routedesc;
        $this->createdby = $userid;
        $this->status = $status;
        return $this->save();
    }
}
