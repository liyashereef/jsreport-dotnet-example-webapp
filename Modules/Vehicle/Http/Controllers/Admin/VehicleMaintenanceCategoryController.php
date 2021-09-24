<?php

namespace Modules\Vehicle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Vehicle\Repositories\VehicleMaintenanceCategoryRepository;
use Modules\Vehicle\Http\Requests\VehicleMaintenanceCategoryRequest;
use Modules\Vehicle\Models\VehicleMaintenanceType;

class VehicleMaintenanceCategoryController extends Controller
{
    public function __construct(
        VehicleMaintenanceCategoryRepository $vehicleMaintenanceCategoryRepository,
        HelperService $helperService
        ) {
            $this->repository = $vehicleMaintenanceCategoryRepository;
            $this->helperService = $helperService;
        }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('vehicle::maintenance-category');
    }

    public function getList()
    {
        $data = $this->repository->getAll();
        return datatables()->of($data)->addIndexColumn()->toJson();
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('expense::create');
    }
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(VehicleMaintenanceCategoryRequest $request)
    {
        try {
            \DB::beginTransaction();
            $vehicleList = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('expense::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('expense::edit');
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
     * @return Response
     */
    public function destroy($id)
    {
           try {
            \DB::beginTransaction();
            $category_delete = $this->repository->delete($id);
            \DB::commit();
            if ($category_delete == false) {
                return response()->json($this->helperService->returnFalseResponse());
            } else {
                return response()->json($this->helperService->returnTrueResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }
}
