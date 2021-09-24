<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\CompetencyMatrixLookupRequest;
use Modules\Admin\Models\CompetencyMatrixCategoryLookup;
use Modules\Admin\Repositories\CompetencyMatrixCategoryLookupRepository;
use Modules\Admin\Repositories\CompetencyMatrixLookupRepository;

class CompetencyMatrixLookupController extends Controller
{

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CompetencyMatrixLookupRepository $scheduleShiftTimingsRepository
     * @return void
     */
    public function __construct(
        CompetencyMatrixLookupRepository $competencyMatrixLookupRepository,
        CompetencyMatrixCategoryLookupRepository $competencyMatrixCategoryLookupRepository,
        HelperService $helperService,
        CompetencyMatrixCategoryLookup $competencyMatrixCategoryLookup
    ) {
        $this->repository = $competencyMatrixLookupRepository;
        $this->competencyMatrixCategoryLookupRepository = $competencyMatrixCategoryLookupRepository;
        $this->competencyMatrixCategoryLookup = $competencyMatrixCategoryLookup;
        $this->helperService = $helperService;

    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category_lookup = $this->competencyMatrixCategoryLookup->pluck('category_name', 'id')->toArray();
        return view('admin::masters.competency-matrix-lookup', compact('category_lookup'));
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function get($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CompetencyMatrixCategoryLookupRequest $request
     * @return json
     */
    public function store(CompetencyMatrixLookupRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->store($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
