<?php
namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IdsNoshowSettings;

class IdsNoshowSettingsRepository
{
    protected $model;

    /**
     * Create a new instance.
     *
     * @param IdsNoshowSettings $idsNoshowSettings.
     */
    public function __construct(IdsNoshowSettings $idsNoshowSettings){
        $this->model = $idsNoshowSettings;
    }

     /**
     * Store a newly created offfice in storage.
     *
     * @param  $request
     * @return object
     */

    public function store($inputs){
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    public function getLatest(){
        return $this->model
        ->orderBy('id','DESC')
        ->select("id","notice_hours","cancellation_penalty","is_active")
        ->first();
    }


}
