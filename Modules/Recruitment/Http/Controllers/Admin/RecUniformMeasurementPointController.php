<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Recruitment\Http\Requests\RecUniformMeasurementRequest;
use Modules\Recruitment\Repositories\RecUniformMeasurementPointRepository;

class RecUniformMeasurementPointController extends Controller
{
     /**
     * Create Repository instance.
     * @param  Modules\Recruitment\Repositories\RecUniformMeasurementPointRepository $recUniformMeasurementPointRepository
     * @return void
     */
    public function __construct(RecUniformMeasurementPointRepository $recUniformMeasurementPointRepository, HelperService $helperService)
    {
        $this->repository = $recUniformMeasurementPointRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('recruitment::masters.uniform-measurement-points');
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
     * @param  Modules\Recruitment\Http\Requests\RecUniformItemRequest $request
     * @return json
     */
    public function store(RecUniformMeasurementRequest $request)
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
