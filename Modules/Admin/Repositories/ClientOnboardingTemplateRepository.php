<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ClientOnboardingDefaultSection;
use Modules\Admin\Models\ClientOnboardingTemplateSection;
use Modules\Admin\Models\ClientOnboardingTemplateStep;
use Illuminate\Support\Facades\Auth;

class ClientOnboardingTemplateRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $sectionModel;
    protected $stepModel;

    /**
     * Create a new instance.
     * @param ClientOnboardingTemplateSection $clientOnboardingDefaultSection
     * @param ClientOnboardingTemplateStep $clientOnboardingDefaultTask
     */
    public function __construct(
        ClientOnboardingTemplateSection $clientOnboardingSection,
        ClientOnboardingTemplateStep $clientOnboardingStep
    )
    {
        $this->sectionModel = $clientOnboardingSection;
        $this->stepModel = $clientOnboardingStep;
    }


    /**
     * Get Section model
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|ClientOnboardingTemplateSection
     */
    public function getSectionListModel()
    {
        return $this->sectionModel->with('step');
    }
    /**
     * Get Section list
     *
     * @param empty
     * @return array
     */
    public function getSectionList()
    {
        return $this->getSectionListModel()->pluck('section_name','id')->toArray();
    }

    /**
     * Get Step list
     *
     * @param empty
     * @return array
     */
    public function getStepListBySection($section_id)
    {
        return $this->stepModel->where('section_id',$section_id)->orderby('sort_order','asc')->get();
    }

    /**
     * Get Step list
     *
     * @param empty
     * @return array
     */
    public function getTaskBySectionList($section_id)
    {
        return $this->sectionModel->orderby('sort_order','asc')->pluck('section','id')->toArray();
    }

    /**
     * Get list
     *
     * @param empty
     * @return array
     */
    public function getSectionWithTask($id = null)
    {
        $idArr = is_array($id) || $id == null ? $id : [$id];
        $sectionWithStep = $this->sectionModel
            ->with('step')
            ->orderby('sort_order','asc');
        if(isset($idArr)) {
            $sectionWithStep = $sectionWithStep->whereIn('id',$idArr);
        }
        $returnVal = is_array($id) || $id == null ? $sectionWithStep->get() : $sectionWithStep->first();
        return $returnVal;
    }    

    /**
     * Display details of single
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->sectionModel->find($id);
    }

    /**
     * Store a newly created
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        if(!isset($data['id'])) {
            $data['created_by'] = Auth::user()->id;
        }
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
        return $section_id;
    }

    /**
     * Remove.
     *
     * @param  $id
     * @return object
     */
    public function deleteStep($id)
    {
        return $this->stepModel->destroy($id);
    }

    /**
     * Remove.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->sectionModel->destroy($id);
    }


}
