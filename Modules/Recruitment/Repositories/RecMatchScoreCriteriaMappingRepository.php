<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecMatchScoreCriteriaMapping;

class RecMatchScoreCriteriaMappingRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RecMatchScoreCriteria instance.
     *
     * @param  \App\Models\RecMatchScoreCriteria $recMatchScoreCriteria
     */
    public function __construct(RecMatchScoreCriteriaMapping $recMatchScoreCriteriaMapping)
    {
        $this->model = $recMatchScoreCriteriaMapping;
    }

    /**
     * Get Experience  lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'criteria_name','weight','type_id','created_at','updated_at'])->get();
    }

    /**
     * Get Experience lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('criteria_name', 'asc')->pluck('criteria_name', 'id')->toArray();
    }
    /**
     * Get Experience lookup list
     *
     * @param empty
     * @return array
     */
    public function getCriteria($id)
    {
        $q = 'CAST(`limit` as UNSIGNED)';
        return $this->model->where('criteria', $id)->with('criteriaList')->orderByRaw($q)->get();
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        if (isset($data['score_yes']) && isset($data['score_no'])) {
            $record['limit']=0; //NO
            $record['score']=$data['score_no'];
            $record['criteria']=$data['criteria_id'];
            $this->model->updateOrCreate(array('id' => $data['no-step-id']), $record);
            $record['limit']=1; //NO
            $record['score']=$data['score_yes'];
            $record['criteria']=$data['criteria_id'];
            $this->model->updateOrCreate(array('id' => $data['yes-step-id']), $record);
        } else {
            if (isset($data['over_limit'])  && isset($data['over_score'])) {
                array_push($data['lower_limit'], $data['over_limit']);
                array_push($data['position'], array_key_last($data['lower_limit']));
                array_push($data['score'], $data['over_score']);
                array_push($data['step-id'], $data['over_step_id']);
            }
            $already_saved_data=$this->model->where('criteria', $data['criteria_id'])->pluck('id')->toArray();
            $diff_arr=array_diff($already_saved_data, $data['step-id']);
            $this->model->whereIn('id', $diff_arr)->delete();
            foreach ($data['position'] as $key => $i) {
                if (isset($i)) {
                    $record = [
                       'limit' => $data['lower_limit'][$i],
                       'criteria' => $data['criteria_id'],
                       'score' => $data['score'][$i],

                       ];
                    $this->model->updateOrCreate(array('id' => $data['step-id'][$i]), $record);
                }
            }
        }
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
}
