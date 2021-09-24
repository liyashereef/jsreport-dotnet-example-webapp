<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Mail;
use Modules\Admin\Http\Requests\CompliancePolicyRequest;
use Modules\Admin\Models\CompliancePolicy;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CompliancePolicyCategoryRepository;
use Modules\Admin\Repositories\CompliancePolicyAgreeDisagreeRepository;
use Modules\Admin\Repositories\CompliancePolicyRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Compliance\Repositories\ComplianceRepository;
use Modules\Admin\Repositories\CompliancePolicyRoleRepository;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Repositories\RolesAndPermissionRepository;

class CompliancePolicyController extends Controller
{
    protected $repository, $helperService, $compliancePolicyCategoryRepository, $complianceRepository,$compliancePolicyAgreeDisagreeRepository,$userRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CompliancePolicyRepository $compliancePolicyRepository
     * @return void
     */
    public function __construct(
        CompliancePolicyRepository $compliancePolicyRepository,
        CompliancePolicyCategoryRepository $compliancePolicyCategoryRepository,
        HelperService $helperService,
        ComplianceRepository $complianceRepository,
        CompliancePolicyAgreeDisagreeRepository $compliancePolicyAgreeDisagreeRepository,
        UserRepository $userRepository,
        RolesAndPermissionRepository $rolesAndPermissionRepository,
        CompliancePolicyRoleRepository $compliancePolicyRoleRepository
    ) {
        $this->repository = $compliancePolicyRepository;
        $this->helperService = $helperService;
        $this->compliancePolicyCategoryRepository = $compliancePolicyCategoryRepository;
        $this->complianceRepository = $complianceRepository;
        $this->compliancePolicyAgreeDisagreeRepository = $compliancePolicyAgreeDisagreeRepository;
        $this->compliancePolicyRoleRepository = $compliancePolicyRoleRepository;
        $this->userRepository = $userRepository;
        $this->rolesAndPermissionRepository = $rolesAndPermissionRepository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categoryList = $this->compliancePolicyCategoryRepository->getList();
        $complianceRoles = $this->userRepository->getRoleLookup();
        return view('admin::masters.compliance-policy', compact('categoryList', 'complianceRoles'));
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function analyticsIndex()
    {
        $current_user = Auth::user()->id;
        $curr_user_role = Auth::user()->roles->pluck('name');
        $curr_user_role_str = "('".implode("','", data_get($curr_user_role, '*'))."')";
        $sql_total_policy_count =
            "SELECT count(distinct(j.id)) as total
            FROM compliance_policies j
            LEFT JOIN compliance_policy_roles pr
            ON
            j.id = pr.compliance_policy_id
            AND j.deleted_at IS NULL
            AND pr.deleted_at IS NULL
            WHERE j.deleted_at IS NULL
            AND pr.deleted_at IS NULL";

        $total_count = $this->complianceRepository->elementPreparation($sql_total_policy_count);
        $element_total = $total_count == 0 ? $total_count : [$total_count, 0, $total_count];

        $policy_count_chart = $this->complianceRepository->prepareChart($element_total, "Total Policies");

        $sql_compliant_count = "select count(DISTINCT policy_id) as total
        from policy_acceptances k
        where k.policy_id in(select id from compliance_policies j WHERE j.status=0 and j.deleted_at IS NULL) and k.employee_id='$current_user'
        ";
        $compliant_count = $this->complianceRepository->elementPreparation($sql_compliant_count);
        $element_compliant = $compliant_count == 0 ? $compliant_count : [$compliant_count, 0, $total_count];
        $chart_count_compliant = $this->complianceRepository->prepareChart($element_compliant, "Total Compliant");
        $average = $this->complianceRepository->calculateAverage($sql_compliant_count, $sql_total_policy_count);
        return view('admin::masters.compliance-policy-analytics', ['policy_count_chart' => $policy_count_chart, 'compliant_count_chart' => $chart_count_compliant,
            'average' => $average]);
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CompliancePolicyRequest $request
     * @return json
     */
    public function store(CompliancePolicyRequest $request)
    {

        try {
            \DB::beginTransaction();
            if (!$request->has('status')) {
                $request->merge(['status' => 0]);
            }
            if (!$request->has('enable_agree_or_disagree')) {
                $request->merge(['enable_agree_or_disagree' => 0]);
                $request->merge(['enable_agree_textbox' => 0]);
                $request->merge(['enable_disagree_textbox' => 0]);
            } else {
                if (!$request->has('enable_agree_textbox')) {
                    $request->merge(['enable_agree_textbox' => 0]);
                }
                if (!$request->has('enable_disagree_textbox')) {
                    $request->merge(['enable_disagree_textbox' => 0]);
                }
            }
            $result = $this->repository->save($request->all());
            if ($request->has('id')) {
                $compliance_id = $request->id;
                $delete_reasons_result = $this->compliancePolicyAgreeDisagreeRepository->deleteComplianceReason($compliance_id);
                $delete_role_result = $this->compliancePolicyRoleRepository->deleteComplianceRole($compliance_id);
            }
            $request->merge(['compliance_policy_id' => $result->id]);
            $role_result = $this->compliancePolicyRoleRepository->saveAll($request);

            if ($request->has('enable_agree_or_disagree')) {
                $reasons_result = $this->compliancePolicyAgreeDisagreeRepository->saveAll($request);
            }
            if ($request->hasFile('policy_file')) {
                $result = $this->repository->uploadFile($request->all(), $result);
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse(['reference_code' => $result->reference_code, 'created' => $result->created]));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $category_delete = $this->repository->delete($id);
            \DB::commit();
            if ($category_delete == false) {
                return response()->json($this->helperService->returnFalseResponse());
            } else {
                return response()->json($this->helperService->returnTrueResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Broadcast mail to all emploiyees.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */
    public function broadcastPolicy(Request $request)
    {
        try {
            \DB::beginTransaction();
            $policy_mail = $this->repository->policyBroadcasting($request);
            \DB::commit();
            if ($policy_mail == false) {
                return response()->json($this->helperService->returnFalseResponse());
            } else {
                return response()->json($this->helperService->returnTrueResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Policy statistics of all policies.
     *
     *
     * @return chart
     */
    public function policyStatistics()
    {
        $user_count = User::count();
        $query = "
        select compliance_policies.policy_name,
        count(policy_acceptances.policy_id) as total,
        '$user_count'-count(policy_acceptances.policy_id)  
        as total_count,policy_acceptances.policy_id
        from compliance_policies
        join policy_acceptances 
        on compliance_policies.id = policy_acceptances.policy_id 
        AND compliance_policies.deleted_at IS NULL 
        group by compliance_policies.policy_name,policy_acceptances.policy_id";
        $results = \DB::select(\DB::raw($query));
        $dataset2 = $policyid = $labels = $dataset1 = [];
        foreach ($results as $each_data) {
            $dataset1[] = $each_data->total;
            $dataset2[] = $each_data->total_count;
            $labels[] = $each_data->policy_name;
        }
        return view('admin::statistics', ['dataset1' => $dataset1, 'dataset2' => $dataset2, 'labels' => $labels]);
    }

    /**
     * Policy statistics of all policies.
     *
     *@param  Illuminate\Http\Request $request
     * @return json
     */
    public function policyAllList(Request $request)
    {

        $employee_list = $this->repository->getAllPolicies($request);
        $data_values = $this->repository->makeDatatablevalues($employee_list);
        return datatables()->of($data_values)->addIndexColumn()->toJson();
    }
}
