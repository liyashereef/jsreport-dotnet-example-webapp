<?php


namespace Modules\Admin\Repositories;

use  Modules\Admin\Models\IdsCustomQuestion;
use  Modules\Admin\Repositories\IdsCustomQuestionOptionRepository;
use  Modules\Admin\Repositories\IdsCustomQuestionOptionAllocationRepository;

class IdsCustomQuestionRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new IdsCustomQuestionRepository instance.
     *
     * @param  Modules\Admin\Models\IdsCustomQuestion $idsCustomQuestion
     */
    public function __construct(IdsCustomQuestion $idsCustomQuestion, IdsCustomQuestionOptionRepository $idsCustomQuestionOptionRepository, IdsCustomQuestionOptionAllocationRepository $idsCustomQuestionOptionAllocationRepository)
    {
        $this->model = $idsCustomQuestion;
        $this->idsCustomQuestionOptionRepository = $idsCustomQuestionOptionRepository;
        $this->idsCustomQuestionOptionAllocationRepository = $idsCustomQuestionOptionAllocationRepository;
    }


    /**
     * Get list
     *
     * @param empty
     * @return array
     */
    public function getDataList()
    {
        return $this->model
            ->orderby('display_order', 'asc')
            ->orderby('question', 'asc')
            ->with('IdsCustomQuestionAllocation.idsCustomOption')
            ->get();
    }
    /**
     * Get list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model
            ->orderby('display_order', 'asc')
            ->orderby('question', 'asc')
            ->pluck('question', 'id')->where("is_active", true)
            ->toArray();
    }

    /**
     * Get list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model
            ->orderby('display_order', 'asc')
            ->orderby('question', 'asc')
            ->where("is_active", true)
            ->get();
    }

    public function getAllWithOptions()
    {
        return $this->model
            ->with('IdsCustomQuestionAllocation.idsCustomOption')
            ->orderby('display_order', 'asc')
            ->orderby('question', 'asc')
            ->where("is_active", true)
            ->get();
    }

    /**
     * Display details of single
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('IdsCustomQuestionAllocation.idsCustomOption')
            ->with(array('IdsCustomQuestionAllocation' => function ($query) {
                $query->where('ids_custom_option_id', '!=', 1);
            }))->find($id);
    }

    /**
     * Display details of active single
     *
     * @param $id
     * @return object
     */
    public function getActive($id)
    {
        return $this->model->where("is_active", true)->find($id);
    }

    /**
     * Store a newly created in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {

        $data['has_other'] = isset($data['has_other']) ? 1 : 0;
        $data['is_required'] = isset($data['is_required']) ? 1 : 0;
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteQuestion($id)
    {
        return $this->model->destroy($id);
    }

    public function deleteRelatedRecords($id)
    {
        $details = $this->get($id);
        $allocation_details = data_get($details, 'IdsCustomQuestionAllocation.*');
        foreach ($allocation_details as $key => $each_allocation) {
            if ($each_allocation->ids_custom_option_id != \Config::get('globals.idsCustomQuestionOther')) {
                $this->idsCustomQuestionOptionRepository->delete($each_allocation->ids_custom_option_id);
            }
            $this->idsCustomQuestionOptionAllocationRepository->delete($each_allocation->id);
        }
        return true;
    }

    public function getAllAnaswerd()
    {
        return $this->model
            ->orderby('display_order', 'asc')
            ->orderby('question', 'asc')
            // ->where("is_active",true)
            ->where("id", 10)
            ->whereHas('IdsCustomQuestionAnswers')
            ->get();
    }
}
