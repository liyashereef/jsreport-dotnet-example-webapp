<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\SecurityClearanceRequest;
use Modules\Admin\Repositories\SecurityClearanceLookupRepository;

class SecurityClearanceLookupController extends Controller
{

    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\SecurityClearanceLookupRepository $securityClearanceLookupRepository
     * @return void
     */
    public function __construct(SecurityClearanceLookupRepository $securityClearanceLookupRepository, HelperService $helperService)
    {
        $this->repository = $securityClearanceLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the Security Clearance  Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::masters.security-clearance');
    }

    /**
     * Display a listing of Security Clearance.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single Security Clearance
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created Security Clearance in storage.
     *
     * @param  App\Http\Requests\SecurityClearanceRequest $request
     * @return json
     */
    public function store(SecurityClearanceRequest $request)
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
     * Remove the specified Security Clearance from storage.
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
