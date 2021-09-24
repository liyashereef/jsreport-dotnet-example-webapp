<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Models\RecCompetencyMatrixCategoryLookup;
use Modules\Recruitment\Http\Requests\RecCompetencyMatrixLookupRequest;
use Modules\Recruitment\Repositories\RecCompetencyMatrixLookupRepository;
use Modules\Recruitment\Repositories\RecCompetencyMatrixCategoryLookupRepository;

class RecCompetencyMatrixLookupController extends Controller
{
   /**
     * Create Repository instance.
     * @param  Modules\Recruitment\Repositories\RecCompetencyMatrixLookupRepository $recCompetencyMatrixLookupRepository
     * @return void
     */
    public function __construct(
        RecCompetencyMatrixLookupRepository $recCompetencyMatrixLookupRepository,
        RecCompetencyMatrixCategoryLookupRepository $recCompetencyMatrixCategoryLookupRepository,
        HelperService $helperService,
        RecCompetencyMatrixCategoryLookup $recCompetencyMatrixCategoryLookup
    ) {
        $this->repository = $recCompetencyMatrixLookupRepository;
        $this->competencyMatrixCategoryLookupRepository = $recCompetencyMatrixCategoryLookupRepository;
        $this->competencyMatrixCategoryLookup = $recCompetencyMatrixCategoryLookup;
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
        return view('recruitment::masters.competency-matrix-lookup', compact('category_lookup'));
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
     * @param  Modules\Recruitment\Http\Requests\RecCompetencyMatrixLookupRequest; $request
     * @return json
     */
    public function store(RecCompetencyMatrixLookupRequest $request)
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
