<?php


namespace Modules\Admin\Repositories;


use Modules\Admin\Models\IdsCustomQuestionOption;

class IdsCustomQuestionOptionRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $idsCustomQuestionOptionAllocationRepository;

    /**
     * Create a new IdsCustomQuestionOption instance.
     *
     * @param IdsCustomQuestionOption $idsCustomQuestionOption
     * @param IdsCustomQuestionOptionAllocationRepository $idsCustomQuestionOptionAllocationRepository
     */
    public function __construct(
        IdsCustomQuestionOption $idsCustomQuestionOption,
        IdsCustomQuestionOptionAllocationRepository $idsCustomQuestionOptionAllocationRepository
    )
    {
        $this->model = $idsCustomQuestionOption;
        $this->idsCustomQuestionOptionAllocationRepository = $idsCustomQuestionOptionAllocationRepository;
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
            ->orderby('custom_question_option','asc')
            ->pluck( 'custom_question_option','id')
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
            ->orderby('custom_question_option','asc')
            ->select(['id', 'custom_question_option'])->get();
    }

    /**
     * Display details of single
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data,$question_id)
    {
         $option_arr= $this->idsCustomQuestionOptionAllocationRepository->getOptionsByQuestion($question_id)->pluck('ids_custom_option_id')->toArray();
         $diff_arr=array_diff($option_arr,$data['option_id']);
          foreach ($diff_arr as $key => $optionId) {
            if($optionId!=config('globals.idsCustomQuestionOther')){
                $this->model->where('id',$optionId)->delete();
            }
        }
            $this->idsCustomQuestionOptionAllocationRepository->deleteBasedonQuestionId($question_id); 
         for($i = 0; $i < count($data['answer_option']); $i++)
            {
            $datas = [
                        'custom_question_option' =>$data['answer_option'][$i],
                    ];
                
                    $options=$this->model->updateOrCreate(array('id' => $data['option_id'][$i]), $datas);
                    $this->idsCustomQuestionOptionAllocationRepository->save($question_id, $options->id);
            }
          if(isset($data['has_other']) && $data['has_other']==1)
        {
         $this->idsCustomQuestionOptionAllocationRepository->save($question_id,config('globals.idsCustomQuestionOther'));   
        }
    
        return true;
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

    public function getOptionsByQuestion($question_id)
    {
        return $this->idsCustomQuestionOptionAllocationRepository->getOptionsByQuestion($question_id);
    }

}
