<?php


namespace Modules\Admin\Repositories;


use Modules\Admin\Models\UniformSchedulingCustomQuestionAnswer;

class UniformSchedulingCustomQuestionAnswerRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new instance.
     *
     * @param UniformSchedulingCustomQuestionAnswer $uniformSchedulingCustomQuestionAnswer
     */
    public function __construct(
        UniformSchedulingCustomQuestionAnswer $uniformSchedulingCustomQuestionAnswer
    )
    {
        $this->model = $uniformSchedulingCustomQuestionAnswer;
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
            ->with('uniformSchedulingCustomQuestions', 'uniformSchedulingCustomQuestionOption')
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
        return $this->model->with('uniformSchedulingCustomQuestions', 'uniformSchedulingCustomQuestionOption')->find($id);
    }

    /**
     * Store a newly created post order group in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the post order group from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
