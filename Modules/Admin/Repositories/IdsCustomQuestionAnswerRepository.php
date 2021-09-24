<?php


namespace Modules\Admin\Repositories;


use Modules\Admin\Models\IdsCustomQuestionAnswer;

class IdsCustomQuestionAnswerRepository
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
     * @param IdsCustomQuestionAnswer $idsCustomQuestionAnswer
     */
    public function __construct(
        IdsCustomQuestionAnswer $idsCustomQuestionAnswer
    )
    {
        $this->model = $idsCustomQuestionAnswer;
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
            ->with('idsCustomQuestions', 'idsCustomOption')
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
        return $this->model->with('idsCustomQuestions', 'idsCustomOption')->find($id);
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
