<?php

namespace Modules\Admin\Http\Controllers;

use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\ShiftModuleRequest;
use Modules\Admin\Models\ShiftModule;
use Modules\Admin\Models\ShiftModuleDropdown;
use Modules\Admin\Models\ShiftModuleEntry;
use Modules\Admin\Models\ShiftModuleField;
use Modules\Admin\Models\ShiftModuleFieldType;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\ShiftModuleRepository;

class ShiftModuleController extends Controller
{

    /**
     * The Repository instance.
     * @var \App\Services\HelperService
     * @var \Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
     */
    protected $customerEmployeeAllocationRepository, $helperService, $shiftModuleRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, HelperService $helperService, ShiftModuleRepository $shiftModuleRepository)
    {
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->shiftModuleRepository = $shiftModuleRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return view
     */
    public function index()
    {
        return view('admin::customer.shift-module', ['customer_list' => $this->customerEmployeeAllocationRepository->getCustomersList()->sortBy('client_name')]);
    }

    public function getModuleList($customer_id = null)
    {
        $modules_list = $this->shiftModuleRepository->getAll($customer_id);
        return datatables()->of($this->prepareDataforShiftModuleList($modules_list))->addIndexColumn()->toJson();
    }

    /**
     * Prepare datatable elements as array.
     * @param  $modules_list
     * @return array
     */
    public function prepareDataforShiftModuleList($modules_list)
    {
        $datatable_rows = array();
        foreach ($modules_list as $key => $each_list) {
            $each_row["id"] = $each_list->id;
            $each_row["module_name"] = $each_list->module_name;
            $each_row["customer"] = $each_list->customer->client_name;
            $each_row["status"] = ($each_list->is_active == 1) ? 'Active' : 'Inactive';
            $each_row["post_order"] = ($each_list->post_order == 1) ? 'Yes' : 'No';
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function addModule($module_id = null)
    {
        $module_exists = 0;
        $field_type = ShiftModuleFieldType::get();
        $shift_dropdown = ShiftModuleDropdown::where('info',0)->orderby('dropdown_name', 'asc')->get();
        $shift_dropdown_with_info = ShiftModuleDropdown::where('info',1)->orderby('dropdown_name', 'asc')->get();
        $post_orders = ShiftModuleDropdown::where('post_order',1)->orderby('dropdown_name', 'asc')->get();
        $customer_list = $this->customerEmployeeAllocationRepository->getCustomersList()->sortBy('client_name');
        if (isset($module_id)) {
            $module = ShiftModule::select('id', 'customer_id', 'module_name','enable_timeshift','dashboard_view', 'is_active')->where('id', $module_id)->get();
            $module_fields = ShiftModuleField::where('module_id', $module_id)->get();
            $module_dropdown_id = ShiftModuleEntry::pluck('module_id')->toArray();
            
            if (in_array($module_id, $module_dropdown_id)) {
                $module_exists = 1;
            } else {
                $module_exists = 0;
            }
            return view('admin::customer.shift-module-add', compact('module_id', 'module', 'module_fields', 'field_type', 'shift_dropdown', 'customer_list', 'module_exists','shift_dropdown_with_info','post_orders'));

        } else {
            $module_id = 0;
            $module_exists = 0;
            return view('admin::customer.shift-module-add', compact('module_id', 'field_type', 'shift_dropdown', 'customer_list', 'module_exists','shift_dropdown_with_info','post_orders'));

        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function storeModule(ShiftModuleRequest $request)
    {
        try {
            DB::beginTransaction();
            $obj_module = $this->shiftModuleRepository->storeShiftModule($request);
            $result = ($obj_module->wasRecentlyCreated);
            DB::commit();
            return response()->json(array('success' => true, 'result' => $result));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Function to delete template
     * @param Request $request
     * @return json
     */
    public function destroy(Request $request)
    {
        ShiftModuleField::where('module_id', $request->get('id'))->delete();
        ShiftModule::find($request->get('id'))->delete();
        return response()->json(array('success' => true));
    }

    public function getPostOrderModule($customer_id)
    {
       $module_count = ShiftModule::where('post_order', 1)->where('customer_id', $customer_id)->count();
       return $module_count;
    }

    public function getShiftModulePostOrder()
    {
       return $obj_module = $this->shiftModuleRepository->getShiftJournalSummary();
    
    }

}
