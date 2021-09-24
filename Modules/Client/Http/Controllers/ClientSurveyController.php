<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Client\Models\ClientSurvey;
use Modules\Client\Repositories\ClientSurveyRepository;;

use Modules\Admin\Repositories\UserRepository;
use Auth;
use App\Services\HelperService;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;

use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Contracts\Repositories\ContractsRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Models\EmployeeRatingLookup;

class ClientSurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

    protected $customeremployeeallocationrepository;
    protected $contractsrepository;
    protected $payperiodrepository;
    protected $clientSurveyrepository;
    protected $userRepository;
    public function __construct(
        CustomerEmployeeAllocationRepository $customeremployeeallocationrepository,
        ContractsRepository $contractsrepository,
        PayPeriodRepository $payperiodrepository,
        ClientSurveyRepository $clientSurveyrepository,
        UserRepository $userRepository
    ) {
        $this->customeremployeeallocationrepository = $customeremployeeallocationrepository;
        $this->contractsrepository = $contractsrepository;
        $this->payperiodrepository = $payperiodrepository;
        $this->clientSurveyrepository = $clientSurveyrepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $permission = "";
        $permissionChart = "";
        $clientAllocation = [];
        $areaManagers = [];
        if (\Auth::user()->hasAnyPermission(["super_admin", "view_all_clientsurvey"])) {
            $permission = "all";
            $areaManagers = CustomerEmployeeAllocation::with('user')
                ->whereHas("areaManager")->get();
        } else if (\Auth::user()->hasAnyPermission(["add_allocated_clientsurvey"])) {
            $clientAllocation = $this->customeremployeeallocationrepository->getAllocatedCustomerId([\Auth::user()->id]);
            $areaManagers = CustomerEmployeeAllocation::with(['user' => function ($q) {
                return $q->orderBy("first_name", "asc");
            }])
                ->whereIn("customer_id", $clientAllocation)
                ->whereHas("areaManager")
                ->whereHas("user", function ($q) {
                    return $q->orderBy("first_name", "asc");
                })->get();
        }
        $aremanagersarray = [];
        foreach ($areaManagers as $rmanager) {
            $aremanagersarray[$rmanager->user->id] = $rmanager->user->getFullNameAttribute();
        }


        if (\Auth::user()->hasAnyPermission(["add_all_clientsurvey"])) {
            $permissionChart = "all";
        } else if (\Auth::user()->hasAnyPermission(["view_allocated_clientsurvey"])) {
            $clientAllocationView = $this->customeremployeeallocationrepository->getAllocatedCustomerId([\Auth::user()->id]);
        }
        $employeeratinglookup = EmployeeRatingLookup::all()->sortBy("score");
        $clients = Customer::select('id', 'client_name', 'project_number')->when(
            $permission != "all",
            function ($q) use ($clientAllocation) {
                return $q->whereIn("id", $clientAllocation);
            }
        )->orderBy("client_name", "asc")->get();
        $i = 0;
        foreach ($clients as $client) {
            $clientarray[$i]["id"] = $client->id;
            $clientarray[$i]["parent_client_id"] = 0;
            $clientarray[$i]["subs"] = [];
            $clientarray[$i]["title"] = $client->project_number . " - " . $client->client_name;
            $i++;
        }
        $clientarray = json_encode($clientarray, true);
        $permissionaddclients = Customer::select('id', 'client_name', 'project_number')->when(
            $permissionChart != "all",
            function ($q) use ($clientAllocation) {
                return $q->whereIn("id", $clientAllocation);
            }
        )->orderBy("client_name", "asc")->get();
        $chartclients = Customer::select('id', 'client_name')->when(
            $permission != "all",
            function ($q) use ($clientAllocation) {
                return $q->whereIn("id", $clientAllocation);
            }
        )->orderBy("client_name", "asc")->get();
        asort($aremanagersarray);
        return view('client::clientsurvey', compact(
            "clients",
            "permissionaddclients",
            "clientarray",
            "employeeratinglookup",
            "areaManagers",
            "aremanagersarray"
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {

        return view('client::create');
    }

    public function getSurveyData(Request $request)
    {

        $permission = "";
        $time = strtotime("-1 year", time());
        if ($request->has("startdate")) {
            $startdate = $request->get("startdate");
        } else {
            $startdate = date("Y-m-d", $time);
        }
        $startdate = date('Y-m-d', strtotime($startdate . "-1 days"));

        if ($request->has("enddate")) {
            $enddate = $request->get("enddate");
        } else {
            $enddate = date("Y-m-d", $time);
        }
        $enddate = date('Y-m-d', strtotime($enddate . "+1 days"));

        $clientAllocation = [];
        if (\Auth::user()->hasAnyPermission(["super_admin", "view_all_clientsurvey"])) {
            $permission = "all";
        } else if (\Auth::user()->hasAnyPermission(["view_allocated_clientsurvey"])) {
            $clientAllocation = $this->customeremployeeallocationrepository
                ->getAllocatedCustomerId([\Auth::user()->id]);
        }
        $datearray = [];
        $payperiodids = [];
        $payperiods = $this->payperiodrepository->getPayperiodRangeAll($startdate, $enddate);
        foreach ($payperiods as $payp) {
            array_push($payperiodids, $payp->id);
            if (!in_array($payp->short_name, $datearray)) {
                array_push($datearray, $payp->short_name);
            }
        }
        try {
            $data = ClientSurvey::select("*")
                ->addSelect(\DB::raw("(select concat_ws('-',project_number,client_name) from customers where id=client_surveys.client_id) as client"))
                ->addSelect(\DB::raw("(select concat_ws(' ',first_name,last_name) from users where id=client_surveys.client_contact_user) as usercontact"))
                ->with(["customer", "pay_period", "user", "created_user", "user.employee", "user.employee.employeePosition"])
                ->when($permission != "all", function ($q) use ($clientAllocation) {
                    return $q->whereIn("client_id", $clientAllocation);
                })
                // ->whereIn("payperiod", $payperiodids)
                ->whereBetween("created_at", [$startdate, $enddate])
                ->get();

            return datatables()->of($data)->addIndexColumn()->toJson();
        } catch (\Exception $e) {
        }
    }
    public function getClientchart(Request $request)
    {
        $clientid = $request->client;
        return $this->clientSurveyrepository
            ->getClientchartallcustomer($request);
        /*
         if ($clientid > 0) {
            return $this->clientSurveyrepository
                ->getClientchartsinglecustomer($request);
        } else {
            return $this->clientSurveyrepository
                ->getClientchartallcustomer($request);
        }
        */
    }

    public function getClientchartanalyticswidget(Request $request)
    {
        $clientid = $request->get("customer-search");
        return $chartdata = $this->clientSurveyrepository
            ->getClientchartallcustomeranalytics($request);
    }

    public function setClientuserdata(Request $request)
    {
        $payperiod = $this->payperiodrepository->getCurrentPayperiod();
        $savecontent = [
            "client_id" => $request->client_id,
            "client_contact_user" => $request->client_contact_userid,
            "rating" => $request->userrating,
            "notes" => $request->notes,
            "payperiod" => $payperiod->id,
            "created_by" => \Auth::user()->id,
        ];
        if ($payperiod->id > 0) {
            // $saved = ClientSurvey::updateOrCreate(array(
            //     "client_id" => $request->client_id,
            //     "client_contact_user" => $request->client_contact_userid,
            //     "payperiod" => $payperiod->id
            // ), $savecontent);
            $saved = ClientSurvey::create($savecontent);

            if ($saved->id > 0) {
                $content['success'] = "success";
                $content['message'] = 'Survey submitted successfully';
                $content['code'] = 200;
            } else {
                $content['success'] = "warning";
                $content['message'] = 'Please check the input prior submit';
                $content['code'] = 406;
            }
        } else {
            $content['success'] = "warning";
            $content['message'] = 'Current payperiod cannot be empty';
            $content['code'] = 406;
        }

        return json_encode($content);
    }

    public function getClientuserdata(Request $request)
    {
        $clientcontact = "";
        $othercontact = [];
        $clientid = $request->clientid;
        $clients = Customer::find($clientid);
        $otherallocated = collect([]);
        $contractdetails = $this->contractsrepository->getContractsafterdate($clientid, date("Y-m-d"));
        if (isset($contractdetails)) {
            if ($contractdetails->client_contact_information->count() > 0) {
                $otherallocated =  $contractdetails->client_contact_information_without_trash;
            }
        }

        $clientcontact = $clients->contact_person_name;


        foreach ($otherallocated as $others) {
            $othercontact[$others->primary_contact] = $others->contact_name;
        }
        $dataarray[0] = $clientcontact;
        $dataarray[1] = $othercontact;
        return response()->json($dataarray);
    }
}
