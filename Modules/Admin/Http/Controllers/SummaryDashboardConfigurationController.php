<?php

namespace Modules\Admin\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\SummaryDashboardConfiguration;
use Modules\Admin\Models\DashboardSetting;
use Modules\Hranalytics\Models\EmployeeSurveyTemplate;

class SummaryDashboardConfigurationController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\BankRepository $banksRepository
     * @return void
     */
    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;
    }

    /**
     * Display index form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config = SummaryDashboardConfiguration::get();
        $employeeSurveys = EmployeeSurveyTemplate::where("active", 1)->get();
        $dashBoardSettings = DashboardSetting::find(1);
        $defaulTemplate = "";
        if ($dashBoardSettings) {
            $defaulTemplate = $dashBoardSettings->default_employeesurvey;
        }
        return view('admin::summary-dashboard.index', compact(
            'config',
            'dashBoardSettings',
            'employeeSurveys',
            'defaulTemplate'
        ));
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            $dataArray = $request->all();
            if (!empty($dataArray)) {
                $numberOfElements = isset($dataArray['value']) ? count($dataArray['value']) : 0;
                if ($numberOfElements == 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No values found',
                    ]);
                }
                $uniqueValues = count(array_unique($dataArray['value']));
                $arrayElementsCount = count($dataArray['value']);
                if ($uniqueValues != $arrayElementsCount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Duplicate values found',
                    ]);
                }
                $deleteItems = SummaryDashboardConfiguration::where('type', $dataArray['type'])->delete();
                for ($i = 0; $i < $numberOfElements; $i++) {
                    $summaryDashboardConfiguration = new SummaryDashboardConfiguration;
                    $summaryDashboardConfiguration->type = $dataArray['type'];
                    $summaryDashboardConfiguration->value = $dataArray['value'][$i];
                    $summaryDashboardConfiguration->color = $dataArray['color'][$i];
                    $summaryDashboardConfiguration->created_by = \Auth::user()->id;
                    $summaryDashboardConfiguration->save();
                }
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }


    public function setDefaultConfiguration(Request $request)
    {
        $dashBoardsave = DashboardSetting::updateOrCreate(["id" => 1], [
            "default_employeesurvey" => $request->employeesurvey
        ]);
        // $dashBoardsave->default_employeesurvey = $request->employeesurvey;
        if ($dashBoardsave) {
            $successcontent['success'] = true;
            $successcontent['message'] = 'Updated successfully';
            $successcontent['code'] = 200;
        } else {
            $successcontent['success'] = false;
            $successcontent['message'] = 'Not Updated';
            $successcontent['code'] = 406;
        }
        return json_encode($successcontent, true);
    }

    public function getList(Request $request)
    {
        $type = $request->get('type');

        if (!empty($type)) {
            $data = SummaryDashboardConfiguration::where('type', $type)->get()->toArray();
        } else {
            $data = [];
        }
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
