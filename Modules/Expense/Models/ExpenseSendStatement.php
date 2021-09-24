<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ExpenseSendStatement extends Model
{
    use SoftDeletes;

    protected $table = 'expense_send_statements';
    protected $fillable = ['id','user_id','financial_controller_id','attachment_id'];
    //protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->with('employee');
    }
    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }
}
   