<?php

namespace Modules\Hranalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Hranalytics\Models\EmployeeFeedback;
use Modules\Hranalytics\Models\EmployeeFeedbackApproval;
use Modules\Admin\Models\WhistleblowerStatusLookup;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\DepartmentEmployees;

class EmployeeFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('hranalytics::employeeFeedback.index');
    }

    public function employeeFeedbackDetailed(Request  $request)
    {

        $statusLookup = WhistleblowerStatusLookup::get();
        $employeeFeedback = EmployeeFeedback::with([
            "create_user",
            "create_user.employee",
            "create_user.employee.employeePosition",
            "update_user",
            "customer",
            "department",
            "userstatus" => function ($q) {
                return $q->withTrashed();
            },
            "approvalfeedback" => function ($q) {
                return $q->orderBy("created_at", "desc");
            },
            "approvalfeedback.userstatus" => function ($qry) {
                return $qry->orderBy("created_at", "desc");
            },
            "approvalfeedback.create_user" => function ($qry) {
                return $qry->orderBy("created_at", "desc");
            }
        ])->find($request->id);
        $id = $request->id;
        $latitude = $employeeFeedback->latitude;
        $longitude = $employeeFeedback->longitude;
        $userDetail = isset($employeeFeedback->create_user) ?
            $employeeFeedback->create_user : null;
        $customer = isset($employeeFeedback->customer) ?
            $employeeFeedback->customer : null;
        $employeeName = $employeeFeedback->create_user->getFullNameAttribute();
        return view(
            'hranalytics::employeeFeedback.employeeFeedbackDetailed',
            compact(
                "employeeFeedback",
                "statusLookup",
                "id",
                "latitude",
                "longitude",
                "userDetail",
                "employeeName",
                "customer"
            )
        );
    }


    public function viewEmployeeMap(Request $request)
    {
        $id = $request->id;
        $employeeFeedback = EmployeeFeedback::with([
            "customer", "create_user", "create_user.employee", "create_user.employee.employeePosition"
        ])->find($id);
        $latitude = $employeeFeedback->latitude;
        $longitude = $employeeFeedback->longitude;
        $userDetail = isset($employeeFeedback->create_user) ?
            $employeeFeedback->create_user : null;
        $customer = isset($employeeFeedback->customer) ?
            $employeeFeedback->customer : null;
        $employeeName = $employeeFeedback->create_user->getFullNameAttribute();
        return view('hranalytics::employeeFeedback.employeeMap', compact(
            "latitude",
            "longitude",
            "userDetail",
            "employeeName",
            "customer"
        ));
    }


    public function saveFeedbackApproval(Request $request)
    {
        $successcontent['success'] = false;
        $successcontent['message'] = 'Not Saved';
        $successcontent['code'] = 406;
        $id = $request->id;
        $status = $request->status;

        $notes = $request->notes;
        $approval = EmployeeFeedbackApproval::create([
            "feedback_id" => $id,
            "notes" => $notes,
            "status" => $status,
            "created_by" => \Auth::user()->id
        ]);
        if ($approval) {
            $employeeFeedback = EmployeeFeedback::find($id);
            $employeeFeedback->status = $status;
            $employeeFeedback->updated_by = \Auth::user()->id;
            if ($employeeFeedback->save()) {
                $successcontent['success'] = true;
                $successcontent['message'] = 'Added successfully';
                $successcontent['code'] = 200;
            } else {
                $successcontent['success'] = false;
                $successcontent['message'] = 'Not Added';
                $successcontent['code'] = 406;
            }
        }
        return json_encode($successcontent);
    }

    public function listFeedbacks(Request $request)
    {
        $employeeFeedbackdata = collect([]);
        $departmentEmployees = [];
        $allocatedCustomer = [];
        $permission = 0;
        if (\Auth::user()->hasAnyPermission([
            "view_allocated_sites_in_employeefeedback",
            "view_all_sites_in_employeefeedback",
            "view_transaction_department_allocation",
            "super_admin"
        ])) {
            if (\Auth::user()->hasAnyPermission(['view_all_sites_in_employeefeedback', "super_admin"])) {
                $permission = 1;
            } else if (\Auth::user()->hasAnyPermission(['view_allocated_sites_in_employeefeedback'])) {
                $permission = 2;
                $allocatedCustomer = CustomerEmployeeAllocation::where("user_id", \Auth::user()->id)->get()->pluck("customer_id")->toArray();
            } else if (\Auth::user()->hasAnyPermission(['view_transaction_department_allocation'])) {
                $permission = 3;
                $departmentEmployees = DepartmentEmployees::where("user_id", \Auth::user()->id)
                    ->get()->pluck("department_master_id")->toArray();
            }
            $employeeFeedbackdata = EmployeeFeedback::with([
                "create_user",
                "update_user",
                "customer",
                "department",
                "userstatus" => function ($q) {
                    return $q->withTrashed();
                },
                "approvalfeedback" => function ($q) {
                    return $q->orderBy("created_at", "desc");
                },
                "approvalfeedback.userstatus" => function ($qry) {
                    return $qry->orderBy("created_at", "desc");
                },
                "approvalfeedback.create_user" => function ($qry) {
                    return $qry->orderBy("created_at", "desc");
                }, "employeeRating"
            ])
                ->when($permission > 0, function ($q) use (
                    $allocatedCustomer,
                    $departmentEmployees,
                    $permission
                ) {
                    if ($permission == 2) {
                        return $q->whereIn("customer_id", $allocatedCustomer);
                    } else if ($permission == 3) {
                        return $q->whereIn("department_id", $departmentEmployees);
                    }
                })->orderBy("id", "desc")->get();
        }

        return datatables()->of($employeeFeedbackdata)->addIndexColumn()->toJson();
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hranalytics::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('hranalytics::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('hranalytics::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
