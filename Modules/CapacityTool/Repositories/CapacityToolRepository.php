<?php

namespace Modules\CapacityTool\Repositories;

use Modules\CapacityTool\Models\CapacityTool;
use Modules\CapacityTool\Repositories\CapacityToolEntryRepository;
use Modules\CapacityTool\Repositories\CapacityToolQuestionRepository;

class CapacityToolRepository
{

    /**
     * Create a new CapacityTool instance.
     *
     * @param  Modules\Admin\Models\CapacityTool $capacityTool
     */
    public function __construct()
    {
        $this->model = new CapacityTool;
        $this->capacityToolQuestionRepository = new CapacityToolQuestionRepository();
        $this->capacityToolEntryRepository = new CapacityToolEntryRepository();

    }
    /**
     * Details of single resource 
     *
     * @param $capacity_tool_entry_id - integer
     * @return array
     */
    public function getSingle($capacity_tool_entry_id)
    {
        $capacityToolQuestions = $this->model
            ->where('capacity_tool_entry_id', $capacity_tool_entry_id)
            ->with(['question', 'answerable'])->get();
        return  $capacityToolQuestions;
    }

      /**
     * Display details of single resource for edit
     *
     * @param $capacity_tool_entry_id - integer
     * @return object
     */
    public function getEditCapacityTool($capacity_tool_entry_id)
    {
        $capacityToolQuestions = $this->getSingle($capacity_tool_entry_id);
        $capacityToolQuestionsWithAnswers = $this->capacityToolQuestionRepository->getQuestionsWithAnswers($capacityToolQuestions);
        $formattedData = $capacityToolQuestionsWithAnswers->map(function ($item) {
            $app = app();
            $new_item = $app->make('stdClass');
            $new_item->id = $item->question->id;
            $new_item->field_type = $item->question->field_type;
            $new_item->tooltip = $item->question->tooltip;
            $new_item->is_parent = $item->question->is_parent;
            $new_item->parent_id = $item->question->parent_id;
            $new_item->question = $item->question->question;
            $new_item->answer = $item->answer;
            $new_item->answers = $item->answers;
            if($item->answer_type == 'Modules\Admin\Models\Customer')
            {
                $new_item->project_name = $item->answerable->client_name;
                $new_item->answer = $item->answerable->id;
            }
            $new_item->answer_type = $item->answer_type;
           
            return $new_item;
        });
        return $formattedData;

    }

    


      /**
     * Display details of single resource for view
     *
     * @param $capacity_tool_entry_id - integer
     * @return object
     */
    public function getSingleCapacityTool($capacity_tool_entry_id)
    {
        $capacityToolQuestions = $this->getSingle($capacity_tool_entry_id);
        $capacityToolQuestionsWithAnswers = $this->capacityToolQuestionRepository->getQuestionsWithAnswers($capacityToolQuestions);
        $formattedData = $capacityToolQuestionsWithAnswers->map(function ($item) {
            $app = app();
            $new_item = $app->make('stdClass');
            $new_item->id = $item->question->id;
            $new_item->question = $item->question->question;
            $new_item->answer = $item->answer;
            $new_item->answer_type = $item->answer_type;
            if($item->answer_type != null){
                $new_item->answer = ($item->answer_type == 'Modules\Admin\Models\Customer') ? $item->answerable->project_number.' - '.$item->answerable->client_name :$item->answerable->value;
            }
            $new_item->answers = $item->answers;
            return $new_item;
        });
        return $formattedData;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function store($request)
    {
        $logged_in_user = \Auth::id();
        $capacity_tool_entry_id = $this->capacityToolEntryRepository->store($logged_in_user);
        foreach ($request->arr as $question) {
            $capacityTool['employee_id'] = $logged_in_user;
            $capacityTool['capacity_tool_entry_id'] = $capacity_tool_entry_id;
            $capacityTool['question_id'] = $question['question_id'];
            $capacityTool['answer'] = $question['answer'];
            $capacityTool['answer_type'] = $question['answer_type'];
            $capacityTool['created_by'] = $logged_in_user;
            $this->model->create($capacityTool);
        }
        return $capacityTool;
    }

    /**
     * Update capacity tool
     *
     * @param  json array
     * @return object
     */
    public function update($request)
    {
        $logged_in_user = \Auth::id();
        $capacity_tool_entry_id = $request->capacity_tool_entry_id;
        foreach ($request->arr as $question) {
            $capacityTool['employee_id'] = $question['employee_id'];
            $capacityTool['capacity_tool_entry_id'] = $capacity_tool_entry_id;
            $capacityTool['question_id'] = $question['question_id'];
            $capacityTool['answer'] = $question['answer'];
            $capacityTool['answer_type'] = $question['answer_type'];
            $capacityTool['created_by'] = $logged_in_user;
            $this->model->create($capacityTool);
        }
        return $capacityTool;
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        //return $this->model->destroy($id);
    }
}
