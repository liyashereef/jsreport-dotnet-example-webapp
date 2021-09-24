<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Config;
use DB;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\ImportRequest;
use Modules\Admin\Http\Requests\UserRequest;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\User;
use Modules\Admin\Models\EmployeeComplianceReports;
use Modules\Admin\Repositories\UserRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use App\Exports\VisionExport;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Models\Customer;
use Modules\Uniform\Repositories\UraRateRepository;

use Validator;

class UserController extends Controller
{

    /**
     * The UserRepository instance.
     *
     * @var \App\Repositories\UserRepository
     */
    protected $userRepository, $helperService, $imageUploadPath;

    /**
     * Create a new UserRepository instance.
     *
     * @param  \App\Repositories\UserRepository $userRepository
     * @return void
     */
    public function __construct(UraRateRepository $uraRateRepository, UserRepository $userRepository, HelperService $helperService)
    {
        $this->userRepository = $userRepository;
        $this->helperService = $helperService;
        $this->uraRateRepository = $uraRateRepository;
        $this->imageUploadPath = Config::get('globals.profilePicPath');
        $this->user_allocation_repo = new TrainingUserCourseAllocationRepository();
        //$this->customerRepository=new CustomerRepository();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_variables_arr = $this->userRepository->userIndex();
        $roles = $page_variables_arr['roles'];
        $work_types = $page_variables_arr['work_types'];
        $employees = $page_variables_arr['employees'];
        $security_clearances = $page_variables_arr['security_clearances'];
        $positions = $page_variables_arr['positions'];
        $certificates = $page_variables_arr['certificates'];
        $trashed_certificates = $page_variables_arr['trashed_certificates'];
        $banks = $page_variables_arr['banks'];
        $bank_code = $page_variables_arr['bank_code'];
        $customers = $page_variables_arr['customers'];
        $marital_status = $page_variables_arr['marital_status'];
        $salutation = $page_variables_arr['salutation'];
        $payroll_group = $page_variables_arr['payroll_group'];
        $relation = $page_variables_arr['relation'];
        $payment_methods = $page_variables_arr['payment_methods'];
        $user_skills = $page_variables_arr['user_skills'];
        $employeecompliancereports = EmployeeComplianceReports::get();
        $ura_rate = $this->uraRateRepository->getCurrentRate();
        $userList = $this->userRepository->getUserLookup(null, ['admin', 'super_admin'], null, true, null, true)
        ->orderBy('first_name', 'asc')
        ->get();
        $approversList = $this->userRepository->getUserList(true, null, null, ['super_admin'], false, false)->sortBy('full_name')->pluck('full_name', 'id')->toArray();
        return view('admin::user.index', compact(
            'roles',
            'employees',
            'work_types',
            'security_clearances',
            'positions',
            'certificates',
            'trashed_certificates',
            'approversList',
            'employeecompliancereports',
            'banks',
            'bank_code',
            'customers',
            'relation',
            'marital_status',
            'salutation',
            'payroll_group',
            'relation',
            'payment_methods',
            'ura_rate',
            'userList',
            'user_skills'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        //dd($request->all());
        try {
            DB::beginTransaction();
            $this->userRepository->userStore($request);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id)
    {
        try {
            DB::beginTransaction();
            $this->userRepository->userDestroy($user_id);
            $delete = $this->user_allocation_repo->userAllUnallocation($user_id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Get all users list
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function getList(Request $request, $active = null)
    {
        // GET User List
        $employee_id=$request->get('employee_id')?:null;
        return datatables()->of($this->userRepository->getUserTableList($active, $employee_id))->addIndexColumn()->toJson();
    }

    /**
     * Display the specified User.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_details = $this->userRepository->getUserDetails($id);
        return response()->json($user_details);
    }

    /**
     * Export user data for Banking purpose
     * @param  Request $request
     * @return redirect
     */
    public function userVisionExport(Request $request)
    {
        \Config::set('excel.exports.csv.delimiter', ';');
        \Config::set('excel.exports.csv.enclosure', '');
        return Excel::download(new VisionExport, 'Vision Export' . date("Y-m-d H:i A") . '.csv');
        //return (new InvoicesExport)->download('invoices.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Import user data from excel
     * @param  Request $request
     * @return redirect
     */
    public function userImport(ImportRequest $request)
    {
        $import = $this->userRepository->userExcelImport($request);
        return redirect(route('user'))->with('user-updated', __($import));
    }

    /**
     * Display the  Employee Profile.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request)
    {
        $user = \Auth::user();
        return view('admin::user.profile', ['user' => $user]);
    }

    /**
     * Update the user profile in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, $id)
    {
        $user = User::find($id);
        $user_id = \Auth::User()->id;
        $new_password = $request->get('password');
        Validator::make($request->all(), [
            'first_name' => 'bail|required|regex:/^[0-9A-Za-z\s\-]+$/u|max:255' . $id,
            'last_name' => 'bail|nullable|regex:/^[0-9A-Za-z\s\-]+$/u|max:255',
            'phone' => 'bail|required|max:13',
            'email' => 'bail|required|max:255|email|unique:users,email,' . $id . ',id,deleted_at,NULL',
            'password' => 'sometimes|nullable|same:password|min:8',
            'password_confirmation' => 'required_with:password|same:password|sometimes|nullable|min:8',
        ])->validate();
        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->email = $request->get('email');
        if ($new_password != null) {
            $user->password = bcrypt($new_password);
        }
        $employee = Employee::where('user_id', $id)->first();
        $employee->phone = $request->get('phone');
        //Image upload - start
        $employeeImageName = $this->userRepository->uploadProfileImage($request, $id);
        if ($employeeImageName != null) {
            $employee->image = $employeeImageName;
        }
        //Image upload - end
        $user->save();
        $employee->save();
        return response()->json(['status' => true]);
    }

    /**
     * Function to get the formated user details.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function formattedUserDetails($id)
    {
        $user_details = $this->userRepository->getFormatedUserDetails($id);
        return response()->json($user_details);
    }
}
