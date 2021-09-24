<?php

namespace Modules\Admin\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Timetracker\Http\Requests\DispatchRequestTypeRequest;
use Modules\Timetracker\Repositories\DispatchRequestTypeRepository;

class MSTDispatchRequestTypeController extends Controller
{
    protected $repository;
    protected $helper_service;

    public function __construct(DispatchRequestTypeRepository $repository,
                                HelperService $helper_service)
    {
        $this->repository = $repository;
        $this->helper_service = $helper_service;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        return view('timetracker::admin.request_type.list');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('timetracker::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  DispatchRequestTypeRequest $request
     * @return Response
     */
    public function store(DispatchRequestTypeRequest $request)
    {
        try {
            \DB::beginTransaction();
            $dispatch_request_type = $this->repository->save($request->all());
            \DB::commit();
            return response()->json(array('success' => 'true', 'data' => $dispatch_request_type));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helper_service->returnFalseResponse($e));
        }

    }

    /**
     * Return a specified resource
     * @param integer $id
     * @return Response
     */
    public function show($id)
    {
        return response()->json($this->repository->getById($id));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('timetracker::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(DispatchRequestTypeRequest $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @param integer $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $is_deleted = $this->repository->delete($id);
            \DB::commit();
            if ($is_deleted == false) {
                return response()->json($this->helper_service->returnFalseResponse());
            } else {
                return response()->json($this->helper_service->returnTrueResponse(null));
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helper_service->returnFalseResponse($e));
        }
    }

    public function list()
    {
        return datatables()->of($this->repository->getAll())->toJson();
    }

}
