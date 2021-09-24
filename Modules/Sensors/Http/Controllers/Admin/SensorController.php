<?php

namespace Modules\Sensors\Http\Controllers\Admin;

use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sensors\Http\Requests\SensorRequest;
use Modules\Sensors\Repositories\SensorActiveSettingRepository;
use Modules\Sensors\Repositories\SensorRepository;
use Modules\Admin\Models\Customer;


class SensorController extends Controller
{
    protected $repository;
    protected $customerModel;

    /**
     * Create Repository instance.
     * @param SensorRepository $sensorRepository
     * @param HelperService $helperService
     */
    public function __construct(
        SensorRepository $sensorRepository,
        Customer $customerModel,
        HelperService $helperService
    )
    {
        $this->repository = $sensorRepository;
        $this->helperService = $helperService;
        $this->customerModel = $customerModel;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index($roomId = null)
    {
        return view('sensors::sensor-settings.add-sensor', compact('roomId'));
    }

    /**
     *Get a listing of the Request Type Master for Datatable.
     *
     * @return Json
     */
    public function getList($id = null)
    {
        return datatables()->of($this->repository->getAll($id))->addIndexColumn()->toJson();
    }

    public function store(SensorRequest $request)
    {
        try {
            DB::beginTransaction();
            $sensorStore = $this->repository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
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
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $isSensorDeleted = $this->repository->delete($id);
            if($isSensorDeleted){
                $sensorIds = [$id];
                $this->sensorActiveSetting->updateLambdaClient($sensorIds,null,null);
            }
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }


}
