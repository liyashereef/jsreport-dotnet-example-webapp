<?php

namespace Modules\Admin\Repositories;

use DB;
use Modules\Admin\Models\CompetencyMatrixLookup;

class CompetencyMatrixLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CompetencyMatrixLookup instance.
     *
     * @param  \App\Models\CompetencyMatrixLookup $competencyMatrixLookup
     */
    public function __construct(CompetencyMatrixLookup $competencyMatrixLookup)
    {
        $this->model = $competencyMatrixLookup;
    }

    /**
     * Get CompetencyMatrixLookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        $result = $this->model->select(['id', 'competency', 'definition', 'behavior', 'competency_matrix_category_id', 'created_at', 'updated_at', 'deleted_at'])->get();
        $competency_array = array();
        foreach ($result as $key => $competency) {
            $competency_array[$key]['id'] = $competency->id;
            $competency_array[$key]['competency'] = $competency->competency;
            $competency_array[$key]['definition'] = $competency->definition;
            $competency_array[$key]['behavior'] = $competency->behavior;
            $competency_array[$key]['competency_matrix_category_id'] = $competency->competency_matrix_category_id;
        }
        return $competency_array;
    }

    public function getCompetency()
    {
        $result = $this->model
        ->select(['id', 'competency', 'definition', 'behavior', 'competency_matrix_category_id', 'created_at', 'updated_at', 'deleted_at'])
        ->with('category')
        ->orderBy('competency_matrix_category_id')        
        ->get();
        $competency_array = array();
        $prev_competency_category = null;
        $competency_category = null;
        $category_index = -1;
        $competency_index = 0;
        foreach ($result as $key => $competency) {
            $competency_category = $competency->competency_matrix_category_id;
            if(!isset($prev_competency_category) || 
            ( (isset($prev_competency_category) && $competency_category != $prev_competency_category)))
            {   
                $category_index++;
                $prev_competency_category = $competency_category;                 
                $competency_index = 0;
                
            }
            $competency_array[$category_index]['competency_matrix_category_id'] = $competency->competency_matrix_category_id;
            $competency_array[$category_index]['competency_matrix_category'] = $competency->category->category_name;
            $competency_array[$category_index]['competency'][$competency_index]['id'] = $competency->id;
            $competency_array[$category_index]['competency'][$competency_index]['competency'] = $competency->competency;
            $competency_array[$category_index]['competency'][$competency_index]['definition'] = ($competency->definition);
            $competency_array[$category_index]['competency'][$competency_index]['behavior'] = ($competency->behavior);
            $competency_index++;
        }
        return $competency_array;
    }    

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('competency')->pluck('competency', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($competency_id)
    {
        $array = array();
        $competency = $this->model->find($competency_id);
        if ($competency == null) {
            $array['id'] = $competency->id;
            $array['competency'] = $competency->category_name;
            $array['definition'] = $competency->definition;
            $array['behavior'] = $competency->behavior;
            $array['competency_matrix_category_id'] = $competency->competency_matrix_category_id;
        } else {
            $array = $competency->toArray();
        }
        return $array;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function store($data)
    {
        try {
            DB::beginTransaction();           
            $shift_save = $this->model->updateOrCreate(array('id' => $data['id']), $data);
            DB::commit();
            return $shift_save;

        } catch (\Exception $e) {
            DB::rollBack();
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

    /**
     * Get the count of competency matrix with the given category 
     *
     * @param  integer $category_id
     * @return integer count
     */
    public function getCompetencyMatrixCategory($category_id)
    {
        return $this->model->where('competency_matrix_category_id',$category_id)->count();
    }


}
