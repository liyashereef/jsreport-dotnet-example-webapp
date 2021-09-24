<?php

namespace Modules\Admin\Repositories;

use DB;
use Modules\Admin\Models\CompetencyMatrixCategoryLookup;
use Modules\Admin\Repositories\CompetencyMatrixLookupRepository;

class CompetencyMatrixCategoryLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CompetencyMatrixCategoryLookup instance.
     *
     * @param  \App\Models\CompetencyMatrixCategoryLookup $competencyMatrixCategoryLookup
     */
    public function __construct(
        CompetencyMatrixCategoryLookup $competencyMatrixCategoryLookup,
        CompetencyMatrixLookupRepository $competency_matrix_repository
        )
    {
        $this->model = $competencyMatrixCategoryLookup;
        $this->competency_matrix_repository = $competency_matrix_repository;
    }

    /**
     * Get Competency category list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        $result = $this->model->select(['id', 'category_name', 'short_name'])->get();
        $category_array = array();
        foreach ($result as $key => $category) {
            $category_array[$key]['id'] = $category->id;
            $category_array[$key]['category_name'] = $category->category_name;
            $category_array[$key]['short_name'] = $category->short_name;
        }
        return $category_array;
    }

    /**
     * Get Competency category lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {

        return $this->model->orderBy('category_name')->pluck('category_name', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($category_id)
    {
        $array = array();
        $category = $this->model->find($category_id);
        if ($category == null) {
            $array['id'] = $category->id;
            $array['category_name'] = $category->category_name;
            $array['short_name'] = $category->short_name;
        } else {
            $array = $category->toArray();
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
            $category_save = $this->model->updateOrCreate(array('id' => $data['id']), $data);
            DB::commit();
            return $category_save;

        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return string
     */
    public function delete($id)
    {
        //Check if any competency matrix has the to-be deleted  category
        $competency_matrix_count = $this->competency_matrix_repository->getCompetencyMatrixCategory($id);
        if($competency_matrix_count <= 0)
        {
             $this->model->destroy($id);
             return 'deleted';
        }else{
            return 'warning';
        }
    }
}
