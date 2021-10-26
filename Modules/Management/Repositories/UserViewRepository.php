<?php

namespace Modules\Management\Repositories;

use \Carbon\Carbon;
use Modules\Admin\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Models\UserCertificate;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\SecurityClearanceUser;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Expense\Models\ExpenseAllowableForUser;
use Modules\Management\Http\Requests\UserTabRequest;
use Modules\Management\Http\Requests\UserProfileTabRequest;
use Modules\Management\Http\Requests\SecurityClearanceRequest;
use Modules\Management\Http\Requests\UserCertificatesRequest;
use Modules\Management\Http\Requests\UserSkillRequest;
use Modules\Hranalytics\Models\Candidate;
use Modules\Admin\Models\UserSkillUserValue;
use Modules\Admin\Models\UserSkillOptionAllocation;
use Modules\Admin\Models\UserSkillOptionValue;
use Config;
use DB;
use File;
use App\Services\HelperService;

class UserViewRepository
{
    public function __construct(UserRepository $userRepository)
    {

        $this->employeeAllocationModel = new EmployeeAllocation();
        $this->userRepository=$userRepository;
        $this->imageUploadPath = Config::get('globals.profilePicPath');
    }

    public function userTabStore(UserTabRequest $request, $id)
    {
        $roles = ($id) ? User::find($id)->getRoleNames() : '';
        $prevRoleId = ($roles[0]) ?? '';
        if (($request->get('role') != $prevRoleId) && ($prevRoleId != '')) {
            $user_allocation_update = $this->employeeAllocationModel->where('supervisor_id', $id)
            ->update(['to' => date('Y-m-d'), 'updated_by' => \Auth::user()->id]);
            $user_allocation_delete = $this->employeeAllocationModel->where('supervisor_id', $id)
            ->orWhere('user_id', $id)->delete();
        }
        /** Unallocate employees if role changes   -  End **/

        User::find($id)->syncRoles([$request->get('role')]);

        $userData = [
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'alternate_email' => $request->get('alternate_email'),
            'username' => $request->get('username'),
        ];
        if (!empty($request->get('password'))) {
            $userData['password'] = bcrypt($request->get('password'));
        }
        //dd($userData);
        User::find($id)->update($userData);

        return response()->json(array('success' => true));
    }

    public function expenseTabStore(Request $request, $id)
    {
        $expenseData = [
            'reporting_to_id' => $request->get('reporting_to_id'),
            'max_allowable_expense' => $request->get('max_allowable_expense'),
            'user_id'=>$request->get('id'),
        ];

        ExpenseAllowableForUser::updateOrCreate(['user_id' => $id], $expenseData);
        return response()->json(array('success' => true));
    }


    public function userCertificateStore(UserCertificatesRequest $request, $id)
    {
        UserCertificate::where('user_id', $id)->delete();
        $certificateRowNos = $request->get('certificate-row-no');

        if ($certificateRowNos != null) {
            foreach ($certificateRowNos as $row_no) {
                $certificateId = intval($request->get('certificate_' . $row_no));
                $expiry = $request->get('expiry_' . $row_no);
                if ($certificateId != 0) {
                    $certificateData = [
                     'certificate_id' => $certificateId,
                     'expires_on' => $expiry,
                    ];
                    $certificateData['user_id'] = $id;

                    UserCertificate::updateOrCreate(
                        ['user_id' => $id, 'certificate_id' => $certificateId],
                        $certificateData
                    );
                }
            }
            return response()->json(array('success' => true));
        }
    }

    public function userSecurityClearanceStore(SecurityClearanceRequest $request, $id)
    {
        SecurityClearanceUser::where('user_id', $id)->delete();
        $rowNos = $request->get('row-no');
        if ($rowNos != null) {
            foreach ($rowNos as $row_no) {
                $securityClearanceLookupId = intval($request->get('security_clearance_' . $row_no));
                $valid_until = $request->get('valid_until_' . $row_no);
                if ($securityClearanceLookupId != 0) {
                    $securityClearanceData = [
                        'security_clearance_lookup_id' => $securityClearanceLookupId,
                        'value' => 'Yes',
                        'valid_until' => $valid_until,
                    ];
                    $securityClearanceData['user_id'] = $id;
                    SecurityClearanceUser::updateOrCreate(['user_id' => $id,
                    'security_clearance_lookup_id' => $securityClearanceLookupId], $securityClearanceData);
                }
            }
            return response()->json(array('success' => true));
        }
    }

    public function userSkillStore(UserSkillRequest $request, $id)
    {
        UserSkillUserValue::where('user_id', $id)->delete();
        $skill_row_nos = $request->get('skill-row-no');
        // dd($request->all());
        if ($skill_row_nos != null) {
            foreach ($skill_row_nos as $row_no) {
                $skill_id = intval($request->get('skill_' . $row_no));
                $user_option_value_id = $request->get('skillvalue_' . $row_no);
                $user_skill_option_values=  UserSkillOptionValue::find($user_option_value_id);
                $option_allocation=UserSkillOptionAllocation::where('user_skill_id', $skill_id)->where('user_skill_option_id', $user_skill_option_values->user_skill_option_id)->first();
                if ($skill_id != 0) {
                    $skill_data = [
                        'user_skill_option_allocation_id' => $option_allocation->id,
                        'user_option_value_id' => $user_option_value_id,
                    ];
                    $skill_data['user_id'] = $id;
                    UserSkillUserValue::create($skill_data);
                }
            }
            return response()->json(array('success' => true));
        }
    }

    public function profileTabStore(UserProfileTabRequest $request, $id)
    {
        $employeeData = [
            'employee_no' => $request->get('employee_no'),
            'work_type_id' => $request->get('work_type_id'),
            'phone' => $request->get('phone'),
            'phone_ext' => $request->get('phone_ext'),
            'cell_no' => $request->get('cell_no'),
            'employee_address' => $request->get('employee_address'),
            'employee_city' => $request->get('employee_city'),
            'employee_postal_code' => $request->get('employee_postal_code'),
            'employee_work_email' => $request->get('employee_work_email'),
            'employee_vet_status' => $request->get('employee_vet_status'),
            'vet_release_date' => $request->get('vet_release_date'),
            'vet_enrollment_date' => $request->get('vet_enrollment_date'),
            'vet_service_number' => $request->get('vet_service_number'),
            'employee_doj' => $request->get('employee_doj'),
            'employee_dob' => $request->get('employee_dob'),
            'current_project_wage' => $request->get('current_project_wage'),
            'position_id' => $request->get('position_id'),
            'years_of_security' => $request->get('years_of_security'),
            'being_canada_since' => $request->get('being_canada_since'),
            'wage_expectations_from' => $request->get('wage_expectations_from'),
            'wage_expectations_to' => $request->get('wage_expectations_to'),
            'employee_postal_code'=> $request->get('employee_postal_code'),
        ];

        $active = $request->get('active');
        if ($active == null && isset($id)) {
            $user_data['active'] = 0;
            $employeeData['active'] = 0;
        } else {
            $user_data['active'] = 1;
            $employeeData['active'] = 1;
        }
        if ($request->has('candidate_id')) {
            $candidate = Candidate::find($request->get('candidate_id'));
            if (!empty($candidate) && !empty($candidate->profile_image)) {
                $uploadRootFolder = public_path() . '/images/uploads/';
                $oldImageFullPath = $uploadRootFolder . $candidate->profile_image;
                $newFileName = $id . '_profile.png';
                $newImageFullPath = $uploadRootFolder . $newFileName;
                $fileCopied = File::copy($oldImageFullPath, $newImageFullPath);

                if ($fileCopied) {
                    $employeeData['image'] = $newFileName;
                }
            }
        }
        $userId = $id;
        $employeeImageName = $this->uploadProfileImage($request, $userId);
        if ($employeeImageName != null) {
            $employeeData['image'] = $employeeImageName;
        }
        $employeeData['years_of_security'] = $request->get('years_of_security');
        $employeeData['being_canada_since'] = $request->get('being_canada_since');
        Employee::updateOrCreate(['user_id' => $id], $employeeData);
        return response()->json(array('success' => true));
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

    public function employeeLookUps()
    {
        $userList = array();
        $user = \Auth::user();
        if (\Auth::user()->can('user_view') || $user->hasAnyPermission(['admin', 'super_admin'])) {
            $userList = $this->userRepository->getUserLookup(null, ['admin','super_admin'], null, true, null, true)
            ->orderBy('first_name', 'asc')
            ->get();
        } else {
            $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
            $userList = $this->usermodel
            ->whereIn('id', $employees)
            ->get();
        }

        return $userList;
    }



    public function getUserTableList($active = null, $employeenameId = null)
    {
        $today = Carbon::now()->toDateString();
            $securityClearanceExpiry = SecurityClearanceUser::where('valid_until', '<', $today)
            ->pluck('user_id')->unique()->toArray();
            $certificateExpiry = UserCertificate::where('expires_on', '<', $today)
            ->pluck('user_id')->unique()->toArray();
            $result = array_unique(array_merge($securityClearanceExpiry, $certificateExpiry));

        $userTableList = $this->userRepository
        ->getUserList($active, null, null, ['super_admin'], false, false, $employeenameId)
        ->map(function ($item) use ($result) {
            $statusId=$item->id;
            if (in_array($statusId, $result)) {
                $color="red";
            } else {
                $color="white";
            }
            return [
                'id' => $item->id,
                'emp_no' => $item->employee->employee_no,
                'phone' => $item->employee->phone,
                'phone_ext' => $item->employee->phone_ext,
                'full_name' => $item->full_name,
                'username' => $item->username,
                'email' => $item->email,
                'status_color' => $color,
                'created_at' => (isset($item->created_at)) ? $item->created_at->toDateTimeString() : "",
                'updated_at' => (isset($item->updated_at)) ? $item->updated_at->toDateTimeString() : "",
                'roles' => implode(', ', array_map(array(HelperService::class, "snakeToTitleCase"), data_get($item, 'roles.*.name'))),
            ];
        });


        return ($userTableList);
    }
}
