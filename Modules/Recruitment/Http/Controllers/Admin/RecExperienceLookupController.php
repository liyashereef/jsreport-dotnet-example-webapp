<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Recruitment\Http\Requests\RecExperienceRequest;
use Modules\Recruitment\Repositories\RecExperienceLookupRepository;

class RecExperienceLookupController extends Controller
{
 protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\RecExperienceLookupRepository $recExperienceLookupRepository
     * @return void
     */
    public function __construct(RecExperienceLookupRepository $recExperienceLookupRepository, HelperService $helperService)
    {
        $this->repository = $recExperienceLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('recruitment::masters.experience-lookup');
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
     * @param  App\Http\Requests\CandidateExperienceRequest $request
     * @return json
     */
    public function store(RecExperienceRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
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
