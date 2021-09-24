<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Http\Requests\RecRateExperienceLookupRequest;
use Modules\Recruitment\Repositories\RecRateExperienceLookupRepository;

class RecRateExperienceLookupController extends Controller
{
    /**
     * Repository instance.
     * @var \Recruitment\Http\Requests\RecRateExperienceLookupRequest
     *
     */
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \Recruitment\Http\Requests\RecRateExperienceLookupRequest $recRateExperienceLookupRepository
     * @return void
     */
    public function __construct(RecRateExperienceLookupRepository $recRateExperienceLookupRepository, HelperService $helperService)
    {
        $this->repository = $recRateExperienceLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     *  Load the Experience Rating Page
     *
     * @return view
     */
    public function index()
    {
        return view('recruitment::masters.rate-experience-lookup');
    }

    /**
     *Get a listing of the Rate Experience Lookup for Datatable.
     *
     * @return Json
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Store a newly created rate experience lookup in storage.
     *
     * @param  \Recruitment\Http\Requests\RecRateExperienceLookupRequest $request
     * @return json
     */
    public function store(RecRateExperienceLookupRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            //$lookup->save();
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     *Get details of single rate experience lookup
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
