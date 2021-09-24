<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Repositories\RecEnglishRatingLookupRepository;
use Modules\Recruitment\Http\Requests\RecEnglishRatingRequest;

class RecEnglishRatingLookupController extends Controller
{
    protected $repository;
    /**
     * Create Repository instance.
     * @param  Modules\Recruitment\Repositories\RecEnglishRatingLookupRepository $recEnglishRatingLookupRepository
     * @return void
     */
    public function __construct(RecEnglishRatingLookupRepository $recEnglishRatingLookupRepository,HelperService $helperService)
    {
        $this->repository = $recEnglishRatingLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('recruitment::masters.english-rating-lookup');
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
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CandidateAssignmentTypeRequest $request
     * @return json
     */
    public function store(RecEnglishRatingRequest $request)
    {
        try {
            \DB::connection('mysql_rec')->beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::connection('mysql_rec')->commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::connection('mysql_rec')->rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::connection('mysql_rec')->beginTransaction();
            $lookup_delete = $this->repository->delete($id);
            \DB::connection('mysql_rec')->commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::connection('mysql_rec')->rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
