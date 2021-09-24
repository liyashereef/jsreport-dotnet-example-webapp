<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\RfpResponseTypeRequest;
use Modules\Admin\Repositories\RfpResponseTypeLookupRepository;

class RfpResponseTypeLookupController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param RfpResponseTypeLookupRepository $rfpResponseTypeLookupRepository
     * @param HelperService $helperService
     */
    public function __construct(
        RfpResponseTypeLookupRepository $rfpResponseTypeLookupRepository,
        HelperService $helperService
    )
    {
        $this->repository = $rfpResponseTypeLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::contracts.rfp-response-type');
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
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
     *
     * @param RfpResponseTypeRequest $request
     * @return json
     */
    public function store(RfpResponseTypeRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
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
