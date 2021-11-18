<?php

namespace Modules\Admin\Repositories;

use DB;
use File;
use Config;
use Carbon\Carbon;
use Modules\Admin\Models\User;
use App\Services\HelperService;
use Modules\Admin\Models\Banks;
use Modules\Admin\Models\UserTax;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\WorkType;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Modules\Admin\Models\PositionLookup;
use Modules\Admin\Models\UserEmployment;
use Modules\Admin\Models\UserCertificate;
use Modules\Hranalytics\Models\Candidate;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Modules\Admin\Models\CertificateMaster;
use phpDocumentor\Reflection\Types\Boolean;
use Modules\Admin\Http\Requests\UserRequest;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Admin\Models\UserEmergencyContact;
use Modules\Admin\Models\UserBank;
use Modules\Admin\Models\UserBenefit;
use Modules\Admin\Models\UserPayrollGroup;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\MaritalStatus;
use Modules\Admin\Models\UserSalutations;
use Modules\Admin\Models\SecurityClearanceUser;
use Modules\Admin\Models\EmployeeMobileDashboard;
use Modules\Admin\Models\SecurityClearanceLookup;
use Modules\Admin\Models\EmployeeComplianceReports;
use Modules\Expense\Models\ExpenseAllowableForUser;
use Modules\Admin\Models\UserEmergencyContactRelation;
use Modules\Admin\Models\UserPaymentMethods;
use Modules\Admin\Models\UserSkill;
use Modules\Admin\Models\UserSkillOptionAllocation;
use Modules\Admin\Models\UserSkillUserValue;
use Modules\Admin\Models\UserSkillOptionValue;

class UserRepository
{

    private $arr_role_lookup;

    protected $imageUploadPath;

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $employeeModel, $roleModel, $securityClearanceLookupModel, $securityClearanceUser, $helperService, $employeeAllocationModel, $certificate;

    /**
     * Create a new UserRepository instance.
     *
     * @param \App\Models\User $user
     */
    public function __construct()
    {
        $this->model = new User();
        $this->employeeModel = new Employee();
        $this->employeeAllocationModel = new EmployeeAllocation();
        $this->roleModel = new Role();
        $this->securityClearanceLookupModel = new SecurityClearanceLookup();
        $this->securityClearanceUser = new SecurityClearanceUser();
        $this->certificate = new CertificateMaster();
        $this->helperService = new HelperService();
        $this->arr_role_lookup = null;
        $this->imageUploadPath = Config::get('globals.profilePicPath');
    }

    /**
     * Function to prepare Role lookup
     * @return type
     */
    private function prepareRoleLookup($role = null, $role_except = null)
    {
        $arr_role_lookup = array();
        $role_names_obj = Role::when(($role != null), function ($query) use ($role) {
            $query->whereIn('name', $role);
        });
        $role_names_obj->when(($role_except != null), function ($query) use ($role_except) {
            $query->whereNotIn('name', $role_except);
        });
        $role_names = $role_names_obj->orderby('name')->pluck('name');
        foreach ($role_names as $each_role_name) {
            $arr_role_lookup[$each_role_name] = HelperService::snakeToTitleCase($each_role_name);
        }
        return $arr_role_lookup;
    }

    /**
     * Getter for Role list array
     * @return type
     */
    public function getRoleLookup($role = null, $role_except = null)
    {
        $arr_role_lookup = array();
        if (isset($this->arr_role_lookup) && !isset($role) && !isset($role_except)) {
            $arr_role_lookup = $this->arr_role_lookup;
        } else {
            $arr_role_lookup = $this->prepareRoleLookup($role, $role_except);
            if (!isset($role) && !isset($role_except)) {
                $this->arr_role_lookup = $arr_role_lookup;
            }
        }
        return $arr_role_lookup;
    }

    /**
     * Index variables in view
     * @return type
     */
    public function userIndex()
    {
        $form_index['roles'] = $this->getRoleLookup(null, ['super_admin']);
        $form_index['employees'] = User::select(DB::raw("CONCAT(first_name,' ',COALESCE(last_name,'')) as name"), 'id')->where('id', '!=', 1)->whereActive(true)->pluck('name', 'id');
        $form_index['work_types'] = WorkType::whereActive(true)->pluck('type', 'id');
        $form_index['security_clearances'] = $this->securityClearanceLookupModel->orderBy('security_clearance')->pluck('security_clearance', 'id');
        $form_index['certificates'] = $this->certificate->orderBy('certificate_name')->pluck('certificate_name', 'id');
        $form_index['trashed_certificates'] = $this->certificate->withTrashed()->orderBy('certificate_name')->pluck('certificate_name', 'id');
        $form_index['positions'] = PositionLookup::orderBy('position')->pluck('position', 'id');
        $form_index['bank_code'] = Banks::orderBy('bank_name')->pluck('bank_code', 'id')->toArray();
        $form_index['banks'] = Banks::orderBy('bank_name')->pluck('bank_name', 'id')->toArray();
        $form_index['marital_status'] = MaritalStatus::orderBy('status')->pluck('status', 'id')->toArray();
        $form_index['salutation'] = UserSalutations::pluck('salutation', 'id')->toArray();
        $form_index['payroll_group'] = UserPayrollGroup::pluck('name', 'id')->toArray();
        $form_index['relation'] = UserEmergencyContactRelation::orderBy('relations')->pluck('relations', 'id')->toArray();
        $form_index['payment_methods'] = UserPaymentMethods::pluck('payment_methods', 'id')->toArray();
        $form_index['user_skills'] = UserSkill::pluck('name', 'id')->toArray();
        $customer_details_arr = Customer::where('active', 1)->orderBy('client_name')->get();
        $customers_arr = array();
        foreach ($customer_details_arr as $key => $customers) {
            $customers_arr[$customers->id] = $customers->client_name . ' (' . $customers->project_number . ')';
        }
        $form_index['customers'] = $customers_arr;
        return $form_index;
    }

    /**
     * Store user
     * @param UserRequest $request
     */
    public function userStore($request)
    {

        $employeeDetail = null;
        if (request('id') > 0) {
            $employeeDetail = Employee::find(request('id'));
        }

        $prev_postal_code = data_get($employeeDetail, 'employee_postal_code');
        $id = request('id');
        $dashboardexemptions = [];
        $dashboardreports = EmployeeComplianceReports::get();


        foreach ($dashboardreports as $dashboardreport) {
            if (!$request->get("dashboardreport_" . $dashboardreport->id)) {
                $dashboardexemptions[] = $dashboardreport->id;
            }
        }

        $user_data = [
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'username' => $request->get('username'),
            'alternate_email' => $request->get('alternate_email'),
            'gender' => $request->get('gender'),
            'marital_status_id' => $request->get('marital_status_id'),
            'sin' => $request->get('sin'),
            'salutation_id' => $request->get('salutation_id'),

        ];
        if (!empty($request->get('password'))) {
            $user_data['password'] = bcrypt($request->get('password'));
        }
        $employee_data = [
            'employee_no' => $request->get('employee_no'),
            'work_type_id' => $request->get('work_type_id'),
            'phone' => $request->get('phone'),
            'phone_ext' => $request->get('phone_ext'),
            'cell_no' => $request->get('cell_no'),
            'employee_address' => $request->get('employee_address'),
            /*'employee_full_address' => $request->get('employee_full_address'),*/
            'employee_city' => $request->get('employee_city'),
            'employee_postal_code' => $request->get('employee_postal_code'),
            'employee_work_email' => $request->get('employee_work_email'),
            'employee_vet_status' => $request->get('employee_vet_status'),
            'vet_release_date' => $request->get('vet_release_date'),
            'vet_enrollment_date' => $request->get('vet_enrollment_date'),
            'vet_service_number' => $request->get('vet_service_number'),
            'employee_doj' => $request->get('employee_doj'),
            'employee_dob' => $request->get('employee_dob'),
            //'employee_rating' => $request->get('employee_rating'),
            'current_project_wage' => $request->get('current_project_wage'),
            'position_id' => $request->get('position_id'),
            'years_of_security' => $request->get('years_of_security'),
            'being_canada_since' => $request->get('being_canada_since'),
            'wage_expectations_from' => $request->get('wage_expectations_from'),
            'wage_expectations_to' => $request->get('wage_expectations_to'),
        ];
        if ($prev_postal_code !== $request->get('employee_postal_code')) {
            $employee_data['geo_location_lat'] = '';
            $employee_data['geo_location_long'] = '';
        }
        $active = $request->get('active');
        if ($active == null && isset($id)) {
            $user_data['active'] = 0;
            $employee_data['active'] = 0;
        } else {
            $user_data['active'] = 1;
            $employee_data['active'] = 1;
        }
        if (isset($id)) {
            $termination = User::find($id);
            $user_data['termination_date'] = $request->get('termination_date');
            // if ($termination->termination_date == null && (null !== $request->get('termination_date'))) {
            //     $user_data['termination_date'] = $request->get('termination_date');
            // } elseif ($termination->termination_date !== null && (null === $request->get('termination_date'))) {
            //     $user_data['termination_date'] = $request->get('termination_date');
            // }
        } else {
            $user_data['termination_date'] = $request->get('termination_date');
        }


        //if the request is submitted from candidate to employee conversion.
        // if ($request->input('candidate_id')) {
        //     $candidate = Candidate::with([
        //         'guardingexperience',
        //         'securityclearance',
        //     ])->where('id', '=', $request->input('candidate_id'))->first();
        //     if (is_object($candidate)) {
        //         if (is_numeric($candidate->guardingexperience->years_security_experience)) {
        //             $employee_data['years_of_security'] = (int) $candidate->guardingexperience->years_security_experience;
        //         }
        //         if (is_numeric($candidate->securityclearance->years_lived_in_canada)) {
        //             $now = Carbon::now();
        //             $ylInCa = $now->subYear($candidate->securityclearance->years_lived_in_canada);
        //             $employee_data['being_canada_since'] = $ylInCa->format('Y-m-d');
        //         }
        //     }
        // }
        $employee_data['years_of_security'] = $request->get('years_of_security');
        $employee_data['being_canada_since'] = $request->get('being_canada_since');

        $user = User::updateOrCreate(['id' => $id], $user_data);
        $employee_data['user_id'] = $user->id;

        //Image upload - start
        if ($request->has('candidate_id')) {
            $candidate = Candidate::find($request->get('candidate_id'));
            if (!empty($candidate) && !empty($candidate->profile_image)) {
                $uploadRootFolder = public_path() . '/images/uploads/';
                $oldImageFullPath = $uploadRootFolder . $candidate->profile_image;
                $newFileName = $user->id . '_profile.png';
                $newImageFullPath = $uploadRootFolder . $newFileName;
                $fileCopied = File::copy($oldImageFullPath, $newImageFullPath);

                if ($fileCopied) {
                    $employee_data['image'] = $newFileName;
                }
            }
        }

        $userId = $user->id;
        $employeeImageName = $this->uploadProfileImage($request, $userId);
        if ($employeeImageName != null) {
            $employee_data['image'] = $employeeImageName;
        }
        //Image upload - end

        Employee::updateOrCreate(['user_id' => $id], $employee_data);

        SecurityClearanceUser::where('user_id', $id)->delete();
        $row_nos = $request->get('row-no');
        if ($row_nos != null) {
            foreach ($row_nos as $row_no) {
                $security_clearance_lookup_id = intval($request->get('security_clearance_' . $row_no));
                $valid_until = $request->get('valid_until_' . $row_no);
                if ($security_clearance_lookup_id != 0) {
                    $security_clearance_data = [
                        'security_clearance_lookup_id' => $security_clearance_lookup_id,
                        'value' => 'Yes',
                        'valid_until' => $valid_until,
                    ];
                    $security_clearance_data['user_id'] = $user->id;
                    SecurityClearanceUser::updateOrCreate(['user_id' => $id, 'security_clearance_lookup_id' => $security_clearance_lookup_id], $security_clearance_data);
                }
            }
        }

        UserCertificate::where('user_id', $id)->delete();
        $certificate_row_nos = $request->get('certificate-row-no');
        if ($certificate_row_nos != null) {
            foreach ($certificate_row_nos as $row_no) {
                $certificate_id = intval($request->get('certificate_' . $row_no));
                $expiry = $request->get('expiry_' . $row_no);
                if ($certificate_id != 0) {
                    $certificate_data = [
                        'certificate_id' => $certificate_id,
                        'expires_on' => $expiry,
                    ];
                    $certificate_data['user_id'] = $user->id;
                    UserCertificate::updateOrCreate(['user_id' => $id, 'certificate_id' => $certificate_id], $certificate_data);
                }
            }
        }
        //expense entry
        //ExpenseAllowableForUser::where('user_id', $id)->delete();
        $expense_data = [
            'reporting_to_id' => $request->get('reporting_to_id'),
            'max_allowable_expense' => $request->get('max_allowable_expense'),
        ];

        $expense_data['user_id'] = $user->id;
        ExpenseAllowableForUser::updateOrCreate(['user_id' => $id], $expense_data);

        //expense stop

        /** Unallocate employees if role changes   -  Start **/
        $roles = ($id) ? User::find($id)->getRoleNames() : '';
        $prev_role_id = ($roles[0]) ?? '';
        if (($request->get('role_id') != $prev_role_id) && ($prev_role_id != '')) {
            $user_allocation_update = $this->employeeAllocationModel->where('supervisor_id', $user->id)->update(['to' => date('Y-m-d'), 'updated_by' => \Auth::user()->id]);
            $user_allocation_delete = $this->employeeAllocationModel->where('supervisor_id', $user->id)->orWhere('user_id', $user->id)->delete();
        }
        EmployeeMobileDashboard::where("user_id", $user->id)
            ->whereNotIn("id", $dashboardexemptions)->delete();
        if (count($dashboardexemptions) > 0) {
            foreach ($dashboardexemptions as $key => $exemptedreports) {
                EmployeeMobileDashboard::updateOrCreate(
                    [
                        "user_id" => $user->id,
                        "report_id" => $exemptedreports
                    ],
                    [
                        "user_id" => $user->id,
                        "report_id" => $exemptedreports
                    ]
                );
            }
        }

        /** Unallocate employees if role changes   -  End **/
        if ($request->get('bankid')) {
            $banking_data = [
                'bank_id' => $request->get('bankid'),
                'transit' => $request->get('transit'),
                'account_no' => $request->get('account_no'),
                'payment_method_id' => $request->get('payment_method_id')
            ];
            if (!isset($id)) {
                $banking_data['created_by'] = \Auth::user()->id;
            } else {
                $banking_data['updated_by'] = \Auth::user()->id;
            }
            if (isset($id)) {
                if ($termination->termination_date == null && (null !== $request->get('termination_date')) && ($user_data['active'] == 0)) {
                    $banking_data['payment_method_id'] = 2;
                }
            }

            $banking_data['user_id'] = $user->id;
            UserBank::updateOrCreate(['user_id' => $id], $banking_data);
        }


        $tax_data = [
            'federal_td1_claim' => $request->get('federal_td1_claim'),
            'provincial_td1_claim' => $request->get('provincial_td1_claim'),
            'is_cpp_exempt' => $request->get('is_cpp_exempt'),
            'is_uic_exempt' => $request->get('is_uic_exempt'),
            'tax_province' => $request->get('tax_province'),
            'epaystub_email' => $request->get('epaystub_email'),
            'is_epaystub_exempt' => $request->get('is_epaystub_exempt')
        ];

        if (!isset($id)) {
            $tax_data['created_by'] = \Auth::user()->id;
        } else {
            $tax_data['updated_by'] = \Auth::user()->id;
        }
        $tax_data['user_id'] = $user->id;
        UserTax::updateOrCreate(['user_id' => $id], $tax_data);

        if ($request->get('payroll_group_id') && $request->get('vacation_level') >= 0) {
            $benefits_data = [
                'payroll_group_id' => $request->get('payroll_group_id'),
                'vacation_level' => $request->get('vacation_level'),
                'green_sheild_no' => $request->get('green_sheild_no'),
                'is_lacapitale_life_insurance_enrolled' => $request->get('is_lacapitale_life_insurance_enrolled')
            ];
            if (!isset($id)) {
                $benefits_data['created_by'] = \Auth::user()->id;
            } else {
                $benefits_data['updated_by'] = \Auth::user()->id;
            }

            $benefits_data['user_id'] = $user->id;
            UserBenefit::updateOrCreate(['user_id' => $id], $benefits_data);
        }

        if ($request->get('pay_detach_customer_id')) {
            $employment_data = [
                'continuous_seniority' => $request->get('continuous_seniority'),
                'pay_detach_customer_id' => $request->get('pay_detach_customer_id')
            ];
            if (!isset($id)) {
                $employment_data['created_by'] = \Auth::user()->id;
            } else {
                $employment_data['updated_by'] = \Auth::user()->id;
            }

            $employment_data['user_id'] = $user->id;
            UserEmployment::updateOrCreate(['user_id' => $id], $employment_data);
        }



        $emergency_contact = [
            'name' => $request->get('name'),
            'relation_id' => $request->get('relation_id'),
            'full_address' => $request->get('full_address'),
            'primary_phoneno' => $request->get('primary_phoneno'),
            'alternate_phoneno' => $request->get('alternate_phoneno')
        ];
        if (!isset($id)) {
            $emergency_contact['created_by'] = \Auth::user()->id;
        } else {
            $emergency_contact['updated_by'] = \Auth::user()->id;
        }

        $emergency_contact['user_id'] = $user->id;
        UserEmergencyContact::updateOrCreate(['user_id' => $id], $emergency_contact);
        UserSkillUserValue::where('user_id', $id)->delete();
        $skill_row_nos = $request->get('skill-row-no');
        // dd($request->all());
        if ($skill_row_nos != null) {
            foreach ($skill_row_nos as $row_no) {
                $skill_id = intval($request->get('skill_' . $row_no));
                $user_option_value_id = $request->get('skillvalue_' . $row_no);
                $user_skill_option_values =  UserSkillOptionValue::find($user_option_value_id);
                $option_allocation = UserSkillOptionAllocation::where('user_skill_id', $skill_id)->where('user_skill_option_id', $user_skill_option_values->user_skill_option_id)->first();
                if ($skill_id != 0) {
                    $skill_data = [
                        'user_skill_option_allocation_id' => $option_allocation->id,
                        'user_option_value_id' => $user_option_value_id,
                    ];
                    $skill_data['user_id'] = $user->id;
                    UserSkillUserValue::create($skill_data);
                }
            }
        }



        User::find($user->id)->syncRoles([$request->get('role_id')]);

        return $user;
    }

    /**
     * Delete user
     * @param int $id
     */
    public function userDestroy($id)
    {
        User::find($id)->update(['active' => 0]);
        User::find($id)->delete();
        SecurityClearanceUser::where('user_id', '=', $id)->delete();
        Employee::where('user_id', '=', $id)->update(['active' => 0]);
        Employee::where('user_id', '=', $id)->delete();
        $allocated_supervisor_id = $this->employeeAllocationModel->pluck('supervisor_id')->toArray();
        if (in_array($id, $allocated_supervisor_id)) {
            $user_allocation_update = $this->employeeAllocationModel->where('supervisor_id', $id)->update(['to' => date('Y-m-d'), 'updated_by' => \Auth::user()->id]);
            $user_allocation_delete = $this->employeeAllocationModel->where('supervisor_id', $id)->delete();
        }
    }

    /**
     * Function to get details of a single user
     * @param type $user_id
     */
    public function getUserDetails($user_id)
    {
        $user_details = User::with(
            'roles',
            'employee',
            'employee.work_type',
            'allocatedSupervisor.supervisor',
            'allocatedEmployee.user',
            'allocation.customer',
            'securityClearanceUser.securityClearanceLookups',
            'candidate_transition.updatedUser.employee',
            'userCertificate.trashedCertificateMaster',
            'expenseAllowedForUser',
            'dashboardCompliancereports',
            'user_bank',
            'user_tax',
            'user_salutations',
            'user_emergency_contact',
            'user_benefits',
            'user_employments',
            'user_skill_value.optionAllocation.skill',
            'user_skill_value.userOptionValue'
        )->find($user_id);
        return $user_details;
    }

    /**
     * Function to get list of Users
     *
     * @param array $includeRoleOrPermission
     *              (optional) array of permissions/roles of users to be fetched
     *
     * @param array $exlcludeRoleOrPermission
     *              (optional) array of permissions/roles of users NOT to be fetched
     *
     * @param boolean $active
     *              (optional) active state of user
     *              default - all users will be fetched
     *
     * @param boolean $full_object
     *              (optional) if set true full object of users will be returned
     *              default - false - array of users with id as key
     * @param boolean $isPermissionWise permission wise / role wise flag
     * @return type
     */
    public function getUserLookup(
        $includeRoleOrPermission = null,
        $exlcludeRoleOrPermission = null,
        $active = true,
        $full_object = false,
        $permissions = null,
        //todo::remove later and alter all functioncalls
        $query_object = false,
        $isPermissionWise = true
    ) {
        $adminarray = [];
        $adminusers = [];
        $permissionrolesinclude = [];
        if ($isPermissionWise == true && $includeRoleOrPermission != null) {
            $roles = Role::whereHas('permissions', function ($q) use ($includeRoleOrPermission, $permissionrolesinclude) {
                $q->whereIn('name', $includeRoleOrPermission);
            })->get();
            try {
                foreach ($roles as $role) {
                    array_push($permissionrolesinclude, $role->name);
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        $permissionrolesexclude = [];
        if ($isPermissionWise == true && $exlcludeRoleOrPermission != null) {
            foreach (['super_admin', 'admin'] as $key => $value) {
                if (in_array($value, $exlcludeRoleOrPermission)) {
                    array_push($adminarray, $value);
                    array_splice($exlcludeRoleOrPermission, array_search($value, $exlcludeRoleOrPermission), 1);
                }
            }
            $roles = Role::whereHas('permissions', function ($q) use ($exlcludeRoleOrPermission, $permissionrolesexclude) {
                $q->whereIn('name', $exlcludeRoleOrPermission);
            })->get();
            try {
                foreach ($roles as $role) {
                    array_push($permissionrolesexclude, $role->name);
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
            if (count($adminarray) > 0) {
                $adminusers = User::role($adminarray)->get()->pluck('name')->toArray();
            }
        } else {
            $permissionrolesexclude = $exlcludeRoleOrPermission;
        }

        $filterRelationString = 'roles';
        //verify permissions
        if ($isPermissionWise) {
            try {
                HelperService::verifyPermissions($includeRoleOrPermission);
                HelperService::verifyPermissions($exlcludeRoleOrPermission);
                HelperService::verifyPermissions($permissions); //todo:should remove later
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        //Filter active users
        $user_lookup_obj = User::when(($active !== null), function ($query) use ($active) {
            return $query->where([['active', "=", $active]]);
        });

        //Filter users having the permission
        $user_lookup_obj->when(($includeRoleOrPermission != null), function ($query)
        use ($includeRoleOrPermission, $filterRelationString, $permissionrolesinclude, $isPermissionWise, $permissionrolesexclude) {
            $query->whereHas($filterRelationString, function ($q) use (
                $includeRoleOrPermission,
                $permissionrolesinclude,
                $isPermissionWise,
                $permissionrolesexclude
            ) {
                if ($isPermissionWise === false) {
                    $q->whereIn('name', $includeRoleOrPermission);
                } else {
                    $q->whereIn('name', $permissionrolesinclude);
                }

                if ($permissionrolesexclude != null) {
                    $q->whereNotIn('name', $permissionrolesexclude);
                }
            });
        }, function ($query) {
            $query->with('roles');
        });
        $user_lookup_obj->when(count($adminusers) > 0, function ($query) use ($adminusers, $adminarray) {
            $query->whereHas('roles', function ($q) use ($adminusers, $adminarray) {

                return $q->whereNotIn('name', $adminarray);
            });
        });
        //Filter user by permission (backward compatibility)
        $user_lookup_obj->when(($permissions != null), function ($query) use ($permissions) {
            $query->permission($permissions);
        });

        //Loading relations
        $user_lookup_obj->when(($full_object), function ($query) {
            $query->with(
                'employee',
                'employee.work_type',
                'allocation.customer',
                'employee.employeePosition',
                'securityClearanceUser.securityClearanceLookups'
            );
        });

        //Processing response object as per the reqirements.
        if ($query_object == true) {
            $user_lookup = $user_lookup_obj;
            return $user_lookup;
        } else {
            $user_lookup = $user_lookup_obj->orderBy('first_name')->get();
        }

        if (!$full_object) {
            $user_lookup = $user_lookup->pluck('name_with_emp_no', 'id');
        }
        $return_list = $user_lookup;
        if (!$full_object) {
            $return_list = $user_lookup->toArray();
        }
        if ($exlcludeRoleOrPermission != null) {
            if ($isPermissionWise == true) {
                $removeusers = User::permission($exlcludeRoleOrPermission)->get()->pluck('id')->toArray();
            } else {
                $removeusers = User::role($exlcludeRoleOrPermission)->get()->pluck('id')->toArray();
            }

            foreach ($removeusers as $key => $value) {
                $removeindex = $value;
                unset($return_list[$removeindex]);
            }
        }
        return $return_list;
    }

    /**
     * To get the user list based on permission
     *
     * @param [type] $permission
     * @return void
     */
    public function getUserLookupByPermission($permissions)
    {
        return $this->getUserLookup(null, ['admin', 'super_admin'], true, false, $permissions);
    }

    /**
     * Get user list for user table
     * @param boolean $active (optional)
     * @return type
     */
    public function getUserTableList($active = null, $employee_id = null)
    {
        $user_table_list = $this->getUserList($active, null, null, ['super_admin'], false, false, null, $employee_id)->map(function ($item) {
            return [
                'id' => $item->id,
                'emp_no' => ($item->employee) ? $item->employee->employee_no : '--',
                'phone' => ($item->employee) ? $item->employee->phone : '--',
                'phone_ext' => ($item->employee) ? $item->employee->phone_ext : '--',
                'full_name' => $item->full_name,
                'username' => $item->username,
                'email' => $item->email,
                'created_at' => (isset($item->created_at)) ? $item->created_at->toDateTimeString() : "",
                'updated_at' => (isset($item->updated_at)) ? $item->updated_at->toDateTimeString() : "",
                'roles' => implode(', ', array_map(array(HelperService::class, "snakeToTitleCase"), data_get($item, 'roles.*.name'))),
            ];
        });
        return ($user_table_list);
    }

    /**
     *
     * Get user list
     *
     * @param Boolean $active filter by acitve status
     * @param Array $permissionsOrRolesInclude - users of this role/permission will be fetched
     * @param Integer $supervisor_id filter by supervisor
     * @param Array $permissionsOrRolesExclude - exclude users of this role/permission
     * @param Boolean $customer_session is for dashboard customer base data filter.
     * @param Boolean $isPermissionWise -Filter either permission wise or role wise
     * @param Integer $userId - Filter using userId (array of Id or Integer)
     * @return type
     */
    public function getUserList(
        $active = null,
        $permissionsOrRolesInclude = null,
        $supervisor_id = null,
        $permissionsOrRolesExclude = null,
        $customer_session = false,
        $isPermissionWise = true,
        $userId = null,
        $employee_id = null
    ) {
        $removeusers = [];
        $adminarray = [];
        $adminusers = [];
        if ($permissionsOrRolesExclude != null) {
            if ($isPermissionWise == true) {
                foreach (['super_admin', 'admin'] as $key => $value) {
                    if (in_array($value, $permissionsOrRolesExclude)) {
                        array_push($adminarray, $value);
                        array_splice($permissionsOrRolesExclude, array_search($value, $permissionsOrRolesExclude), 1);
                    }
                }

                $removeusers = User::permission($permissionsOrRolesExclude)->get()->pluck('id')->toArray();
                if (count($adminarray) > 0) {
                    $adminusers = User::role($adminarray)->get()->pluck('id')->toArray();
                    $removeusers = array_merge($removeusers, $adminusers);
                }
            } else {
                $removeusers = User::role($permissionsOrRolesExclude)->get()->pluck('id')->toArray();
            }
        }
        //verify permissions
        if ($isPermissionWise) {
            HelperService::verifyPermissions($permissionsOrRolesInclude);
            HelperService::verifyPermissions($permissionsOrRolesExclude);
        }

        $filterRelationString = $isPermissionWise ? 'roles.permissions' : 'roles';
        // Function Param - customer_session : is for dashboard customer base data filter.
        $user_query_obj = User::select(DB::raw("*, CONCAT(first_name,' ',COALESCE(last_name,'')) as full_name"))
            ->with([
                'employee',
                'employee.work_type',
                'allocatedSupervisor.supervisor',
            ]);

        //Filter data in the permissions
        $user_query_obj->when(($permissionsOrRolesInclude != null), function ($query)
        use ($permissionsOrRolesInclude, $filterRelationString) {
            $query->with($filterRelationString);
            $query->whereHas($filterRelationString, function ($q) use ($permissionsOrRolesInclude) {
                $q->select(DB::raw("name as role_name"))->whereIn('name', $permissionsOrRolesInclude);
                //todo::check the select relation.
            });
        }, function ($query) {
            $query->with('roles');
        });

        //Filter data exclude the permissions
        $user_query_obj->when(count($removeusers) > 0, function ($user_query_obj)
        use ($permissionsOrRolesExclude, $filterRelationString, $removeusers) {
            $user_query_obj->whereNotIn('id', $removeusers);
        });

        //Filter by active status
        $user_query_obj->when(($active != null), function ($query) use ($active) {
            return $query->where('active', "=", $active);
        });

        //Filter by user dropdown
        if ($employee_id != null) {
            $user_query_obj = $user_query_obj->where('id', '=', $employee_id)->get();
            return $user_query_obj;
        }

        //Filter by user id
        if (is_array($userId)) {
            $user_query_obj->when(($userId != null), function ($query) use ($userId) {
                return $query->whereIn('id', $userId);
            });
        } else {
            $user_query_obj->when(($userId != null), function ($query) use ($userId) {
                return $query->where('id', $userId);
            });
        }

        //Filter by supervisor
        if ($supervisor_id != null) {
            $user_query_obj->whereHas('allocatedSupervisor', function ($query) use ($supervisor_id) {
                $query->where('supervisor_id', $supervisor_id);
            });
        }
        /** START ** Get Customer Ids from Session and Filter */
        if ($customer_session) {
            $customer_ids = $this->helperService->getCustomerIds();

            if (!empty($customer_ids)) {
                $user_query_obj->whereHas('allocation', function ($query) use ($customer_ids) {
                    $query->whereIn('customer_id', $customer_ids);
                });
            }
            //Inclide emp_shift_payperiod
            $user_query_obj->with(['employee_shift_payperiods' => function ($shift_payperiod_query) use ($customer_ids) {
                $shift_payperiod_query->when((!empty($customer_ids)), function ($shift_payperiod_query) use ($customer_ids) {
                    $shift_payperiod_query->whereIn('customer_id', $customer_ids);
                });
                $shift_payperiod_query->whereHas('availableShift', function ($query) {
                    return $query->with('availableShift');
                });
                $shift_payperiod_query->orderBy('created_at', 'DESC')->with('availableShift');
            }]);
            /** END ** Get Customer Ids from Session and Filter */
        }
        return $query = $user_query_obj->orderBy('first_name', 'asc')->get();
    }


    /**
     * Get allocation user list
     * @param $customer_id
     * @param Array $rolesOrPermissions
     * @param Boolean $isExclude (Is role to be excluded=false)
     * @param Boolean $isPermissionWise (Is role permission wise=true)
     */
    public function allocationUserList(
        $customer_id = null,
        $rolesOrPermissions = null,
        $isExclude = false,
        $has_allocation = false,
        $isPermissionWise = true
    ) {
        $permissionroles = [];
        if ($isPermissionWise === true && $rolesOrPermissions != null) {
            $roles = Role::whereHas('permissions', function ($q) use ($rolesOrPermissions) {
                $q->whereIn('name', $rolesOrPermissions);
            })->get();
            try {
                foreach ($roles as $role) {
                    array_push($permissionroles, $role->name);
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        $relationString = 'roles';
        //verify permissions
        if ($isPermissionWise) {
            HelperService::verifyPermissions($rolesOrPermissions);
        }
        //get employees
        $get_employees = $this->model->whereActive(true)
            ->when($has_allocation, function ($q) {
                $q->whereHas('allocation');
            })
            ->with(
                'employee_profile',
                'employee_profile.work_type',
                'roles',
                'allocation.customer'
            )
            ->whereHas($relationString, function ($query)
            use ($rolesOrPermissions, $isExclude, $relationString, $permissionroles, $isPermissionWise) {
                if ($isPermissionWise === true) {
                    $rolesOrPermissions = $permissionroles;
                }
                //default exclude admin and super admin
                $query->whereNotIn('name', ['super_admin', 'admin']);
                //if the roles or permissinos are present
                if ($rolesOrPermissions != null) {
                    if ($isExclude === false) {
                        $query->whereIn('name', $rolesOrPermissions);
                    } else {
                        $query->whereNotIn('name', $rolesOrPermissions);
                    }
                }
            });

        if ($customer_id != null) {
            $get_employees->whereHas('allocation', function ($query) use ($customer_id) {
                if (is_array($customer_id)) {
                    $query->whereIn('customer_id', $customer_id);
                } else {
                    $query->where('customer_id', $customer_id);
                }
            });
        }
        return $get_employees->orderBy('first_name')->get();
    }

    /**
     * Update Profile
     *
     * @param \App\Models\User $user
     * @return $content
     */
    public function updateProfile($request)
    {
        if ($id = (int) $request->get('id')) {
            $user_info_data = [
                'full_name' => $request->get('full_name'),
                'username' => $request->get('username'),
                'email' => $request->get('email'),
                'employee_number' => $request->get('employee_number'),
                'phone' => $request->get('phone'),
                'role' => $request->get('role')
            ];
            if (!empty($request->get('password'))) {
                $user_info_data['password'] = bcrypt($request->get('password'));
            }
            User::find($id)->update($user_info_data);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Import user data from excel
     * @param Request $request
     * @return response
     */
    public function userExcelImport($request)
    {
        ini_set('max_execution_time', 3000);
        $import_success_row_no = [];
        $row_no = [];
        if ($request->hasFile('import_file') && in_array($request->file('import_file')->getClientOriginalExtension(), ['xls', 'xlsx'])) {
            try {
                DB::beginTransaction();
                $import_success_count = 0;
                $roles_values = $this->roleModel->pluck('name')->toArray();
                $roles = array_map('strtolower', $roles_values);
                $path = $request->file('import_file')->getRealPath();
                $excel = IOFactory::load($path);
                $sheet = $excel->getSheetByName('Employee Information');
                if ($sheet != null) {
                    $row = $sheet->getHighestRow();
                    $row_count = $row - 1;
                    if (!empty($sheet)) {
                        $desired_arr = array();
                        $y = 1;
                        $security_clearance_list = array_map('strtolower', SecurityClearanceLookup::pluck('security_clearance')->toArray());

                        //Get all security clearance value and corresponding id from the respective cells
                        for ($cell_value = 'Y'; $cell_value < 'ZZ'; $cell_value++) {
                            ${'securityClearance_' . $y . '_title'} = $sheet->getCell($cell_value . 1)->getValue();
                            if (${'securityClearance_' . $y . '_title'} != null) {
                                if (in_array(strtolower(trim(${'securityClearance_' . $y . '_title'})), $security_clearance_list)) {
                                    $desired_arr[] = $cell_value;
                                    ${'securityClearance_' . $y . '_title_id'} = $this->getSecurityClearanceLookupId(${'securityClearance_' . $y . '_title'});
                                } else {
                                    return 'The title "' . ${'securityClearance_' . $y . '_title'} . '" is not found in security clearances.';
                                }
                                $y++;
                                $cell_value++;
                            }
                        }
                        for ($i = 2; $i <= $row; $i++) {
                            $usernames = $this->model->whereActive(true)->pluck('username')->toArray();
                            $emails = $this->model->whereActive(true)->pluck('email')->toArray();
                            $employee_nos = $this->employeeModel->whereActive(true)->pluck('employee_no')->toArray();
                            $positions = PositionLookup::pluck('position')->toArray();
                            $work_types_values = WorkType::pluck('type')->toArray();
                            $work_types = array_map('strtolower', $work_types_values);
                            $active = array('Yes', 'yes', 'No', 'no');
                            $veteran_status = array('Yes', 'yes', 'No', 'no');

                            $employee_number = $sheet->getCell('A' . $i)->getValue();

                            $role = str_replace(' ', '_', strtolower($sheet->getCell('H' . $i)->getValue()));
                            $work_type = strtolower($sheet->getCell('W' . $i)->getValue());
                            $postal_code = $sheet->getCell('P' . $i)->getValue();

                            if (!empty($employee_number) && preg_match('/^\d{6}$/', $employee_number) && !empty($postal_code) && preg_match('/^([A-Za-z]\d){3}$/', $postal_code) && !empty($sheet->getCell('B' . $i)->getValue()) && !empty($sheet->getCell('D' . $i)->getValue()) && !empty($sheet->getCell('E' . $i)->getValue()) && !empty($sheet->getCell('G' . $i)->getValue()) && !empty($sheet->getCell('H' . $i)->getValue()) && !empty($sheet->getCell('W' . $i)->getValue())) {
                                if (!in_array($sheet->getCell('A' . $i)->getValue(), $employee_nos) && !in_array($sheet->getCell('D' . $i)->getValue(), $usernames) && !in_array($sheet->getCell('E' . $i)->getValue(), $emails) && in_array($role, $roles) && in_array($work_type, $work_types) && in_array($sheet->getCell('I' . $i)->getValue(), $active)) {
                                    array_push($import_success_row_no, $i);

                                    $active = $sheet->getCell('I' . $i)->getValue();
                                    if ($active == 'Yes' || $active == 'yes') {
                                        $active_status = 1;
                                    } else {
                                        $active_status = 0;
                                    }

                                    $user = [
                                        'first_name' => $sheet->getCell('B' . $i)->getValue(),
                                        'last_name' => $sheet->getCell('C' . $i)->getValue(),
                                        'username' => $sheet->getCell('D' . $i)->getValue(),
                                        'email' => $sheet->getCell('E' . $i)->getValue(),
                                        'alternate_email' => $sheet->getCell('F' . $i)->getValue(),
                                        'password' => bcrypt($sheet->getCell('G' . $i)->getValue()),
                                        'active' => $active_status,
                                    ];
                                    $user = $this->model->create($user);
                                    $this->model->find($user->id)->syncRoles([$role]);

                                    if (in_array($sheet->getCell('V' . $i)->getValue(), $positions)) {
                                        $position = $sheet->getCell('V' . $i)->getValue();
                                        $position_id = PositionLookup::where('position', $position)->value('id');
                                    } else {
                                        $position_id = null;
                                    }

                                    $work_type_id = WorkType::where('type', $work_type)->value('id');

                                    if (in_array($sheet->getCell('X' . $i)->getValue(), $veteran_status)) {
                                        $vet_status = $sheet->getCell('X' . $i)->getValue();
                                        if ($vet_status == 'Yes' || $vet_status == 'yes') {
                                            $employee_vet_status = 1;
                                        } else {
                                            $employee_vet_status = 0;
                                        }
                                    } else {
                                        $employee_vet_status = null;
                                    }

                                    $employee_doj_date = $sheet->getCell('T' . $i)->getValue();
                                    $employee_doj = $employee_doj_date != null ? Date::excelToDateTimeObject($employee_doj_date) : null;
                                    $employee_dob_date = $sheet->getCell('U' . $i)->getValue();
                                    $employee_dob = $employee_dob_date != null ? Date::excelToDateTimeObject($employee_dob_date) : null;

                                    $employee = [
                                        'user_id' => $user->id,
                                        'phone' => $sheet->getCell('J' . $i)->getValue(),
                                        'cell_no' => $sheet->getCell('K' . $i)->getValue(),
                                        'phone_ext' => $sheet->getCell('L' . $i)->getValue(),
                                        'current_project_wage' => $sheet->getCell('M' . $i)->getValue(),
                                        'employee_no' => $employee_number,
                                        'employee_city' => $sheet->getCell('N' . $i)->getValue(),
                                        'employee_address' => $sheet->getCell('O' . $i)->getValue(),
                                        'employee_postal_code' => $postal_code,
                                        'employee_lat' => $sheet->getCell('Q' . $i)->getValue(),
                                        'employee_long' => $sheet->getCell('R' . $i)->getValue(),
                                        'employee_work_email' => $sheet->getCell('S' . $i)->getValue(),
                                        'employee_doj' => $employee_doj != null ? $employee_doj->format('Y-m-d') : null,
                                        'employee_dob' => $employee_dob != null ? $employee_dob->format('Y-m-d') : null,
                                        'position_id' => $position_id,
                                        'work_type_id' => $work_type_id,
                                        'employee_vet_status' => $employee_vet_status,
                                        'active' => $active_status,
                                    ];
                                    $employee = $this->employeeModel->create($employee);

                                    foreach ($desired_arr as $key => $excel_cell_value) {
                                        $j = $key + 1;
                                        $cell_value = $excel_cell_value;
                                        ${'securityClearance_' . $j . '_value'} = $sheet->getCell($excel_cell_value . $i)->getValue();

                                        if (${'securityClearance_' . $j . '_value'} != null && strtolower(${'securityClearance_' . $j . '_value'}) != 'no') {
                                            ${'securityClearance_' . $j . '_valid_until_date'} = $sheet->getCell(++$cell_value . $i)->getValue();
                                            if (strtolower(${'securityClearance_' . $j . '_value'}) == 'yes' && ${'securityClearance_' . $j . '_valid_until_date'} != null) {
                                                ${'securityClearance_' . $j . '_date'} = ${'securityClearance_' . $j . '_valid_until_date'} != null ? Date::excelToDateTimeObject(${'securityClearance_' . $j . '_valid_until_date'}) : null;
                                                ${'securityClearance_' . $j} = [
                                                    'user_id' => $user->id,
                                                    'security_clearance_lookup_id' => ${'securityClearance_' . $j . '_title_id'},

                                                    'value' => ${'securityClearance_' . $j . '_value'},
                                                    'valid_until' => ${'securityClearance_' . $j . '_date'} != null ? ${'securityClearance_' . $j . '_date'}->format('Y-m-d') : null,
                                                ];

                                                ${'securityClearance_' . $j} = $this->securityClearanceUser->create(${'securityClearance_' . $j});
                                            }
                                        }
                                        if ($j == count($desired_arr)) {
                                            break;
                                        }
                                    }

                                    $import_success_count++;
                                }
                            }
                            array_push($row_no, $i);
                        }

                        $import_failed_row_no = array_diff($row_no, $import_success_row_no);
                        $rows = implode(", ", $import_failed_row_no);
                        if ($import_success_count > 0) {
                            DB::commit();
                            $import_result = $import_success_count . ' user information(s) successfully imported out of ' . $row_count;
                            if ($rows != null) {
                                $import_result = $import_result . '. user information(s) of row number(s) ' . $rows . ' was not imported';
                            }
                            return $import_result;
                        } else {
                            return 'User information(s) of row number(s) ' . $rows . ' was not imported';
                        }
                    }
                    return 'Please import an excel file with employee informations';
                }
                return 'Please import an excel file with "Employee Information" sheet';
            } catch (\Exception $e) {
                DB::rollBack();
                return 'User information(s) import was unsuccessful';
            }
        } else {
            return 'Please import an excel file of format xlsx and xls';
        }
    }

    /**
     * Get security clearance lookup id
     * @param  $securityClearance
     * @return id
     */
    public function getSecurityClearanceLookupId($securityClearance)
    {
        return $this->securityClearanceLookupModel->where('security_clearance', $securityClearance)->value('id');
    }

    /**
     * FOR APP-User login
     *
     * @param $request
     * @param string $permission
     * @return true/false
     */
    public function loginForApp($request, $permission = 'allow-mobile-app-login')
    {
        if ((Auth::attempt([
                'username' => $request->get('username'),
                'password' => $request->get('password'),
                'active' => 1
            ])) &&
            Auth::user()->hasPermissionTo($permission)
        ) {
            return Auth::user();
        }
        return false;
    }

    /**
     * FOR APP-Reset password
     *
     * @param \App\Models\User $user
     * @return $content
     */
    public function resetPassword($request)
    {
        $email = $request->get('email');
        $user = User::where(['email' => $email, 'active' => 1])->where('id', '!=', 1)->first();
        if ($user) {
            $content['for_test_new_pass'] = $random_password = str_random(8);
            $user->password = bcrypt($random_password);
            $user->save();
            /* send email to user */
            Mail::to($email)->queue(new \Modules\Timetracker\Mail\ForgotPassword($random_password));
            $content['success'] = true;
            $content['message'] = 'Your password has been reset and the same is sent to your registered mail id';
            $content['code'] = 200;
        } else {
            $content['success'] = false;
            $content['message'] = 'Sorry no records has been found';
            $content['code'] = 401;
        }
        return $content;
    }

    /**
     * FOR APP-Update Profile
     *
     * @param \App\Models\User $user
     * @return $content
     */
    public function update($request)
    {
        /* If API reqest */
        if ($request->wantsJson()) {
            $user = Auth::user();
            $firstName = $request->get('firstName');
            $current_password = $request->get('currentPassword');
            $new_password = $request->get('newPassword');
            if (!empty($new_password)) {
                if (!$this->updatePassword($new_password)) {
                    $content['success'] = false;
                    $content['message'] = 'Current password do not match with the records';
                    $content['code'] = 406;
                    return $content;
                }
            }
            if (!empty($firstName)) {
                $lastName = $request->get('lastName');
                $email = $request->get('email');
                $phone = $request->get('phone');
                $request->image;
                if (!empty($request->image)) {
                    $filename = $this->saveImage($request, $user->id);
                    $edit_arr['image'] = $filename;
                }
                if (!empty($email)) {
                    $user_email = User::where('email', '=', $email)->where('id', '!=', $user->id)->first();
                    if ($user_email != null) {
                        $content['success'] = false;
                        $content['message'] = 'Email already exists';
                        $content['code'] = 406;
                        return $content;
                    }
                }
                if ($phone != null) {
                    if (preg_match("/\d{10}/", $phone) && strlen($phone) == 10) {
                        $split_string = str_split($phone, 3);
                        $format_phone = ('(' . $split_string[0] . ')' . $split_string[1] . '-' . $split_string[2] . $split_string[3]);
                    } elseif (preg_match("/\([0-9]{3}\)[0-9]{3}-[0-9]{4}/", $phone) && strlen($phone) == 13) {
                        $format_phone = $phone;
                    } else {
                        $content['success'] = false;
                        $content['message'] = 'Please give a valid phone number';
                        $content['code'] = 406;
                        return $content;
                    }
                    $edit_arr['phone'] = $format_phone;
                    $user->first_name = $firstName;
                    $user->last_name = $lastName;
                    if ($email != null) {
                        $user->email = $email;
                    }
                    $user->save();
                    Employee::where(['user_id' => $user->id])
                        ->update($edit_arr);
                    $content['success'] = true;
                    $content['message'] = 'ok';
                    $content['code'] = 200;
                } else {
                    $content['success'] = false;
                    $content['message'] = 'Please give a valid phone number';
                    $content['code'] = 406;
                }
            } else {
                $content['success'] = false;
                $content['message'] = 'First name should not be blank';
                $content['code'] = 406;
            }
            return $content;
        } else {
            if ($id = (int) $request->get('id')) {
                $user_info_data = [
                    'first_name' => $request->get('name'),
                    'username' => $request->get('username'),
                    'last_name' => $request->get('last_name'),
                    'email' => $request->get('email'),
                    'role_id' => $request->get('role_id')
                ];

                if (!empty($request->get('password'))) {
                    $user_info_data['password'] = bcrypt($request->get('password'));
                }

                $user_profile_data = [
                    'employee_no' => $request->get('employee_no'),
                    'phone' => $request->get('phone'),
                    'work_type_id' => $request->get('work_type_id'),
                ];
                User::find($id)->update($user_info_data);
                Employee::where('user_id', $id)->update($user_profile_data);
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Save Image
     *
     * @param type $name Description
     * @return type Description
     */
    public function saveImage($request, $user_id)
    {
        $base64_str = $request->image;
        $filename = $user_id . "_profile";
        $img_arr = explode(',', $base64_str);
        if (isset($img_arr[0]) && $img_arr[0] != "" && count($img_arr) > 1) {
            $image_type_arr = explode(';', $img_arr[0]);
        }
        if (isset($image_type_arr[0]) && $image_type_arr[0] != "") {
            $extension = $this->getExtension($image_type_arr[0]);
            $image = base64_decode($img_arr[1]);
        } else {
            $extension = "png";
            $image = $base64_str;
        }
        $path = public_path() . $this->imageUploadPath . $filename . "." . $extension;
        if (!file_exists(public_path() . $this->imageUploadPath)) {
            mkdir(public_path() . $this->imageUploadPath, 0777, true);
        }
        $entry = file_put_contents($path, $image);
        return $filename . "." . $extension;
    }

    public function getExtension($base64_type)
    {
        switch ($base64_type) {
            case "data:image/jpeg":
                return "jpg";
                break;
            case "data:image/png":
                return "png";
                break;
            default:
                return "png";
        }
    }

    /**
     * Update password
     *
     * @param type $name Description
     * @return type Description
     */
    public function updatePassword($new_password)
    {
        $user_id = Auth::User()->id;
        $obj_user = User::find($user_id);
        $obj_user->password = bcrypt($new_password);
        $obj_user->save();

        return true;
    }

    /**
     * Function to get details of a single user in
     * @param type $user_id
     */
    public function getFormatedUserDetails($user_id)
    {
        $user_details = $this->getUserDetails($user_id);
        $formated_user_details['id'] = $user_id;
        $formated_user_details['first_name'] = isset($user_details->first_name) ? $user_details->first_name : '--';
        $formated_user_details['last_name'] = isset($user_details->last_name) ? $user_details->last_name : '--';
        $formated_user_details['full_name'] = $formated_user_details['first_name'] . (($formated_user_details['last_name'] != '--') ? ' ' . $user_details->last_name : '');
        $formated_user_details['email'] = isset($user_details->email) ? $user_details->email : '--';
        $formated_user_details['employee_role_id'] = isset($user_details->roles) ? $user_details->roles->first()->id : '--';
        $formated_user_details['employee_role_name'] = isset($user_details->roles) ? $user_details->roles->first()->name : '--';
        $formated_user_details['employee_address'] = isset($user_details->employee->employee_address) ? $user_details->employee->employee_address : '--';
        $formated_user_details['employee_city'] = isset($user_details->employee->employee_city) ? $user_details->employee->employee_city : '--';
        $formated_user_details['employee_no'] = isset($user_details->employee->employee_no) ? $user_details->employee->employee_no : '--';
        $formated_user_details['employee_postal_code'] = isset($user_details->employee->employee_postal_code) ? $user_details->employee->employee_postal_code : '--';
        $phone = isset($user_details->employee->phone) ? $user_details->employee->phone : '--';
        $phone_ext = isset($user_details->employee->phone_ext) ? ' x' . $user_details->employee->phone_ext : '';
        $formated_user_details['phone'] = $phone . $phone_ext;
        $formated_user_details['employee_work_email'] = isset($user_details->employee->employee_work_email) ? $user_details->employee->employee_work_email : '--';
        $formated_user_details['project_number'] = (isset($user_details->allocation)) ? (!empty($user_details->allocation->last()->customer) ? ($user_details->allocation->last()->customer->project_number) : '--') : '--';
        $formated_user_details['client_name'] = (isset($user_details->allocation)) ? (!empty($user_details->allocation->last()->customer) ? ($user_details->allocation->last()->customer->client_name) : '--') : '--';
        $formated_user_details['employee_dob'] = isset($user_details->employee->employee_dob) ? $user_details->employee->employee_dob : '--';
        $formated_user_details['current_project_wage'] = isset($user_details->employee->current_project_wage) ? $user_details->employee->current_project_wage : '--';
        $formated_user_details['age'] = isset($user_details->employee->employee_dob) ? ($user_details->employee->age) : '--';
        $formated_user_details['employee_doj'] = isset($user_details->employee->employee_doj) ? $user_details->employee->employee_doj : '--';
        $formated_user_details['service_length'] = isset($user_details->employee->employee_doj) ? ($user_details->employee->service_length) : '--';
        $formated_user_details['employee_vet_status'] = isset($user_details->employee) ? (($user_details->employee->employee_vet_status === 1) ? "Yes" : ($user_details->employee->employee_vet_status === 0 ? "No" : "--")) : '--';
        $formated_user_details['security_clearance'] = isset($user_details->securityClearanceUser) ? (!($user_details->securityClearanceUser)->isEmpty() ? ($user_details->securityClearanceUser->last()->securityClearanceLookups->security_clearance) : '--') : '--';
        $formated_user_details['valid_until'] = isset($user_details->securityClearanceUser) ? (!($user_details->securityClearanceUser)->isEmpty() ? ($user_details->securityClearanceUser->last()->valid_until) : '--') : '--';
        $formated_user_details['employee_rating'] = isset($user_details->employee->employee_rating) ? $user_details->employee->employee_rating : '--';
        $formated_user_details['position'] = isset($user_details->employee->employeePosition->position) ? $user_details->employee->employeePosition->position : '--';
        $formated_user_details['all_security_clearance'] = isset($user_details->securityClearanceUser) ? (!($user_details->securityClearanceUser)->isEmpty() ? ($user_details->securityClearanceUser) : null) : null;

        return $formated_user_details;
    }

    /**
     * Get Role
     *
     * @param type $name Description
     * @return type Description
     */
    public function getRoleDetails($id)
    {
        $data = Role::find($id);
        return $data;
    }

    /**
     * Get All users based on array of user id.
     *
     * @param $user_ids $active $user_ids_except
     * @return user data
     */

    //todo: Need to change to permission based
    public function getAllByUserIds(
        $user_ids = [],
        $active = null,
        $user_ids_except = null,
        $exlcludeRoleOrPermission = null,
        $isPermissionWise = false
    ) {
        $removeusers = [];
        $filterbyroleorpermission = null;
        if ($exlcludeRoleOrPermission != null) {
            if (count($exlcludeRoleOrPermission) > 0) {
                if ($isPermissionWise == true) {
                    $removeusers = User::permission($exlcludeRoleOrPermission)->get()->pluck('id')->toArray();
                } else {
                    $removeusers = User::role($exlcludeRoleOrPermission)->get()->pluck('id')->toArray();
                }
            }
        }
        if ($isPermissionWise == false) {
            $filterbyroleorpermission = "roles";
        } else {
            $filterbyroleorpermission = "roles.permissions";
        }
        $user_lookup_obj = User::when(($active !== null), function ($query) use ($active) {
            return $query->where('active', "=", $active);
        });

        $user_lookup_obj->where(function ($query1) use ($user_ids) {
            if (!empty($user_ids)) {
                $query1->whereIn('id', $user_ids);
            }
        });

        $user_lookup_obj->when(($user_ids_except !== null), function ($query1) use ($user_ids_except) {
            if (!empty($user_ids_except)) {
                $query1->whereNotIn('id', $user_ids_except);
                $query1->with('employee');
            }
        });
        $user_lookup_obj->when(($removeusers !== null), function ($query1) use ($removeusers) {
            if (!empty($removeusers)) {
                $query1->whereNotIn('id', $removeusers);
                $query1->with('employee');
            }
        });
        $user_lookup = $user_lookup_obj->get();
        return $user_lookup;
    }

    /**
     * Function to get UsersIdList
     * @param null
     * @return array [User dropdown with name and number]
     */
    public function getAllUsersID()
    {
        $user_id = $this->model->pluck('id')->toArray();
        return $user_id;
    }
    /**
     * Function to get UsersDropdownList
     * @param   $id_arr
     * @return array [User dropdown with name and number]
     */

    //todo: Need to change to permission based
    public function getUsersDropdownList($user_list = null, $exlcludeRoleOrPermission = null, $isPermissionWise = false)
    {
        $removeusers = [];
        if ($exlcludeRoleOrPermission != null) {
            if ($isPermissionWise == true) {
                $removeusers = User::permission($exlcludeRoleOrPermission)->get()->pluck('id')->toArray();
            } else {
                $removeusers = User::role($exlcludeRoleOrPermission)->get()->pluck('id')->toArray();
            }
        }
        $relation = $isPermissionWise ? 'roles.permissions' : 'roles';
        $user_details_arr = $this->model->with('employee')->whereActive(true);
        if (!empty($user_list)) {
            $user_details_arr->whereIn('id', $user_list);
        }
        $user_details_arr->when(($exlcludeRoleOrPermission != null), function ($user_details_arr)
        use ($exlcludeRoleOrPermission, $relation, $removeusers) {
            $user_details_arr->with($relation);
            if (count($removeusers) > 0) {
                $user_details_arr->whereNotIn('id', $removeusers);
            }
        });
        $user_details_arr = $user_details_arr->orderBy('first_name')->get();

        $users_arr = array();
        foreach ($user_details_arr as $key => $users) {
            $emp_no = $users->employee['employee_no'] ? ' (' . $users->employee['employee_no'] . ')' : '';
            $name = $users['full_name'] . $emp_no;
            $id = $users['id'];
            $users_arr[] = array('id' => $id, 'name' => $name);
        }
        return $users_arr;
    }

    public function uploadProfileImage($request, $id, $identifier = 'profile')
    {
        $image_name = null;
        if ($request->has('image') && !empty($request->image) && ($request->image != null)) {
            $image = $request->image;
            $imageArray = explode(';', $image);
            if (count($imageArray) > 1) {
                list($type, $image) = explode(';', $image);
                list(, $image) = explode(',', $image);
                $fileDetails = explode('/', $type);

                if (count($fileDetails) == 2) {
                    $fileExtension = $fileDetails[1];
                    $image = base64_decode($image);
                    $image_name = $id . "_" . $identifier . '.' . $fileExtension;
                    $path = public_path($this->imageUploadPath . '/' . $image_name);
                    file_put_contents($path, $image);
                }
            }
        }
        return $image_name;
    }
}
