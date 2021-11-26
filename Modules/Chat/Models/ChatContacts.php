<?php

namespace Modules\Chat\Models;

use Illuminate\Database\Eloquent\Model;

class ChatContacts extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','contact_id'];
    public $timestamps = false;
    public function contact()
    {
        return $this->hasMany('Modules\Admin\Models\User', 'id', 'contact_id');
    }
}
