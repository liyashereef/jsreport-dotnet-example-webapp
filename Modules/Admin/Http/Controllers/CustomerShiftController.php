<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Modules\Admin\Models\CustomerShifts;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\CustomerShiftRequest;
use Modules\Admin\Repositories\CustomerShiftRepository;
use Modules\Admin\Repositories\CustomerRepository;

class CustomerShiftController extends Controller
{
    protected $helperService, $customerShiftRepository,$customerRepository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\CustomerShiftRepository $customerShiftRepository;
     * @return void
     */
    public function __construct(HelperService $helperService, CustomerShiftRepository $customerShiftRepository,CustomerRepository $customerRepository)
    {
        $this->helperService = $helperService;
        $this->customerShiftRepository = $customerShiftRepository;
        $this->customerRepository=$customerRepository;

    }

    /**
     * Display a listing of the CustomerShift Types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_arr = $this->customerRepository->getCustomerList();
        $customerlist = array();
        foreach ($customer_arr as $key => $customer) {
            $id=$customer['id'];
            $customerlist[$id]=$customer['project_number']. ' - ' .$customer['client_name'];
        }
        return view('admin::customer.customers-shift', compact('customerlist'));

    }

    /**
     * Store  newly created  CustomerShift Type in storage.
     *
     * @param  Modules\Admin\Http\Requests\CustomerShiftRequest $request
     * @return Json
     */
    public function store(CustomerShiftRequest $request)
    {
        try {
            DB::beginTransaction();
            $role = $this->customerShiftRepository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * List all CustomerShift Types in datatable.
     *
     *
     * @return Json
     */
    public function getList(Request $request)
    {
        // return datatables()->of($this->customerShiftRepository->getAll())->addIndexColumn()->toJson();
        $client_id = $request->get('client_id');
        $details = $this->customerShiftRepository->getAll($client_id);

        return datatables()->of($details)->addIndexColumn()->toJson();

    }

    /**
     * Show the form for editing the specified CustomerShift Type.
     *
     * @param  $id
     * @return Json
     */
    public function getSingle($id)
    {
        return response()->json($this->customerShiftRepository->get($id));
    }

    /**
     * Remove the specified CustomerShift Type from storage.
     *
     * @param  $id
     * @return Json
     */
    public function destroy($id)
    {

        try {
            DB::beginTransaction();
            $customerShift_delete = $this->customerShiftRepository->deleteCustomerShift($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

}
