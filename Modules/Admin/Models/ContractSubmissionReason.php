<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ContractSubmissionReason extends Model
{
    //
    use SoftDeletes;

    public $table = 'contract_submission_reasons';
    protected $fillable = ['reason','sequence','status','createdby'];
    protected $dates = ['deleted_at'];
}
