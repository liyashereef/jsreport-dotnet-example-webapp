<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Http\Requests\RecCompetencyMatrixRatingLookupRequest;
use Modules\Recruitment\Repositories\RecCompetencyMatrixRatingLookupRepository;

class RecCompetencyMatrixRatingLookupController extends Controller
{
    /**
     * Create Repository instance.
     * @param Recruitment\Repositories\RecCompetencyMatrixRatingLookupRepository
     * $recCompetencyMatrixRatingLookupRepository
     * @return void
     */
    public function __construct(
        RecCompetencyMatrixRatingLookupRepository $recCompetencyMatrixRatingLookupRepository,
        HelperService $helperService
    ) {
        $this->repository = $recCompetencyMatrixRatingLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('recruitment::masters.competency-matrix-rating');
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
     * @param  App\Http\Requests\CompetencyMatrixRatingLookupRequest $request
     * @return json
     */
    public function store(RecCompetencyMatrixRatingLookupRequest $request)
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
