<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostOrder extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'topic_id',
        'group_id',
        'customer_id',
        'description',
        'attachment_id',
        'reviewed_status',
        'reviewed_by',
        'reviewed_at',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function topicDetails()
    {

        return $this->belongsTo('Modules\Admin\Models\PostOrderTopic', 'topic_id', 'id');
    
    }

    public function groupDetails()
    {

        return $this->belongsTo('Modules\Admin\Models\PostOrderGroup', 'group_id', 'id');
    }
     public function customerDetails()
    {

        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

     public function getCreatedby(){
        return $this->belongsTo('Modules\Admin\Models\User','created_by','id')->withTrashed();
    }
    public function getReviewedby(){
        return $this->belongsTo('Modules\Admin\Models\User','reviewed_by','id')->withTrashed();
    }

     public function attachmentDetails()
    {

        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id')->select('id','original_name');
    }

}
