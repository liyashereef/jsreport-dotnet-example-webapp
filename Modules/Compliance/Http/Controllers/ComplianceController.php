<?php

namespace Modules\Compliance\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Compliance\Repositories\ComplianceRepository;
use Modules\Compliance\Http\Requests\PolicyAcceptanceRequest;
use Modules\Timetracker\Repositories\ImageRepository;
use Modules\Compliance\Models\PolicyAcceptance;

class ComplianceController extends Controller
{

    protected $courseRepository;

    public function __construct(ComplianceRepository $complianceRepository, ImageRepository $imageRepository)
    {
        $this->complianceRepository = $complianceRepository;
        $this->imageRepository = $imageRepository;
        $this->directory_seperator = "/";
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $data=$this->complianceRepository->getIndexPage();
        return view('compliance::dashboard.index', ['policy_count_chart' => $data['policy_count_chart'], 'compliant_count_chart' => $data['chart_count_compliant'],
            'average' => $data['average']]);
    }

    /**
     * Display a listing of the policy.
     * @param $type
     */
    public function policyList($id = null)
    {
        $policy = $this->complianceRepository->getDatatablevalues($id);
        $data = $this->complianceRepository->prepareData($policy, $id);
        return datatables()->of($data)->toJson();
    }

    /**
     * Display a listing of the policy.
     * @param $id
     */
    public function policyGet($boolean, $id)
    {
        $policy = $this->complianceRepository->get($id);
        return view('compliance::dashboard.policy_description', ['policy_details' => $policy,
        'agree_reasons' =>$policy['agreeReasons']->pluck('reason', 'id'),
        'disagree_reasons' =>$policy['disagreeReasons']->pluck('reason', 'id'),
        'boolean' => $boolean]);
    }

    /**
     * Make policy Compliant.
     * @param PolicyAcceptanceRequest $request
     */
    public function makePolicyCompliant(PolicyAcceptanceRequest $request)
    {
        //dd($request->all());
        $policy_acknowledge = $this->complianceRepository->agreePolicy($request);
        //$this->uploadImage($request);
        return response()->json(array('success' => true));
        //return response()->json(array('success' => true));
    }

    /***
     * Prepare chart data for specific policy analytics dashboard
     *  @param integer policy_id
     *  @return view
     *
     */
    public function getPolicyChart($policy_id)
    {
        $compliants = $this->complianceRepository->getPolicyComplianceReasons($policy_id, 1);
        $non_compliants = $this->complianceRepository->getPolicyComplianceReasons($policy_id, 0);
        $pending_users = $this->complianceRepository->getCountPendingUser($policy_id);
        $compliant_data = $non_compliant_data = $pending_user_data = [];
        $non_compliant_data = $this->prepareChartData($non_compliants);
        $compliant_data = $this->prepareChartData($compliants);
        $pending_user_data = $pending_users;
        //dd($compliant_data,$non_compliant_data,$pending_user_data);
        return view('compliance::policy_analytics.index', compact(
            'compliant_data',
            'non_compliant_data',
            'pending_user_data',
            'policy_id'
        ));
    }

    /***
     *  Prepare chart data for policy analytics dashboard
     *  @param array
     *  @return array
     */
    public function prepareChartData($data)
    {
        $result = ['data' => [], 'labels' => []];
        if (!$data->isEmpty()) {
            // $result = $data->map(function($item){

            //     $new_item['name'] = $item->agreeDisagreeReason->reason;
            //     $new_item['y'] = $item->total;
            //     $new_item['reason_id'] = $item->agreeDisagreeReason->id;
            //     return $new_item;
            // });

            foreach ($data as $key => $item) {
                $result['data'][$key]['name'] = htmlspecialchars(data_get($item, 'agreeDisagreeReason.reason'), ENT_QUOTES);
                //$result['data'][$key]['name'] = data_get($item,'agreeDisagreeReason.reason'); //git conflict
                $result['data'][$key]['color'] = '#8fb15a';
                $result['data'][$key]['y'] = data_get($item, 'total');
                $result['data'][$key]['reason_id'] = data_get($item, 'agreeDisagreeReason.id');
                $result['labels'][$key] = htmlspecialchars(data_get($item, 'agreeDisagreeReason.reason'), ENT_QUOTES);
            }
            //dd($result);
        }
        return $result;
    }

    /**
     * To get employee details voted for a compliance reason
     * @param  Illuminate\Http\Request $request
     * @return json
     *
     */
    public function getEmployeesComplianceReason(Request $request)
    {
        //dd($request);
        $input = $request->all();
        $result = $this->complianceRepository->getEmployeesComplianceReason($input);
        return datatables()->of($result)->addIndexColumn()->toJson();
    }

    /**
     * To get employee details voted for a compliance reason
     * @param  Illuminate\Http\Request $request
     * @return json
     * */
    public function getPendingCompliance(Request $request)
    {
        $input = $request->all();
        $result = $this->complianceRepository->getPendingCompliance($input);
        return datatables()->of($result)->addIndexColumn()->toJson();
    }

    public function uploadImage(Request $request)
    {
   // dd($request->all());
        $filetxt = 'signature_';
        $fieldname = 'signature_file_name';
   
        $filename = uniqid($filetxt);
        $path = public_path() . '/policy_accept_signature';
        $image = $this->imageRepository->imageFromBase64($request->imageBase64);
        $destination = $path . $this->directory_seperator . $request->policy_id . $this->directory_seperator . $filename . "." . $image['extension'];

        if (!file_exists($path . $this->directory_seperator . $request->policy_id)) {
            mkdir($path . $this->directory_seperator . $request->policy_id, 0777, true);
        }
        $entry = file_put_contents($destination, $image['image']);

        PolicyAcceptance::where('policy_id', $request->policy_id)->update([$fieldname => $filename . "." . $image['extension']]);

        return '1';
    }
}
