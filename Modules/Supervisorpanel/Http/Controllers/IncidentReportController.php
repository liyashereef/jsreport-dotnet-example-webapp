<?php

namespace Modules\Supervisorpanel\Http\Controllers;

use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\IncidentStatusList;
use Modules\Supervisorpanel\Http\Requests\IncidentReportRequest;
use Modules\Supervisorpanel\Models\IncidentReport;
use Modules\Supervisorpanel\Models\PayPeriod;
use Modules\Supervisorpanel\Repositories\IncidentReportRepository;
use App\Services\HelperService;

class IncidentReportController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $incident_report_repository;
    protected $helper_service;

    /**
     * IncidentReportController constructor.
     * @param IncidentReportRepository $incident_report_repository
     */
    public function __construct(IncidentReportRepository $incident_report_repository)
    {
        $this->incident_report_repository = $incident_report_repository;
        $this->helper_service = new HelperService();
    }

    /**
     * Incident Report layout view
     * @param $customer_id
     * @param $payperiod_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($customer_id, $payperiod_id)
    {
        $content = $this->incident_report_repository->incidentReportInit($customer_id, $payperiod_id);
        return response()->json(array('success' => true, 'content' => !empty($content) ? ($content->render()) : '---'));
    }

    /**
     * Datatable array with data list
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request)
    {
        $datatable_arr = $this->incident_report_repository->incidentReportList($request->customer_id, $request->payperiod_id);
        return datatables()->of($datatable_arr)->addIndexColumn()->make(true);
    }

    /**
     * Get real path to Incident report upload
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getfile(Request $request)
    {
        try {
            $path = $this->incident_report_repository->incidentReportAttachment($request->incident_report_id);
            return response()->download($path['path'], $path['file'], [], 'inline');
        } catch (\Exception $e) {
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Store incident report to DB
     * @param IncidentReportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(IncidentReportRequest $request)
    {
        try {
            DB::beginTransaction();
            $incident = $this->incident_report_repository->storeIncidentReport($request);

            if (!$incident) {
                throw new Exception("Save Failed");
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
        try {
            $this->incident_report_repository->sendNotification($incident);
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            return response()->json(array('success' => true, 'message' => 'Incident created but failed to send notification to area manager' /* . $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()*/));
        }
    }

    /**
     * Store status change to DB / Update incident status
     *
     * @param Request $request
     * @return void
     */
    public function storeStatusChange(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->incident_report_repository->storeIncidentStatus($request);
            DB::commit();
            return response()->json(array('success' => 'true'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Incident report dashboard
     *
     * @return void
     */
    public function incidentReportDashboard(Request $request)
    {
        $status = IncidentStatusList::all();
        $allocatedCustomers = $this->incident_report_repository->getCustomerList();
        $fromDate = isset($request->from) ? $request->from : null;
        $toDate = isset($request->to) ? $request->to : null;
        // $selectedCustomers = $this->helper_service->getCustomerIds();
        $selectedCustomers = strlen($request->cIds) > 0 ? explode(",", $request->cIds) : [];
        return view('supervisorpanel::incident-report-dashboard', [
            'status' => $status,
            'allocatedCustomers' => $allocatedCustomers,
            "selectedCustomers" => $selectedCustomers,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
    }

    /**
     * To get a list for incident report dashboard
     *
     * @return void
     */
    public function getIncidentReportDashboardList(Request $request)
    {
        $incident_list = $this->incident_report_repository->getIncidentReportDashList($customer_session = true, false, $request);
        $data_list = $this->incident_report_repository->prepareIncidentReportList($incident_list);
        return datatables()->of($data_list)->toJson();
    }

    public function incidentDetails($id)
    {
        return $this->incident_report_repository->getIncidentStatusLog($id);
    }

    /**
     * Datatable array with data list
     * @param Request $request
     * @return mixed
     */
    public function incidentStatusLists($id)
    {
        $datatable_arr = $this->incident_report_repository->incidentStatusList($id);
        return datatables()->of($datatable_arr)->addIndexColumn()->make(true);
    }
}
