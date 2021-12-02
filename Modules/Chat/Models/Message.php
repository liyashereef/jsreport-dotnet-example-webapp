<?php

namespace Modules\Chat\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from','to','read','text','type'];

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User','user_id','id');
    }
    public function fromContact()
    {
        return $this->hasOne('Modules\Admin\Models\User', 'id', 'from');
    }
   
}
