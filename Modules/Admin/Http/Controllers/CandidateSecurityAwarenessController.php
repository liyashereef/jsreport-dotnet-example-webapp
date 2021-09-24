<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Modules\Admin\Repositories\CandidateSecurityAwarenessRepository;
use Modules\Admin\Http\Requests\CandidateSecurityAwarenessRequest;
use Illuminate\Routing\Controller;


class CandidateSecurityAwarenessController extends Controller
{
    protected $repository;
    /**
     * Create Repository instance.
     * @param  \App\Repositories\EnglishRatingLookupRepository $englishRatingLookupRepository
     * @return void
     */
    public function __construct(CandidateSecurityAwarenessRepository $candidateSecurityAwarenessRepository,HelperService $helperService)
    {
        $this->repository = $candidateSecurityAwarenessRepository;
        $this->helperService = $helperService;
    }

    /**
     *  Load the Request Type Masters Page
     *
     * @return view
     */
    public function index()
    {
        return view('admin::masters.candidate-security-awareness');
    }

    /**
     *Get a listing of the Request Type Master for Datatable.
     *
     * @return Json
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function lookupList()
    {
        return $this->repository->getLookupList();
    }

    /**
     * Store a newly created Brand Awareness in storage.
     *
     * @param  App\Http\Requests\CandidateBrandAwarenessRequest $request
     * @return json
     */
    public function store(CandidateSecurityAwarenessRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            $lookup->save();
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

   /**
     *Get details of single Brand awareness Master
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
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
