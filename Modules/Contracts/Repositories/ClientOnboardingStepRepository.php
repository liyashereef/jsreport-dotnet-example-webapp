<?php

namespace Modules\Contracts\Repositories;

use App\Models\Attachment;
use Modules\Admin\Models\PostOrderGroup;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AttachmentRepository;
use Modules\Contracts\Models\ClientOnboarding;
use Modules\Contracts\Models\ClientOnboardingStep;
use Modules\Contracts\Models\PostOrder;

class ClientOnboardingStepRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $attachment_repository;

    /**
     * Create a new instance.
     *
     * @param ClientOnboarding $clientOnboarding
     */
    public function __construct(
        ClientOnboardingStep $clientOnboardingStep
    )
    {
        $this->model = $clientOnboardingStep;
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
            ->orderby('sort_order','asc')
            ->pluck('step','id')->toArray();
    }

    public function getStepListBySection($section_id)
    {
        return $this->model
            ->where('section',$section_id)
            ->orderby('sort_order','asc')
            ->pluck('step','id')->toArray();
    }

//    /**
//     * Get list
//     *
//     * @param empty
//     * @return array
//     */
//    public function getAll()
//    {
//        return $this->model
//            ->with('step', 'step.assigned')
//            ->orderBy('created_at','desc')
//            ->get();
//    }

    /**
     * Display details of single
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('section', 'section.step','createdby','section.step.assignedTo')->find($id);
    }

    /**
     * Display details of single
     *
     * @param $rfpDetailsId
     * @return object
     */
    public function getByRfpId($rfpDetailsId)
    {
        return $this->model
            ->with('section', 'section.step','createdby','section.step.assignedTo')
            ->where('rfp_details_id',$rfpDetailsId);
    }

    /**
     * Store a newly created in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        if(!isset($data['id'])){
            $data['created_by'] = Auth::user()->id;
        }
        $data['updated_by'] = Auth::user()->id;
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.post_order_attachment_folder'));
    }

    /**
     * Function handle save of incident report along with attachments
     * @param $request
     * @return bool
     */
    public function storeClientOnboardingTemplate($request)
    {
        $data['id'] = $request->id ?? null;
        $data['updated_by'] = Auth::user()->id;
        $section_id = $this->sectionModel->updateOrCreate(array('id' => $data['id']), $data);
        $existingStepArr = $this->getStepListBySection($section_id->id)->pluck('id')->toArray();
        $newStepArr = $data['step-id'];
        $stepsDelete = (array_diff($existingStepArr,$newStepArr));
        if(sizeof($stepsDelete)) {
            $this->deleteStep($stepsDelete);
        }
        foreach($data['step'] as $key=> $step) {
            $this->stepModel->updateOrCreate(array('id' => $data['step-id'][$key]),
                array(
                    'section_id' => $section_id->id,
                    'step' => $step,
                    'sort_order' => $data['sort'][$key],
                )
            );
        }




        return $postOrderResult;
    }

    public function changeStatus($request)
    {
    return $this->model->where('id',$request->id)->update(['reviewed_status'=>$request->status,'reviewed_by'=>\Auth::user()->id,'reviewed_at'=>\Carbon::now()]);
    }

    public function getPercentageCompleted(){
        return 0;
    }
}
