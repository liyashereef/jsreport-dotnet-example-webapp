<?php

namespace Modules\Client\Http\Controllers;

use App\Services\HelperService;
use \Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Log;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\VisitorLogCustomerTemplateAllocation;
use Modules\Admin\Models\VisitorLogTypeLookup;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\VisitorLogTemplateRepository;
use Modules\Client\Http\Requests\VisitorLogRequest;
use Modules\Client\Models\VisitorLogDetails;
use Modules\Client\Models\VisitorLogMeta;
use Modules\Client\Repositories\VisitorLogRepository;
use Modules\Timetracker\Repositories\ImageRepository;
use Session;
use View;

class VisitorLogController extends Controller
{

    protected $visitorLogRepository, $helperService;
    private $directory_seperator;

    public function __construct(
        CustomerRepository $customerRepository,
        VisitorLogTemplateRepository $visitorLogTemplateRepository,
        VisitorLogRepository $visitorLogRepository,
        HelperService $helperService,
        ImageRepository $imageRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->visitorLogTemplateRepository = $visitorLogTemplateRepository;
        $this->visitorLogRepository = $visitorLogRepository;
        $this->helperService = $helperService;
        $this->imageRepository = $imageRepository;
        $this->directory_seperator = "/";
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        $customer_id = Session::get('default_customer');
        if (\Auth::user()->can('view_all_visitorlog')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('all');
        } else if (\Auth::user()->can('view_allocated_visitorlog')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('allocated');
        } else {
            $project_list = $this->customerRepository->getProjectsDropdownList('all');
        }
        $today = date('Y-m-d');
        $type_list = VisitorLogTypeLookup::pluck('type', 'id')->toArray();
        if ($customer_id) {
            $customer = Customer::where('id', $customer_id)->first();
            $overstay = $customer->overstay_enabled;
            // $total_visitors_count = VisitorLogDetails::where('customer_id', $customer_id)->whereDate('checkin', date('Y-m-d'))->count();

            $query = $this->visitorLogRepository->getAllVisitorslog($customer_id);
            // $query2 = $this->visitorLogRepository->CheckoutLog($customer_id);
            //  $total_visitors_count = $query->count() + $query2->count();
            $total_visitors_count = $query->count();
            $recently_checkin_type_count = $query->select('visitor_type_id', \DB::raw('count(visitor_type_id) as total'))
                ->with('type')
                // ->rightJoin('visitor_log_type_lookups', 'visitor_log_details.visitor_type_id', '=', 'visitor_log_type_lookups.id')
                ->groupBy('visitor_type_id')
                ->get();
            $recently_checkin_type_count = $recently_checkin_type_count->pluck('total', 'visitor_type_id')->toArray();

            // $recently_checkin_type_count = \DB::select(\DB::raw("select l.type as label,count(v.visitor_type_id) as total,l.id from visitor_log_details v right join visitor_log_type_lookups l on v.visitor_type_id=l.id
            //     AND l.deleted_at IS NULL AND v.customer_id='$customer_id' AND date(v.checkin)='$today'
            //         group by l.type,l.id"));
        } else {
            $total_visitors_count = $overstay = 0;
            $recently_checkin_type_count = [];
        }
        return view('client::visitor-log', compact('customer_id', 'project_list', 'total_visitors_count', 'recently_checkin_type_count', 'overstay', 'type_list'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->session()->put('default_customer', $request->get('customer_id'));
        $customer = Customer::where('id', $request->session()->get('default_customer'))->first();
        $request->session()->put('default_client_name', $customer->client_name);
        return response()->json(array('success' => true, 'default_customer' => $request->session()->get('default_customer'), 'default_client_name' => $request->session()->get('default_client_name')));
    }

    /**
     * Exit session variables set.
     * @return Response
     */

    public function exitSession(Request $request)
    {
        $request->session()->put('default_customer', null);
        $request->session()->put('default_client_name', null);
        $request->session()->put('template_id', null);
        return response()->json(array('success' => true, 'default_customer' => $request->session()->get('default_customer'), 'default_client_name' => $request->session()->get('default_client_name'), 'template_id' => $request->session()->get('template_id')));
    }

    /**
     * Show the specified resource.
     * @return Response
     */

    public function getTemplateList(Request $request)
    {
        //dd($request->get('customer_id'));
        $res = VisitorLogCustomerTemplateAllocation::where('customer_id', $request->get('customer_id'))->with('template')->get();
        return response()->json(array('success' => true, 'data' => $res));
    }

    public function loadVisitorLogForm($template_id)
    {
        if ($template_id) {
            $template_fields = $this->visitorLogTemplateRepository->getCustomerTemplateFields($template_id);
            $template_features = $this->visitorLogTemplateRepository->getCustomerTemplateFeatures($template_id);
            $visitor_type = VisitorLogTypeLookup::get();
            $content = View::make('client::partials.visitor-log-form')
                ->with(compact(['template_fields', 'template_features', 'visitor_type', 'template_id']));

            return response()->json(array('success' => true, 'content' => !empty($content) ? ($content->render()) : 'Something went wrong!!!'));
        } else {

            return response()->json(array('success' => false, 'content' => !empty($content) ? ($content->render()) : 'Something went wrong!!!'));
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */

    public function getCurrentLogDetails(Request $request)
    {
        $query = $this->visitorLogRepository->CurrentLog($request->get('customer_id'));
        $ret = $this->prepareDataForVisitorLog($query->get());
        return datatables()->of($ret)->toJson();
    }

    /**
     * Show the specified resource.
     * @return Response
     */

    public function getCheckoutLogDetails(Request $request)
    {

        $query = $this->visitorLogRepository->CheckoutLog($request->get('customer_id'));
        $rets = $this->prepareDataForVisitorLog($query->get());
        return datatables()->of($rets)->toJson();
    }

    /**
     * Show the specified resource.
     * @return Response
     */

    public function getOvertimeLogDetails(Request $request)
    {

        $customer = Customer::where('id', $request->get('customer_id'))->first();
        $overstay = ($customer != null) ? date("H:i A", strtotime($customer->overstay_time)) : '--';
        $mytime = Carbon::now();
        $curr_time = $mytime->toTimeString();
        $res1 = null;
        if ($curr_time > $overstay) {
            $res1 = $this->visitorLogRepository->CurrentLog($request->get('customer_id'));
        }

        $res = $this->visitorLogRepository->Overstay($request->get('customer_id'), $overstay, $curr_time);
        $result = ($res1 != null) ? ($res1->get()->toBase()->merge($res->get()->toBase())) : $res;
        $rets = $this->prepareDataForVisitorLog($result, $overstay);
        return datatables()->of($rets)->toJson();
    }

    public function prepareDataForVisitorLog($visitor_log_data, $overstay = null)
    {
        $datatable_rows = array();
        foreach ($visitor_log_data as $key => $each_list) {
            $each_row["id"] = $each_list->id;
            $each_row["overstay"] = $overstay != null ? $overstay : '--';
            $each_row["checkin"] = $each_list->checkin ? date('h:i A', strtotime($each_list->checkin)) : '--';
            $each_row["checkout"] = $each_list->checkout ? date('h:i A', strtotime($each_list->checkout)) : '--';
            $each_row["full_name"] = $each_list->first_name . ' ' . (($each_list->last_name) ? $each_list->last_name : '');
            $each_row["type"] = $each_list->type ? $each_list->type->type : '--';
            $each_row["checkin_datetime"] = $each_list->checkin;
            $each_row["checkout_datetime"] = $each_list->checkout ? $each_list->checkout : '--';
            $each_row["picture_file_name"] = $each_list->picture_file_name;
            $each_row["whom_to_visit"] = $each_list->whom_to_visit;
            $each_row["name_of_company"] = $each_list->name_of_company;
            $each_row["qr_added"] = $each_list->qr_added;
            $each_row["check_in_option"] = $each_list->check_in_option;
            $each_row["created_at"] = $each_list->created_at;
            $each_row["updated_at"] = $each_list->updated_at;
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     * Checkout visitor log.
     * @return Response
     */

    public function checkout(Request $request)
    {
        $log_details = VisitorLogDetails::where('id', $request->get('visitorid'))->update(['notes' => $request->get('notes'), 'checkout' => date('Y-m-d H:i:s')]);
        $this->uploadImage($request);
        return response()->json(array('success' => true, 'data' => $log_details));
    }

    public function addVisitorLog(VisitorLogRequest $request)
    {
        $request->session()->put('template_id', $request->get('template_id'));
        try {
            \DB::beginTransaction();
            $visitorLog = $this->visitorLogRepository->store($request);

            \DB::commit();
            return response()->json(array('success' => $this->helperService->returnTrueResponse($visitorLog), 'template_id' => $request->session()->get('template_id')));
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function viewLog($id)
    {
        $template_details = VisitorLogDetails::select([
            'id',
            'template_id', 'customer_id',
            \DB::raw('DATE_FORMAT(checkin, "%H:%i %p") as checkin'), 'visitor_type_id', 'first_name', 'last_name', 'phone', 'email', 'name_of_company', 'whom_to_visit', 'picture_file_name', 'signature_file_name', 'checkout_file_name',
            'created_at', 'updated_at', 'license_number', 'vehicle_reference', 'work_location', 'additional_comments'
        ])->with('type')->where('id', $id)->first();
        $visitorLogMetas = VisitorLogMeta::where('visitor_log_id',$id)->get();
        
        $template_fields = $this->visitorLogTemplateRepository->getCustomerTemplateFields($template_details->template_id);
        $template_features = $this->visitorLogTemplateRepository->getCustomerTemplateFeatures($template_details->template_id);
        $content = View::make('client::partials.visitor-log-view')
            ->with(compact(['template_fields', 'template_features', 'template_details','visitorLogMetas']));

        return response()->json(['success' => true, 'content' => !empty($content) ? ($content->render()) : 'Something went wrong!!!']);
    }

    public function uploadImage(Request $request)
    {
        $filetxt = $request->imagetype == 'signature' ? 'visitor_signature_' : 'visitor_image_';
        switch ($request->imagetype) {
            case 'signature':
                $filetxt = 'visitor_signature_';
                $fieldname = 'signature_file_name';
                break;
            case 'picture':
                $filetxt = 'visitor_image_';
                $fieldname = 'picture_file_name';
                break;
            case 'checkout_signature':
                $filetxt = 'visitor_checkout_signature_';
                $fieldname = 'checkout_file_name';
                break;

            default:
                $filetxt = 'image_';
                break;
        }
        $filename = uniqid($filetxt);
        $path = public_path() . '/visitor_log';
        $image = $this->imageRepository->imageFromBase64($request->imageBase64);
        $destination = $path . $this->directory_seperator . $request->visitorid . $this->directory_seperator . $filename . "." . $image['extension'];
        if (!file_exists($path . $this->directory_seperator . $request->visitorid)) {
            mkdir($path . $this->directory_seperator . $request->visitorid, 0777, true);
        }
        $entry = file_put_contents($destination, $image['image']);

        VisitorLogDetails::where('id', $request->visitorid)->update([$fieldname => $filename . "." . $image['extension']]);

        return true;
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function getCheckinTime()
    {
        $checkintime = Carbon::now()->format('H : i A');
        $checkin = Carbon::now()->toDateTimeString();
        return response()->json(array('success' => true, 'checkintime' => $checkintime, 'checkin' => $checkin));
    }
}
