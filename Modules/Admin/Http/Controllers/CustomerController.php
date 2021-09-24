<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Http\Requests\CustomerRequest;
use Modules\Admin\Http\Requests\ImportRequest;
use Modules\Admin\Http\Requests\UploadRequest;
use Modules\Admin\Models\AdminColorsetting;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerFenceLogs;
use Modules\Admin\Models\CustomerRoom;
use Modules\Admin\Models\CustomerShifts;
use Modules\Admin\Models\Geofence;
use Modules\Admin\Models\Guardroute;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerTypeRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Sensors\Repositories\SensorActiveSettingRepository;

class CustomerController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Repositories\CustomerRepository
     * @var \App\Services\HelperService
     */
    protected $customerRepository, $helperService, $guardroute, $customerEmployeeAllocationRespository;
    protected $customerTypeRepository;
    /**
     * Create Repository instance.
     *
     * @param  \App\Repositories\CustomerRepository $customerRepository
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRespository,
        CustomerRepository $customerRepository,
        CustomerRoom $customerRoom,
        SensorActiveSettingRepository $sensorActiveSettingRepository,
        HelperService $helperService,
        Guardroute $guardroute,
        CustomerTypeRepository $customerTypeRepository
    ) {
        $this->customerRoomModel = $customerRoom;
        $this->customerRepository = $customerRepository;
        $this->sensorActiveSettingRepository = $sensorActiveSettingRepository;
        $this->helperService = $helperService;
        $this->guardroute = $guardroute;
        $this->customerEmployeeAllocation = $customerEmployeeAllocationRespository;
        $this->customerTypeRepository = $customerTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::customer.customer', ['lookups' => $this->customerRepository->getLookups(), 'mandatory_course_array' => [], 'customerList' => $this->customerRepository->clienLookUps()]);
    }

    /**
     * Get Customers List
     * @return json
     */
    public function getList($customer_type = PERMANENT_CUSTOMER, $customer_status = ACTIVE, Request $request)
    {
        $client_id = $request->get('client_id');
        $client_id =  (array) $client_id;
        return datatables()->of($this->customerRepository->getCustomerList($customer_type, $customer_status, $client_id))->addIndexColumn()->toJson();
    }

    /**
     * Display customer add and edit form
     */

    public function addCustomer()
    {

        $singleCustomer = [];
        $allocatedIncidentSubjects = [];
        $customerAllocattedUsers = [];

        return view('admin::customer.add-customer-form', [
            'lookups' => $this->customerRepository->getLookups(),
            'customerTypes' => $this->customerTypeRepository->getList(),
            'mandatory_course_array' => [],
            'single_customer_details' => $singleCustomer,
            'allocatedIncidentSubjects' => $allocatedIncidentSubjects,
            'customerAllocattedUsers' => $customerAllocattedUsers,
            'headers' => [],
        ]);
    }

    /**
     * Get single customer details
     * @param  $id
     * @return json
     */
    public function editCustomer($id)
    {

        $singleCustomer = $this->customerRepository->getSingleCustomer($id);
        $customerAllocattedUsers = $this->customerEmployeeAllocation->allocationList($id)->pluck('name_with_emp_no', 'id')->toArray();
        $allocatedIncidentSubjects = array_pluck(
            $singleCustomer->subjectAllocation,
            'subject.subject',
            'subject.id'
        );
        return view('admin::customer.add-customer-form', [
            'lookups' => $this->customerRepository->getLookups(),
            'mandatory_course_array' => [],
            'customerTypes' => $this->customerTypeRepository->getList(),
            'single_customer_details' => $singleCustomer,
            'allocatedIncidentSubjects' => $allocatedIncidentSubjects,
            'customerAllocattedUsers' => $customerAllocattedUsers,
            'headers' => [],
        ]);
    }

    public function getAddressLocation(Request $request)
    {
        $postal_code = str_replace(" ", "", $request->get('postal_code'));
        $google_api_key = config('globals.google_api_curl_key');
        $location_data = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=" . $postal_code . "&sensor=false&key=" . $google_api_key);

        $location_data = json_decode($location_data);

        if (isset($location_data->{'results'}[0])) {
            $data['lat'] = $location_data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $data['long'] = $location_data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
            $data['postal_code_address'] = $location_data->{'results'}[0]->formatted_address;
        } else {
            $data['lat'] = null;
            $data['long'] = null;
            $data['postal_code_address'] = null;
        }
        return $data;
    }

    public function showColorSettings(Request $request)
    {
        $rangelilst = "";
        return view('admin::color_settings.colorsettings', compact('rangelilst'));
    }

    public function getColorSettings(Request $request)
    {
        $colorsetting = AdminColorsetting::all();
        return datatables()->of($colorsetting)->addIndexColumn()->toJson();
    }

    public function getColorSettingssingle(Request $request)
    {
        $colorsetting = AdminColorsetting::find($request->id);
        return $colorsetting;
    }

    public function removeColorSettings(Request $request)
    {
        $id = $request->id;
        $removegroup = AdminColorsetting::find($request->id)->delete();
        if ($removegroup) {
            $successcontent['success'] = true;
            $successcontent['message'] = 'Added successfully';
            $successcontent['code'] = 200;
        } else {
            $successcontent['success'] = false;
            $successcontent['message'] = 'Not deleted';
            $successcontent['code'] = 406;
        }
        return $successcontent;
    }

    public function setColorSettings(Request $request)
    {
        $id = $request->get('id');
        $title = $request->get('Title');
        $Group = $request->get('Group');
        $colorcode = $request->get('colorcode');
        $rangefrom = $request->get('rangefrom');
        $rangetill = $request->get('rangetill');
        if ($id) {
            $valuecheck = AdminColorsetting::whereRaw('fieldIdentifier=? and id!=? and ((rangebegin between ? and ?) or (rangeend between ? and ?))', [$Group, $id, $rangefrom, $rangetill, $rangefrom, $rangetill])->count();
        } else {
            $valuecheck = AdminColorsetting::whereRaw('fieldidentifier=?  and ((rangebegin between ? and ?) or (rangeend between ? and ?))', [$Group, $rangefrom, $rangetill, $rangefrom, $rangetill])->count();
        }
        if ($valuecheck > 0) {
            $successcontent['success'] = false;
            $successcontent['message'] = 'Similar data exist';
            $successcontent['code'] = 406;
        } else {
            if ($id) {
                $content = AdminColorsetting::find($id);
            } else {
                $content = new AdminColorsetting;
                $content->status = true;
                $content->created_by = Auth::user()->id;
            }
            $content->title = $title;
            $content->colorhexacode = $colorcode;
            $content->fieldidentifier = $Group;
            $content->rangebegin = $rangefrom;
            $content->rangeend = $rangetill;
            $content->save();

            $successcontent['success'] = true;
            $successcontent['message'] = 'Saved';
            $successcontent['code'] = 200;
        }
        return $successcontent;
    }
    public function getFenceList(Request $request)
    {
        $fencelist = Geofence::with('ContractualVisitUnit')->where('customer_id', $request->get('customerid'))->get();
        return view('admin::partials.fencelist', compact('fencelist'));
    }

    public function disablefence(Request $request)
    {
        $fenceid = $request->get('fenceid');
        $process = $request->get('process');
        $fence = Geofence::find($request->get('fenceid'));
        if ($process == "false") {
            $fence->active = 0;
        } else {
            $fence->active = 1;
        }

        $fence->save();
    }

    public function editFence(Request $request)
    {
        $whichfence = $request->get('whichfence');
        $title = $request->get('title');
        $address = $request->get('address');
        $visit_count = $request->get('visit_count');
        $latitiude = $request->get('latitiude');
        $longitude = $request->get('longitude');
        $radius = $request->get('radius');
        $visitsfence = $request->get('visitsfence');
        $contractual_visit = $request->get('contractual_visit');

        $fencestatus = $request->get('fencestatus');

        $customer_id = $request->get('customer_id');
        $customerdetail = Customer::select('contractual_visit_unit')->where('id', $customer_id)->first();
        $unit = $customerdetail->contractual_visit_unit;

        if ($whichfence > 0) {
            $updatefence = Geofence::find($whichfence);

            CustomerFenceLogs::firstOrCreate(['fenceid' => $whichfence, 'unit' => $unit]);
        } else {
            $updatefence = new Geofence;
            $updatefence->unit = $unit;
        }

        if ($fencestatus > 0) {
            $fencestatus = 1;
        } else {
            $fencestatus = 0;
        }

        $updatefence->customer_id = $customer_id;
        $updatefence->title = $title;
        $updatefence->address = $address;
        $updatefence->visit_count = $visit_count;
        $updatefence->geo_lat = $latitiude;
        $updatefence->geo_lon = $longitude;
        $updatefence->geo_rad = $radius;
        $updatefence->visit_count = $visitsfence;
        $updatefence->contractual_visit = $contractual_visit;
        $updatefence->unit = $unit;
        $updatefence->active = $fencestatus;

        $updatefence->created_by = Auth::user()->id;
        $updatefence->save();
        //echo $id = $updatefence->id;

    }

    public function getFenceListArray(Request $request)
    {
        $fencelist = Geofence::where('customer_id', $request->get('customer_id'))->get();
        $fencearray = [];
        $i = 0;
        foreach ($fencelist as $fence) {
            $fencearray[$i] = [$fence->id, $fence->title, $fence->address, $fence->geo_lat, $fence->geo_lon, $fence->geo_rad, $fence->visit_count, $fence->created_by, $fence->contractual_visit];
            $i++;
        }
        return json_encode($fencearray, true);
    }

    public function removeFenceList(Request $request)
    {
        $fencequery = Geofence::find($request->get('fenceid'))->delete();
        if ($fencequery) {
            echo "Deleted";
        } else {
            echo "Not deleted";
        }
    }
    /**
     * Get Customer shifts
     * @param  $customerid
     * @return json
     */
    public function getCustomershifts(Request $request)
    {
        $customer = $request->get('customer');
        $customershifts = CustomerShifts::where('customer_id', $customer)->get();
        $returnarray = [];
        $i = 0;

        foreach ($customershifts as $customershift) {
            $returnarray[$i]["id"] = $customershift->id;
            $returnarray[$i]["customer_id"] = $customershift->customer_id;
            $returnarray[$i]["shiftname"] = $customershift->shiftname;
            $returnarray[$i]["starttime"] = $customershift->starttime;
            $returnarray[$i]["endtime"] = $customershift->endtime;
            $loopstarttime = date("Y-m-d") . " " . $customershift->starttime;
            $loopendtime = date("Y-m-d") . " " . $customershift->endtime;

            $returnarray[$i]["starthour"] = date('h', strtotime($loopstarttime));

            $returnarray[$i]["startminute"] = date('i', strtotime($loopstarttime));
            $returnarray[$i]["startmeredian"] = date('A', strtotime($loopstarttime));

            $returnarray[$i]["endhour"] = date('h', strtotime($loopendtime));
            $returnarray[$i]["endminute"] = date('i', strtotime($loopendtime));
            $returnarray[$i]["endmeredian"] = date('A', strtotime($loopendtime));

            $i++;
        }
        return json_encode($returnarray, true);
    }
    /**
     * Get single customer details
     * @param  $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->customerRepository->getSingleCustomer($id));
    }

    /**
     * Function to get customers name and id list based on customer type
     * @return object
     */
    public function getCustomersNameIdList($customer_type)
    {
        return $this->customerRepository->getCustomersNameList($customer_type);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        //dd($request->all());
        try {
            DB::beginTransaction();
            $customer = $this->customerRepository->storeCustomer($request->all());
            $roomIds = $this->customerRoomModel->where('customer_id', $customer['id'])->pluck('id')->toArray();
            if ($customer["geo_location_lat"] == null || $customer["geo_location_long"] == null) {
                return response()->json(['success' => false, "message" => "The given data was invalid.", "errors" => ["postal_code" => ["Given  postal code is not recognized"]]], 422);
            }
            $result = null;
            try {
                if ($customer['motion_sensor_enabled'] == true) {
                    //                    $this->sensorActiveSettingRepository->updateSensorAsEnabled($roomIds);
                    //                    $this->sensorActiveSettingRepository->updateLambdaClient(null,$roomIds);
                } else {
                    //                    $this->sensorActiveSettingRepository->updateSensorAsDisabled($roomIds);
                    //                    $this->sensorActiveSettingRepository->updateLambdaClient(null,$roomIds);
                }
            } catch (\Exception $e) {
                $result = "Error in Lambda";
            }
            $customer->save();
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function guardroutes(Request $request)
    {
        $customers = $this->customerRepository->getCustomersNameList(null);
        $routeslist = $this->guardroute->getAll();
        return view('admin::customer.guardroutes', compact('customers', 'routeslist'));
    }

    public function guardroutesdata()
    {
        return datatables()->of($this->guardroute->select('id', 'routename', 'description', 'status')->get())->addIndexColumn()->toJson();
    }

    public function guardroutesdatadetailed(Request $request)
    {
        return "1";
    }

    public function postguardroutes(Request $request)
    {
        $flag = $request->get('editflag');
        $routename = $request->get('routename');
        $routedesc = $request->get('routedesc');
        $status = $request->get('status');
        if ($flag == 0) {
            $this->guardroute->store($routename, $routedesc, \Auth::user()->id, true);
        }
    }

    public function customerfences(Request $request)
    {
        $customers = $this->customerRepository->getCustomersNameList(null);

        return view('admin::customer.customerfences', compact('customers'));
    }
    /**
     * Update a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateLatLong(Request $request)
    {
        try {
            DB::beginTransaction();
            $update_customer = $this->customerRepository->updateCustomerLatLong($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $experinceLookup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $customer_delete = $this->customerRepository->destroyCustomer($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Import data from excel into DB
     * @param  UploadRequest $request
     * @return redirect
     */
    public function customerImport(ImportRequest $request)
    {
        $import = $this->customerRepository->customerExcelImport($request);
        return redirect(route('customer'))->with('customer-updated', __($import));
    }

    /**
     * Function to get customers name and id list based on customer type
     * @return object
     */
    public function getAllocatedUserEmail($userId)
    {
        return $this->customerRepository->getAllocatedUserEmail($userId);
    }

    /**
     * Function to get formatted project Details
     * @param  $request
     * @return array
     */
    public function formattedProjectDetails($customer_id)
    {
        $project_details = $this->customerRepository->getFormattedProjectDetails($customer_id);
        return $project_details;
    }

    public function resetIncidentLogo(Request $request)
    {
        $customer = Customer::find($request->input('customer_id'));
        if (is_object($customer)) {
            if (Storage::disk('public')->exists($customer->incident_report_logo)) {
                Storage::disk('public')->delete($customer->incident_report_logo);
                $customer->incident_report_logo = null;
                $customer->save();
                return response()->json([
                    'success' => true,
                ]);
            }
        }
        return response()->json([
            'success' => false,
        ]);
    }
}
