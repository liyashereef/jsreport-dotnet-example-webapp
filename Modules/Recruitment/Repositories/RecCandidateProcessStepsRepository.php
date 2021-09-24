<?php

namespace Modules\Recruitment\Repositories;

use Auth;
use Modules\Recruitment\Models\RecProcessSteps;

class RecCandidateProcessStepsRepository
{
   /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /** 
     * Create a new RecBrandAwareness instance.
     *
     * @param  Modules\Recruitment\Models\RecCandidate $recCandidate;
     */
    public function __construct(RecProcessSteps $recProcessSteps)
    {
        $this->model = $recProcessSteps;
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderBy('step_order')->get();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    public function save($data)
    {
        if (empty($data['id'])) {
            $data['created_by'] = Auth::user()->id;
        } else {
            $data['updated_by'] = Auth::user()->id;
        }

        if (array_key_exists("status", $data) && $data['status'] == 'on') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            $data['password'] = bcrypt('password');
        }

        $lookup = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $lookup;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Candidate Login
     *
     * @param Request $request
     * @param Response
     */
    public function candidateLogin($data)
    {
        $content["code"] = 406;
        $content["success"] = false;
        $content["message"] = "Invalid user";
        if (\Auth::guard('rec_candidate')->attempt(['username' => $data->username, 'password' => $data->password], true)) {
            $content["code"] = 200;
            $content["success"] = true;
            $content["message"] = "Welcome " . \Auth::guard('rec_candidate')->user()->first_name . " " . \Auth::guard('rec_candidate')->user()->last_name;
            return json_encode($content, true);
        } else {
            return json_encode($content, true);
        }
    }
}
