<?php

namespace Modules\Sensors\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Modules\Sensors\Repositories\SensorTriggerRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;

class SensorTriggerController extends Controller
{
    protected $repository;

    /**
     * Create Repository instance.
     * @param SensorRepository $sensorRepository
     * @param HelperService $helperService
     */
    public function __construct(CustomerRepository $customerRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, SensorTriggerRepository $sensorTriggerRepository, HelperService $helperService)
    {
        $this->repository = $sensorTriggerRepository;
        $this->helperService = $helperService;
        $this->customerRepository = $customerRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index($roomId = null)
    {
        $customer_details_arr = $this->customerRepository->getProjectsDropdownList('all');
        return view('sensors::sensor-trigger.sensor-trigger-view', compact('roomId','customer_details_arr'));
    }

    /**
     *Get a listing of the Request Type Master for Datatable.
     *
     * @return Json
     */
    public function getList($id = null, Request $request)
    {
        $client_id = $request->get('client_id');
        return datatables()->of($this->repository->getAll($id, $client_id))->addIndexColumn()->toJson();
    }




}
