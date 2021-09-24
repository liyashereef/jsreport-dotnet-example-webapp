<?php

namespace Modules\Facility\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Facility\Models\Facility;
use Modules\Facility\Models\FacilityService;
use Modules\Facility\Models\FacilityServiceTiming;
use Modules\Facility\Models\FacilityServiceLockdown;
use Modules\Facility\Models\FacilityBooking;
use Modules\Facility\Models\FacilityPrerequisite;
use Modules\Facility\Models\FacilityPolicy;
use Modules\Facility\Models\FacilityUser;
use Modules\Facility\Models\FacilityUserPrerequisiteAnswer;
use Modules\Facility\Http\Requests\FacilityRequest;
use Modules\Facility\Http\Requests\AddServiceLockdown;
use Modules\Facility\Http\Requests\AddServiceTiming;
use Modules\Facility\Http\Requests\Facilityservicerequest;
use Modules\Facility\Http\Requests\FacilityUpdateRequest;
use Modules\Facility\Http\Requests\FacilityServiceUpdateRequest;
use Modules\Facility\Repositories\FacilityRepository;
use Modules\Facility\Repositories\FacilityServiceDataRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use App\Services\HelperService;
use Exception;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    protected $facilityrepository, $helperService, $facilityservicedatarepository;
    public function __construct(
        FacilityRepository $facilityrepository,
        CustomerRepository $customerrepository,
        CustomerEmployeeAllocationRepository $customeremployeeallocationrepository,
        HelperService $helperService,
        FacilityServiceDataRepository $facilityservicedatarepository
    ) {
        $this->facilityrepository = $facilityrepository;
        $this->customerrepository = $customerrepository;
        $this->customeremployeeallocationrepository = $customeremployeeallocationrepository;
        $this->helperService = $helperService;
        $this->facilityservicedatarepository = $facilityservicedatarepository;
    }
    public function index()
    {
        if (\Auth::guard('facilityuser')->user()) {
            $userid = \Auth::guard('facilityuser')->user()->id;
        } else {
            $userid = 0;
        }

        return view('facility::index', compact('userid'));
    }

    public function viewFacilities(Request $request)
    {
        $facilities = collect([]);
        $customers = $this->customeremployeeallocationrepository->getUserallocatedcustomers(true)->pluck('customer.id')->toArray();
        if (\Auth::user()->hasAnyPermission(['admin', 'super_admin', 'view_all_customer_facility'])) {
            $facilities = Facility::get();
        } else if (\Auth::user()->hasAnyPermission(['view_allocated_customer_facility'])) {

            $facilities = Facility::whereIn('customer_id', $customers)->get();
        }
        return view('facility::facilitymaster', compact('facilities', 'customers'));
    }

    public function addFacility(Request $request)
    {
        $customersarray = [];
        if (\Auth::user()->hasAnyPermission(['admin', 'super_admin', 'manage_all_customer_facility'])) {
            $customers = $this->customerrepository->getCustomerList();
            foreach ($customers as $value) {
                if ($value->facility_booking == 1) {
                    $customersarray[$value->id]["client_name"] = $value->client_name;
                    $customersarray[$value->id]["project_number"] = $value->project_number;
                }
            }
        } else if (\Auth::user()->hasAnyPermission(['manage_allocated_customer_facility'])) {
            $customers = $this->customeremployeeallocationrepository->getUserallocatedcustomers(true);

            foreach ($customers as $value) {
                if ($value->customer->facility_booking == 1) {
                    $customersarray[$value->id]["client_name"] = $value->customer->client_name;
                    $customersarray[$value->id]["project_number"] = $value->customer->project_number;
                }
            }
        }


        return view('facility::addfacility', compact('customers', 'customersarray'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username'   => 'required',
            'password' => 'required|min:6'
        ]);
        $content["code"] = 406;
        $content["success"] = false;
        $content["message"] = "Invalid user";
        if (\Auth::guard('facilityuser')->attempt(['username' => $request->username, 'password' => $request->password], true)) {
            $content["code"] = 200;
            $content["success"] = true;
            $content["message"] = "Welcome " . \Auth::guard('facilityuser')->user()->first_name . " " . \Auth::guard('facilityuser')->user()->last_name;
            return json_encode($content, true);
        } else {
            return json_encode($content, true);
        }
    }
    public function removeFacility(Request $request)
    {
        $facility = $request->id;
        $model_ids = [];
        $facilities = Facility::with('facilityservices')->find($facility);

        if (isset($facilities->facilityservices)) {
            foreach ($facilities->facilityservices as $facservice) {
                array_push($model_ids, $facservice->id);
            }
        }
        $bookings = FacilityBooking::where([
            'model_type' => "Modules\Facility\Models\FacilityService",
            "model_id" => $facility
        ])->whereDate('booking_date_start', '>=', date("Y-m-d"))->count();

        $servicebookings = FacilityBooking::where(['model_type' => "Modules\Facility\Models\FacilityService"])
            ->whereIn("model_id", $model_ids)->whereDate('booking_date_start', '>=', date("Y-m-d"))->count();
        if ($servicebookings < 1 && $bookings < 1) {
            $facremove = Facility::find($facility)->delete();
            if ($facremove) {

                $content["code"] = 200;
                $content["message"] = "Facility removed successfully";
                $content["success"] = "success";
            } else {
                $content["code"] = 406;
                $content["message"] = "Data error";
                $content["success"] = "warning";
            }
        } else {
            $content["code"] = 406;
            $content["message"] = "Please remove booking prior removal";
            $content["success"] = "warning";
        }
        return json_encode($content, true);
    }



    public function viewFacilityservice(Request $request)
    {

        $facilityid = $request->id;
        $facilityservice = FacilityService::where('facility_id', $facilityid)->get();
        $mainfacilitydata = $this->facilityservicedatarepository->getFacilityData($facilityid);

        return view('facility::facilityservicemaster', compact('facilityservice', 'facilityid', 'mainfacilitydata'));
    }

    public function addFacilityservice(Request $request)
    {
        $facilityid = $request->id;
        return view('facility::addfacilityservice', compact('facilityid'));
    }

    public function saveFacilityservice(FacilityServiceRequest $request)
    {
        $content = [];
        $facilityarray["service"] = $request->facility;
        if ($request->description == "") {
            //$facilityarray["description"] = $request->facility;
        } else {
            $facilityarray["description"] = $request->description;
        }

        $facilityarray["facility_id"] = $request->facilityid;
        $facilityarray["created_by"] = \Auth::user()->id;
        $facilityarray["created_by"] = \Auth::user()->id;
        $facilityarray["created_at"] = date("Y-m-d H:i");

        try {
            \DB::beginTransaction();

            $facilityservice = FacilityService::create($facilityarray);
            if ($facilityservice) {
                $this->facilityrepository->createFacilityservice($facilityservice->id, $request);
                $content["code"] = 200;
                $content["message"] = "Service created successfully";
                $content["success"] = "success";
            } else {
                $content["code"] = 406;
                $content["message"] = "Data error";
                $content["success"] = "warning";
            }
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            $content["code"] = 406;
            $content["message"] = "Data error" . $th;
            $content["success"] = "warning";
            //throw $th;
        }
        return json_encode($content, true);
    }

    public function updateFacilityservice(FacilityServiceUpdaterequest $request)
    {
        $content = [];
        $facilityarray["service"] = $request->facility;
        if ($request->description == "") {
            $facilityarray["description"] = $request->facility;
        } else {
            $facilityarray["description"] = $request->description;
        }
        $facilityarray["restrict_booking"] = $request->restrict_booking;
        $facilityarray["active"] = $request->active;

        $facilityarray["updated_by"] = \Auth::user()->id;
        $facilityarray["updated_at"] = date("Y-m-d H:i");

        try {

            $model_type = "Modules\Facility\Models\FacilityService";
            $model_id =  $request->facilityid;
            $booking = FacilityBooking::where(["model_type" => $model_type, ["booking_date_start", '>=', date("Y-m-d H:i")]])
                ->where('model_id', $model_id)
                ->count();
            if ($booking == 0) {
                \DB::beginTransaction();
                $facilityservice = FacilityService::find($request->facilityid);

                $facilityservice->service = $request->facility;
                $facilityservice->description = $request->description;
                $facilityservice->restrict_booking = $request->restrict_booking;
                $facilityservice->active = $request->active;
                $facilityservice->updated_by = \Auth::user()->id;

                $facservice = $facilityservice->save();
                if ($facservice) {
                    $this->facilityrepository->updateFacilityservice($facilityservice->id, $request);
                    $content["code"] = 200;
                    $content["message"] = "Service updated successfully";
                    $content["success"] = "success";
                } else {
                    $content["code"] = 406;
                    $content["message"] = "Data error";
                    $content["success"] = "warning";
                }
                \DB::commit();
            } else {
                $content["code"] = 406;
                $content["message"] = "Please remove future bookings";
                $content["success"] = "warning";
            }
        } catch (\Throwable $th) {
            throw $th;
            \DB::rollback();
            $content["code"] = 406;
            $content["message"] = "Data error" . $th;
            $content["success"] = "warning";
            //throw $th;
        }
        return json_encode($content, true);
    }



    public function saveFacilityservicelockdown(AddServiceLockdown $request)
    {
        $model_type = "Modules\Facility\Models\Facility";
        return $this->facilityrepository->saveLockdown($request, $model_type);
    }

    public function saveFacilityprerequisite(Request $request)
    {
        $content["code"] = 406;
        $content["message"] = "Data error";
        $content["success"] = "warning";
        $edit_requisite_id = $request->edit_requisite_id;
        $facilityid = $request->service_id;
        $requisite = $request->requisite;
        if ($edit_requisite_id == "") {
            $lastfacility = FacilityPrerequisite::where("facility_id", $facilityid)->orderBy('order', 'desc')->first();
            $order = 1;
            if ($lastfacility) {
                $order = $lastfacility->order;
                $order = $order + 1;
            } else {
            }

            $facilitypreq = FacilityPrerequisite::create(["facility_id" => $facilityid, "requisite" => $requisite, "created_by" => \Auth::user()->id, "order" => $order]);
            if ($facilitypreq->id > 0) {
                $content["code"] = 200;
                $content["message"] = "Prerequisite added successfully";
                $content["success"] = "success";
            }
        } else {

            $facility = FacilityPrerequisite::find($edit_requisite_id);
            $facility->requisite = $requisite;
            $facility->updated_by = \Auth::user()->id;
            $facility->save();
            if ($facility) {
                $content["code"] = 200;
                $content["message"] = "Prerequisite updated successfully";
                $content["success"] = "success";
            }
        }


        return json_encode($content, true);
    }

    public function saveFacilitypolicy(Request $request)
    {
        $content["code"] = 406;
        $content["message"] = "Data error";
        $content["success"] = "warning";
        $edit_policy_id = $request->edit_policy_id;
        $facilityid = $request->service_id;
        $policy = $request->policytext;
        $polcount = FacilityPolicy::where("facility_id", $facilityid)->count();
        if ($polcount < 8) {
            if ($edit_policy_id == "") {
                $lastfacility = FacilityPolicy::where("facility_id", $facilityid)->orderBy('order', 'desc')->first();
                $order = 1;
                if ($lastfacility) {
                    $order = $lastfacility->order;
                    $order = $order + 1;
                } else {
                }

                $facilitypreq = FacilityPolicy::create(["facility_id" => $facilityid, "policy" => $policy, "created_by" => \Auth::user()->id, "order" => $order]);
                if ($facilitypreq->id > 0) {
                    $content["code"] = 200;
                    $content["message"] = "Facility policy added successfully";
                    $content["success"] = "success";
                }
            } else {

                $facility = FacilityPolicy::find($edit_policy_id);
                $facility->policy = $policy;
                $facility->updated_by = \Auth::user()->id;
                $facility->save();
                if ($facility) {
                    $content["code"] = 200;
                    $content["message"] = "Facility policy updated successfully";
                    $content["success"] = "success";
                }
            }
        } else {
            $content["code"] = 406;
            $content["message"] = "Exceeded maximum number of policies";
            $content["success"] = "warning";
        }



        return json_encode($content, true);
    }

    public function removeFacilitypolicy(Request $request)
    {
        $model_id = $request->model_id;
        $facilityremove = FacilityPolicy::find($model_id)->delete();
        if ($facilityremove) {
            $content["code"] = 200;
            $content["success"] = true;
            $content["message"] = "Pre requisite removed";
        } else {
            $content["code"] = 406;
            $content["success"] = false;
            $content["message"] = "Sytem issue";
        }
        return json_encode($content, true);
    }

    public function saveFacilityservicetiming(AddServiceTiming $request)
    {
        $model_type = "Modules\Facility\Models\Facility";
        return $this->facilityrepository->saveNewtiming($request, $model_type);
    }
    public function removeServicetiming(Request $request)
    {
        $model_id = $request->model_id;
        $model_type = $request->model_type;
        $facility = $request->facility;
        if ($model_type == "facility") {
            $model_type = "Modules\Facility\Models\Facility";
        }
        $edit = FacilityServiceTiming::find($request->model_id);
        $edit_start_time = $edit->start_time;
        $edit_end_time = $edit->end_time;
        $weekend = $edit->weekend_timing;



        $bookings = FacilityBooking::where("model_id", $facility)
            ->when($weekend > 0, function ($q) {
                // return $q->whereraw('DAYNAME(booking_date_start)="Saturday"
                // and DAYNAME(booking_date_start)="Sunday" and isnull(deleted_at)');
            })
            ->whereRaw("DATE(booking_date_start)>='" . date("Y-m-d") . "'  and
            ((TIME(booking_date_start) between TIME('" . $edit_start_time . "')
            and TIME('" . $edit_end_time . "')) or
            (TIME(booking_date_end) between TIME('" . $edit_start_time . "')
            and TIME('" . $edit_end_time . "')) or (TIME(booking_date_start)='" . $edit_start_time . "')
            or (TIME(booking_date_end)='" . $edit_start_time . "')
            or (TIME(booking_date_start)='" . $edit_end_time . "')
            or (TIME(booking_date_end)='" . $edit_end_time . "') )")

            ->where("model_type", "Modules\Facility\Models\Facility")

            ->count();

        $services = FacilityService::select("id")->where("facility_id", $facility)->get()->pluck("id")->toArray();

        if (count($services) > 0) {
            $servicebooking = FacilityBooking::
                // whereDate('booking_date_start','>=',date("Y-m-d"))
                whereIn("model_id", $services)
                ->when($weekend > 0, function ($q) {
                    // return $q->whereraw('DAYNAME(booking_date_start)="Saturday"
                    // and DAYNAME(booking_date_start)="Sunday" and isnull(deleted_at)');
                })
                ->whereRaw("DATE(booking_date_start)>='" . date("Y-m-d") . "'  and
            ((TIME(booking_date_start) between TIME('" . $edit_start_time . "')
            and TIME('" . $edit_end_time . "')) or
            (TIME(booking_date_end) between TIME('" . $edit_start_time . "')
            and TIME('" . $edit_end_time . "')) or (TIME(booking_date_start)='" . $edit_start_time . "')
            or (TIME(booking_date_end)='" . $edit_start_time . "')
            or (TIME(booking_date_start)='" . $edit_end_time . "')
            or (TIME(booking_date_end)='" . $edit_end_time . "') )")
                ->where("model_type", "Modules\Facility\Models\FacilityService")
                ->count();
            $bookings = $bookings + $servicebooking;
        }


        if ($bookings > 0) {
            $content["code"] = 406;
            $content["success"] = false;
            $content["message"] = "Please remove booking prior remove timing";
        } else {
            $facilityremove = FacilityServiceTiming::find($model_id)->delete();
            if ($facilityremove) {
                $content["code"] = 200;
                $content["success"] = true;
                $content["message"] = "Timing removed";
            } else {
                $content["code"] = 406;
                $content["success"] = false;
                $content["message"] = "System issue";
            }
        }
        return json_encode($content, true);
    }

    public function removeFacilityservicelockdown(Request $request)
    {
        $model_id = $request->model_id;
        $model_type = $request->model_type;
        if ($model_type == "facility") {
            $model_type = "Modules\Facility\Models\Facility";
        }

        $facilityremove = FacilityServiceLockdown::find($model_id)->delete();
        if ($facilityremove) {
            $content["code"] = 200;
            $content["success"] = true;
            $content["message"] = "Lockdown Period removed Successfully";
        } else {
            $content["code"] = 406;
            $content["success"] = false;
            $content["message"] = "Sytem issue";
        }
        return json_encode($content, true);
    }

    public function removeFacilityprerequisite(Request $request)
    {
        $model_id = $request->model_id;
        $exist = FacilityUserPrerequisiteAnswer::where('prereq_id', $model_id)->count();
        if ($exist > 0) {
            $content["code"] = 406;
            $content["success"] = false;
            $content["message"] = "Data exists";
        } else {
            $facilityremove = Facilityprerequisite::find($model_id)->delete();
            if ($facilityremove) {
                $content["code"] = 200;
                $content["success"] = true;
                $content["message"] = "Pre requisite removed";
            } else {
                $content["code"] = 406;
                $content["success"] = false;
                $content["message"] = "Sytem issue";
            }
        }

        return json_encode($content, true);
    }

    public function editFacilityservice(Request $request)
    {
        $facilityid = $request->id;
        $data = FacilityService::with(["facilitydata" => function ($q) {
            return $q->whereNull("expiry_date");
        }, "facilityslot" => function ($q) {
            return $q->whereNull("expiry_date");
        }])->find($facilityid);
        $slot_interval = null;
        $expiry_date = null;
        $facilitydata = ($data->facilitydata);
        $facilityslot = $data->facilityslot->toArray();

        $slot_interval = $facilityslot[0]["slot_interval"];
        $expiry_date = $facilityslot[0]["expiry_date"];
        $booking_window =  $data->getFacility->facilitydata->booking_window;


        return view('facility::partials.editfacilityservice', compact('facilityid', 'data', 'facilitydata', 'facilitytiming', 'slot_interval', 'expiry_date', 'booking_window'));
    }




    public function removeFacilityservice(Request $request)
    {
        $facility = $request->id;
        $bookings = FacilityBooking::where([
            'model_type' => "Modules\Facility\Models\FacilityService",
            "model_id" => $facility
        ])->whereDate('booking_date_start', '>=', date("Y-m-d"))->count();

        if ($bookings < 1) {
            $facremove = FacilityService::find($facility)->delete();
            if ($facremove) {

                $content["code"] = 200;
                $content["message"] = "Facility service removed successfully";
                $content["success"] = "success";
            }
        } else {
            $content["code"] = 406;
            $content["message"] = "Please remove the booking prior removal";
            $content["success"] = "warning";
        }
        return json_encode($content, true);
    }

    public function saveFacilitysignout(FacilityRequest $request)
    {


        $validator = \Validator::make($request->all(), [
            'facility' => 'required',
        ]);

        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator);
        }
        $ssf = 0;
        if ($request->single_service_facility == "yes") {
            $ssf = 1;
        }
        $facilityarray["facility"] = $request->facility;
        $facilityarray["description"] = $request->description;
        $facilityarray["postal_code"] = $request->postal_code;
        $facilityarray["single_service_facility"] = $ssf;
        $facilityarray["customer_id"] = $request->customer_id;
        $facilityarray["created_by"] = \Auth::user()->id;

        $facilityarray["created_at"] = date("Y-m-d H:i");
        \DB::beginTransaction();
        try {
            $facility = Facility::create($facilityarray);
            \DB::commit();
            if ($facility) {
                $this->facilityrepository->createFacility($facility->id, $request);

                $customer_users = FacilityUser::where("customer_id", $request->customer_id)->get()->pluck("id")->toArray();
                foreach ($customer_users as $key => $value) {

                    $this->facilityrepository->associateUserWithnewFacilities($value, $request->customer_id, $facility->id);
                }


                return redirect("/cbs/facility");
            }
        } catch (\Throwable $th) {
            throw $th;
            \DB::rollback();
            return redirect("/cbs/facility");
        }






        //dd($request->all());
    }
    public function editfacilities(request $request)
    {
        $id = $request->id;
        $customersarray = [];
        if (\Auth::user()->hasAnyPermission(['admin', 'super_admin'])) {
            $customers = $this->customerrepository->getCustomerList();
            foreach ($customers as $value) {

                $customersarray[$value->id]["client_name"] = $value->client_name;
                $customersarray[$value->id]["project_number"] = $value->project_number;
            }
        } else {
            $customers = $this->customeremployeeallocationrepository->getUserallocatedcustomers(true);

            foreach ($customers as $value) {

                $customersarray[$value->customer->id]["client_name"] = $value->customer->client_name;
                $customersarray[$value->customer->id]["project_number"] = $value->customer->project_number;
            }
        }
        $error = \Session::get('message');
        $facilitypolicy = FacilityPolicy::where("facility_id", $id)->get();
        $facilityprequisite = FacilityPrerequisite::where("facility_id", $id)->get();

        $data = Facility::with([
            'facilitydata' => function ($q) {
                return $q->whereNull("expiry_date");
            }, 'facilitytiming' => function ($q) {
                return $q->orderByRaw("weekend_timing asc, start_date asc,start_time asc");
            },
            'facilityslot' => function ($q) {
                return $q->whereNull("expiry_date");
            }, 'facilityservicelockdown'
        ])->find($id);

        $facilitydata = $data->facilitydata;
        $facilitytiming = $data->facilitytiming;
        $facilityslot = $data->facilityslot->toArray();
        $facilityservicelockdown = $data->facilityservicelockdown;
        try {
            $slot_interval = $facilityslot[0]["slot_interval"];
            $expiry_date = $facilityslot[0]["expiry_date"];
        } catch (\Throwable $th) {
            $slot_interval = 0;
            $expiry_date = "";
        }

        $booking_window = $facilitydata->booking_window;

        return view('facility::editfacility', compact(
            'id',
            'data',
            'facilitydata',
            'facilitytiming',
            'customersarray',
            'slot_interval',
            'booking_window',
            'customers',
            'error',
            'facilityprequisite',
            'facilitypolicy',
            'facilityservicelockdown'
        ));
    }

    public function editFacilitysignout(FacilityUpdateRequest $request)
    {
        $content = [];
        $facilityid = $request->id;
        $facilityservice = Facility::with('facilityservices')->find($request->id);
        if ($facilityservice->single_service_facility == 1) {
            $model_type = "Modules\Facility\Models\Facility";
            $model_id =  $facilityid;
            $booking = FacilityBooking::where(["model_type" => $model_type, ["booking_date_start", '>=', date("Y-m-d H:i")]])
                ->where('model_id', $model_id)
                ->count();
        } else {
            $model_type = "Modules\Facility\Models\FacilityService";
            $model_id =  $facilityservice->facilityservices->pluck('id')->toArray();
            $booking = FacilityBooking::where(["model_type" => $model_type, ["booking_date_start", '>=', date("Y-m-d H:i")]])
                ->whereIn('model_id', $model_id)
                ->count();
        }

        if ($booking == 0) {
            $facilityarray["facility"] = $request->facility;
            $facilityarray["description"] = $request->description;

            $facilityarray["updated_by"] = \Auth::user()->id;
            $facilityarray["updated_at"] = date("Y-m-d H:i");
            $single_service_facility = 0;
            \DB::beginTransaction();
            try {
                if ($request->single_service_facility == "yes") {
                    $single_service_facility = 1;
                }
                $restrict_booking = 0;
                if ($request->restrict_booking == "yes") {
                    $restrict_booking = 1;
                }

                $active = 0;
                if ($request->active == "yes") {
                    $active = 1;
                }

                $facilityservice->facility = $request->facility;
                $facilityservice->description = $request->description;
                $facilityservice->single_service_facility = $single_service_facility;
                $facilityservice->restrict_booking = $restrict_booking;
                $facilityservice->active = $active;
                $facilityservice->updated_by = \Auth::user()->id;

                $facservice = $facilityservice->save();


                if ($facservice) {
                    $this->facilityrepository->updateFacility($facilityservice->id, $request);
                    $content["code"] = 200;
                    $content["message"] = "Facility updated successfully";
                    $content["success"] = "success";
                } else {
                    $content["code"] = 406;
                    $content["message"] = "Data error";
                    $content["success"] = "warning";
                }
                \DB::commit();
            } catch (Exception $e) {
                \DB::rollback();
                throw $e;
                $content["code"] = 406;
                $content["message"] = "Data error" . $e;
                $content["success"] = "warning";
            }
        } else {
            $content["code"] = 406;
            $content["message"] = "Remove future booking prior edit";
            $content["success"] = "warning";
        }
        //$request->session()->flash('message', $content);
        //return redirect("cbs/editfacilities/".$facilityservice->id);
        return json_encode($content, true);
    }

    public function logout()
    {
        if (\Auth::guard('facilityuser')->logout()) {
            return redirect('cbs');
        } else {
            return redirect('cbs');
        }
        return redirect('cbs');
    }
}
