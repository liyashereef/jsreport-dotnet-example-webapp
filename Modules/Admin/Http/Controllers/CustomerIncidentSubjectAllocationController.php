<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\CustomerIncidentSubjectAllocationRequest;
use Modules\Admin\Repositories\CustomerIncidentSubjectAllocationRepository;

class CustomerIncidentSubjectAllocationController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\IncidentPriorityLookupRepository $incidentPriorityLookupRepository
     * @return void
     */
    public function __construct( HelperService $helperService,CustomerIncidentSubjectAllocationRepository $repository)
    {
        $this->helperService = $helperService;
        $this->repository =$repository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()//need to change
    {
        return view('admin::masters.incident-priority');
    }


    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList($id)
    {
        return datatables()->of($this->repository->getAll($id))->addIndexColumn()->toJson();
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
     * @param  $request
     * @return json
     */
    public function store(CustomerIncidentSubjectAllocationRequest $request)
    {

        try {
            \DB::beginTransaction();
          //  dd($request->all());
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
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
