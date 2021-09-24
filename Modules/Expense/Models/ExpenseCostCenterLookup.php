<?php

namespace Modules\Expense\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ExpenseCostCenterLookup extends Model
{
    use SoftDeletes;

    protected $table = 'expense_cost_center_lookups';

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['id','center_number','center_owner_id','center_senior_manager_id','region_id','description'];

    /**
     * The Document Type that belongs to document name detail
     *
     */
    public function centerOwners()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'center_owner_id', 'id')->withTrashed();
    }

    /**
     * The Document Type that belongs to document name detail
     *
     */
    public function seniorMangers()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'center_senior_manager_id', 'id')->withTrashed();
    }
    /**
     * The Document Type that belongs to document name detail
     *
     */
    public function regions()
    {
        return $this->belongsTo('Modules\Admin\Models\RegionLookup', 'region_id', 'id')->withTrashed();
    }
    
}
