<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\RfpCatalogueGroupRepository;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\RfpCatalogueGroupRequest;

class RfpCatalogueGroupLookupController extends Controller
{
    protected $repository;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(
        RfpCatalogueGroupRepository $rfpCatalogueGroupRepository,
        HelperService $helperService
    )
    {
        $this->repository = $rfpCatalogueGroupRepository;
        $this->helperService = $helperService;
    }
    public function index()
    {
        return view('admin::masters.rfp-catalogue-group');
    }
    public function getList()
    {
        return datatables()->of($this->repository->getList())->addIndexColumn()->toJson();
    }
    

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(RfpCatalogueGroupRequest $request)
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

    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
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
