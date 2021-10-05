<?php

namespace Modules\Admin\Repositories;

use Auth;
use DB;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Models\ContractualVisitUnitLookup;
use Modules\Admin\Models\CpidCustomerAllocations;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\CustomerIncidentSubjectAllocation;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\Geofence;
use Modules\Admin\Models\IndustrySectorLookup;
use Modules\Admin\Models\RegionLookup;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerIncidentPriorityRepository;
use Modules\Admin\Repositories\CustomerIncidentSubjectAllocationRepository;
use Modules\Admin\Repositories\CustomerQrcodeLocationRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\IncidentCategoryRepository;
use Modules\Admin\Repositories\IncidentReportSubjectRepository;
use Modules\Admin\Repositories\IndustrySectorLookupRepository;
use Modules\Admin\Repositories\ParentCustomerRepository;
use Modules\Admin\Repositories\RegionLookupRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Hranalytics\Models\Candidate;
use Modules\Hranalytics\Repositories\CustomerStcDetailsRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Modules\Recruitment\Models\RecDocumentAllocation;
use Modules\Recruitment\Models\RecJobDocumentAllocation;
use Illuminate\Support\Arr;

class CustomerRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $customerModel;
    protected $regionLookupRepository;
    protected $industrySectorLookupRepository;
    protected $regionLookupModel;
    protected $industrySectorLookupModel;
    protected $employeeModel;
    protected $customerEmployeeAllocationModel;
    protected $userRepository;
    protected $parentcustomerrepository;
    protected $customerQrcodeLocationRepository;
    protected $incidentReportSubjectRepository;
    protected $customerIncidentSubjectAllocationRepository;
    protected $customerIncidentPriorityRepository;
    protected $incidentCategoryRepository, $securityClearanceLookupRepository, $stcDetailsRepository;

    /**
     * Create a new Repository instance.
     *
     * @param Customer $customerModel
     * @param \Modules\Admin\Repositories\RegionLookupRepository $regionLookupRepository
     * @param \Modules\Admin\Repositories\IndustrySectorLookupRepository $industrySectorLookupRepository
     * @param Candidate $candidateModel
     * @param RegionLookup $regionLookupModel
     * @param IndustrySectorLookup $industrySectorLookupModel
     * @param Employee $employeeModel
     * @param CustomerEmployeeAllocation $customerEmployeeAllocationModel
     * @param \Modules\Admin\Repositories\EmployeeAllocationRepository $employeeAllocationRepository
     * @param \Modules\Admin\Repositories\UserRepository $userRepository
     * @param PositionLookupRepository $positionLookupRepository
     * @param \Modules\Admin\Repositories\ParentCustomerRepository $parentcustomerrepository
     * @param CpidLookupRepository $cpidRepository
     * @param \Modules\Admin\Repositories\CustomerQrcodeLocationRepository $customerQrcodeLocationRepository
     * @param \Modules\Admin\Repositories\IncidentReportSubjectRepository $incidentReportSubjectRepository
     * @param \Modules\Admin\Repositories\CustomerIncidentSubjectAllocationRepository $customerIncidentSubjectAllocationRepository
     * @param \Modules\Admin\Repositories\CustomerIncidentPriorityRepository $customerIncidentPriorityRepository
     * @param \Modules\Admin\Repositories\IncidentCategoryRepository $incidentCategoryRepository
     */
    public function __construct(
        Customer $customerModel,
        RegionLookupRepository $regionLookupRepository,
        IndustrySectorLookupRepository $industrySectorLookupRepository,
        Candidate $candidateModel,
        RegionLookup $regionLookupModel,
        IndustrySectorLookup $industrySectorLookupModel,
        Employee $employeeModel,
        CustomerEmployeeAllocation $customerEmployeeAllocationModel,
        EmployeeAllocationRepository $employeeAllocationRepository,
        UserRepository $userRepository,
        PositionLookupRepository $positionLookupRepository,
        ParentCustomerRepository $parentcustomerrepository,
        CpidLookupRepository $cpidRepository,
        CustomerQrcodeLocationRepository $customerQrcodeLocationRepository,
        IncidentReportSubjectRepository $incidentReportSubjectRepository,
        CustomerIncidentSubjectAllocationRepository $customerIncidentSubjectAllocationRepository,
        CustomerIncidentPriorityRepository $customerIncidentPriorityRepository,
        IncidentCategoryRepository $incidentCategoryRepository,
        SecurityClearanceLookupRepository $securityClearanceLookupRepository,
        CustomerStcDetailsRepository $stcDetailsRepository
    ) {
        $this->stcDetailsRepository = $stcDetailsRepository;
        $this->customerModel = $customerModel;
        $this->employeeModel = $employeeModel;
        $this->candidateModel = $candidateModel;
        $this->regionLookupModel = $regionLookupModel;
        $this->industrySectorLookupModel = $industrySectorLookupModel;
        $this->regionLookupRepository = $regionLookupRepository;
        $this->industrySectorLookupRepository = $industrySectorLookupRepository;
        $this->customerEmployeeAllocationModel = $customerEmployeeAllocationModel;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->userRepository = $userRepository;
        $this->cpidRepository = $cpidRepository;
        $this->positionLookupRepository = $positionLookupRepository;
        $this->parentcustomerrepository = $parentcustomerrepository;
        $this->customerQrcodeLocationRepository = $customerQrcodeLocationRepository;
        $this->incidentReportSubjectRepository = $incidentReportSubjectRepository;
        $this->customerIncidentPriorityRepository = $customerIncidentPriorityRepository;
        $this->customerIncidentSubjectAllocationRepository = $customerIncidentSubjectAllocationRepository;
        $this->incidentCategoryRepository = $incidentCategoryRepository;
        $this->securityClearanceLookupRepository = $securityClearanceLookupRepository;
    }

    /**
     * Get Lookups for customer creations
     * @return array
     */
    public function getLookups()
    {
        $lookups['securityClearanceLookup'] = $this->securityClearanceLookupRepository->getList();
        $lookups['industrySectorLookup'] = $this->industrySectorLookupRepository->getList();
        $lookups['regionLookup'] = $this->regionLookupRepository->getList();
        $lookups['cpidLookup'] = $this->cpidRepository->get_cpid_positions();
        $lookups['positionLookup'] = $this->positionLookupRepository->getList();
        $lookups['requesterLookup'] = $this->userRepository->getUserLookup(null, ['super_admin', 'admin']);
        $lookups['parentcustomerLookup'] = $this->parentcustomerrepository->getParentList();
        $lookups['contractualVisitLookup'] = ContractualVisitUnitLookup::pluck('value', 'id');
        $lookups['subject'] = $this->incidentReportSubjectRepository->getSubjectList();
        //  $lookups['customerincidentPriority']=$this->customerIncidentPriorityRepository->getList();
        $lookups['incidentCategory'] = $this->incidentCategoryRepository->getList();

        return $lookups;
    }

    /**
     * Get customer list
     *
     * @param customer type
     *  STC_CUSTOMER
     *  PERMANENT_CUSTOMER
     *  ALL_CUSTOMER
     * @return array
     */

    public function getCustomerList($customer_type = PERMANENT_CUSTOMER, $customer_status = ACTIVE, $customerids = null)
    {
        $customerList = $this->customerModel->select([
            'project_number',
            'id',
            'client_name',
            'contact_person_name',
            'contact_person_email_id',
            'contact_person_phone',
            'contact_person_position',
            'address',
            'city',
            'facility_booking',
            'province',
            'postal_code',
            'geo_location_lat',
            'geo_location_long',
            'radius',
            'created_at',
            'updated_at',
        ])

            ->when($customer_type != 'ALL_CUSTOMER', function ($q) use ($customer_type) {
                return $q->where('stc', $customer_type);
            })
            ->when($customer_status != 'ACTIVE', function ($q) use ($customer_status) {
                return $q->where('active', $customer_status);
            })
            ->when($customerids != null, function ($q) use ($customerids) {
                return $q->whereIn('id', $customerids);
            })
            ->orderBy('client_name', 'asc')->get();
        return $customerList;
    }

    /**
     * Get customers name list
     * @return array
     */
    public function getCustomersNameList($customer_type = PERMANENT_CUSTOMER, $customer_status = ACTIVE)
    {
        return $this->customerModel->select('id', 'client_name', 'project_number')
            ->when($customer_type !== null, function ($query) use ($customer_type) {
                $query->where('stc', '=', $customer_type);
            })->where('active', $customer_status)
            ->orderBy('client_name', 'asc')
            ->get();
    }

    public function clienLookUps()
    {
         $customerList = $this->customerModel->orderBy('client_name', 'asc')->get();
         return $customerList;
    }
    /**
     * Get customers name list
     * @return array
     */
    public function getAllCustomersNameList($customer_status = ACTIVE)
    {
        return $this->customerModel->select('id', 'client_name', 'project_number')
            ->where('active', $customer_status)
            ->orderBy('client_name', 'asc')
            ->get();
    }

    /**
     * Get customers name list
     * @return array
     */
    public function getAllocatedUserEmail($userId)
    {
        return User::select('id', 'email')->where('id', $userId)->first();
    }

    public function getCustomerWithMangers($customer_id, $latestSupervisor = true)
    {
        $customer_details = array();
        if ($latestSupervisor) {
            $customer_obj = $this->customerModel::with('employeeLatestCustomerSupervisor', 'employeeLatestCustomerAreaManager', 'customerPayperiodTemplate');
        } else {
            $customer_obj = $this->customerModel::with('employeeCustomerSupervisor', 'employeeCustomerAreaManager', 'customerPayperiodTemplate');
        }
        $customer = $customer_obj->orderBy('client_name');
        if (is_array($customer_id)) {
            $customer = $customer_obj->whereIn('id', $customer_id)->get();
            foreach ($customer as $key => $each_customer) {
                $customer_arr = $each_customer->toArray();
                $customer_details[$key]["details"] = Arr::except($customer_arr, ["employee_latest_customer_supervisor", "employee_latest_customer_area_manager"]);
                $customer_details[$key]["areamanager"] = $this->getManagerDetailsArr($each_customer, "area_manager");
                $customer_details[$key]["supervisor"] = $this->getManagerDetailsArr($each_customer, "supervisor");
            }
        } else {
            $customer = $customer_obj->find($customer_id);
            // if ($customer->stc == STC_CUSTOMER) {
            //     return null;
            // }
            $customer_arr = $customer->toArray();
            $customer_details["details"] = Arr::except($customer_arr, ["employee_latest_customer_supervisor", "employee_latest_customer_area_manager"]);
            $customer_details["areamanager"] = $this->getManagerDetailsArr($customer, "area_manager");
            $customer_details["supervisor"] = $this->getManagerDetailsArr($customer, "supervisor");
        }
        return $customer_details;
    }

    /**
     * Get customer list based on stc value
     *
     * @param $stc
     * ACTIVE=1
     * @return array
     */
    public function getList($customer_type = PERMANENT_CUSTOMER, $is_shift_enabled = null, $text_column = 'customer_name_and_number', $status = ACTIVE)
    {

        return $this->customerModel
            ->when($customer_type !== null, function ($query) use ($customer_type) {
                $query->where('stc', '=', $customer_type);
            })
            ->when($is_shift_enabled !== null, function ($query) {
                $query->where('guard_tour_enabled', '=', 1);
            })
            ->where('active', $status)
            ->orderBy('client_name')
            ->get()
            ->pluck($text_column, 'id')
            ->toArray();
    }

    /**
     * Get customer list based on guard tour value
     *
     * @param $is_shift_enabled
     * @return array
     */
    public function getGuardTourCustomerList($text_column = 'project_number')
    {
        return $this->customerModel
        // ->where('guard_tour_enabled', '=', $is_shift_enabled)
            ->pluck($text_column, 'id')
            ->toArray();
    }

    /**
     * build new project array with defined index
     *
     * @param $stc
     * @return array
     */
    public function getProjects($stc)
    {
        $list['projectlist'] = $this->getList($stc);
        foreach ($list as $key => $project_list) {
            $project_arr[$key] = array();
            foreach ($project_list as $project_id => $each_project) {
                $project['id'] = $project_id;
                $project['project_no'] = $each_project;
                array_push($project_arr[$key], $project);
            }
        } //client_name
        return ($project_arr);
    }

    /**
     * Get single customer details
     *
     * @param $id
     * @return object
     */
    public function getSingleCustomer($id)
    {
        $singleRecord = $this->customerModel
            ->with(
                'stcDetails',
                'requesterDetails',
                'Cpids',
                'qrcodeLocations',
                'subjectAllocation',
                'customerPriority.priority',
                'customerType'
            )
            ->find($id);
        return $singleRecord;
    }

    /**
     * Get customer details from array
     *
     * @param $customer_arr - array of customers
     * @return object
     */
    public function getCustomers($customer_arr)
    {
        $customerCollectionArr = $this->customerModel->with('stcDetails')->whereIn('id', $customer_arr)->orderBy('client_name')->get();
        return $customerCollectionArr;
    }

    /**
     * Store a newly created Customer in storage.
     *
     * @param  $data
     * @return object
     */
    public function storeCustomer($data)
    {
        $data['id'] = isset($data['id']) ? $data['id'] : '';
        $previous_postal_code = $this->customerModel->where('id', $data['id'])->value('postal_code');
        $postal_code = $data['postal_code'];
        $google_api_key = config('globals.google_api_curl_key');
        if ($previous_postal_code != $postal_code || $previous_postal_code == null) {
            $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address="
                . $postal_code
                . "&sensor=false&key="
                . $google_api_key);

            $json = json_decode($json);
            if (isset($json->{'results'}[0])) {
                $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
                $data['geo_location_lat'] = $lat;
                $data['geo_location_long'] = $long;
            } else {
                $data['geo_location_lat'] = null;
                $data['geo_location_long'] = null;
                $data['radius'] = null;
            }
        }
        $data['active'] = isset($data['active']) ? 1 : 0;
        $data['shift_journal_enabled'] = isset($data['shift_journal_enabled']) ? 1 : 0;
        $data['time_shift_enabled'] = isset($data['time_shift_enabled']) ? 1 : 0;
        $data['guard_tour_enabled'] = isset($data['guard_tour_enabled']) ? 1 : 0;
        $data['overstay_enabled'] = isset($data['overstay_enabled']) ? 1 : 0;
        $data['show_in_sitedashboard'] = isset($data['show_in_sitedashboard']) ? 1 : 0;

        //stc details
        $stcDetails['nmso_account'] = "no";
        $stcDetails['security_clearance_lookup_id'] = null;
        if (isset($data['is_nmso_account'])) {
            $stcDetails['nmso_account'] = "yes";
            $stcDetails['security_clearance_lookup_id'] = $data['security_clearance_lookup_id'];
            unset($data['is_nmso_account']);
            unset($data['security_clearance_lookup_id']);
        }

        $data['stc'] = isset($data['stc']) ? 1 : 0;
        $data['basement_mode'] = isset($data['basement_mode']) ? 1 : 0;
        $data['geo_fence'] = isset($data['geo_fence']) ? 1 : 0;
        $data['geo_fence_satellite'] = isset($data['geo_fence_satellite']) ? 1 : 0;
        $data['mobile_security_patrol_site'] = isset($data['mobile_security_patrol_site']) ? 1 : 0;
        $data['fence_interval'] = $data['fence_interval'] ?? null;
        $data['contractual_visit_unit'] = $data['contractual_visit_unit'] ?? null;
        $data['employee_rating_response'] = isset($data['employee_rating_response']) ? 1 : 0;
        $data['qr_patrol_enabled'] = isset($data['qr_patrol_enabled']) ? 1 : 0;
        $data['qr_daily_activity_report'] = isset($data['qr_daily_activity_report']) ? 1 : 0;
        $data['qr_recipient_email'] = $data['qr_recipient_email'] ?? null;
        $data['key_management_enabled'] = isset($data['key_management_enabled']) ? 1 : 0;
        $data['key_management_signature'] = isset($data['key_management_signature']) ? 1 : 0;
        $data['key_management_image_id'] = isset($data['key_management_image_id']) ? 1 : 0;
        $data['qr_interval_check'] = isset($data['qr_interval_check']) ? 1 : 0;
        $data['facility_booking'] = isset($data['facility_booking']) ? 1 : 0;
        $data['motion_sensor_enabled'] = isset($data['motion_sensor_enabled']) ? 1 : 0;
        $data['visitor_screening_enabled'] = isset($data['visitor_screening_enabled']) ? 1 : 0;
        $data['time_sheet_approver_id'] = isset($data['time_sheet_approver_id']) ? $data['time_sheet_approver_id'] : null;
        $data['recruiting_match_score_for_sending_mail'] = isset($data['recruiting_match_score_for_sending_mail'])
        ? $data['recruiting_match_score_for_sending_mail']
        : null;

        if (empty($data['id'])) {
            $data['created_by'] = Auth::user()->id;
        } else {
            $data['updated_by'] = Auth::user()->id;
        }
        $data["master_customer"] = number_format($data["master_customer"]);

        //store incident logo to server
        $request = request();
        if ($request->hasFile('incident_report_logo')) {
            if (!empty($data['id'])) {
                $custObj = Customer::find($data['id']);
                if (is_object($custObj)) {
                    //remove existing images if.
                    if (Storage::disk('public')->exists($custObj->incident_report_logo)) {
                        Storage::disk('public')->delete($custObj->incident_report_logo);
                    }
                }
            }
            //store the data
            $path = $request->incident_report_logo->store('incident_logos', 'public');
            $data['incident_report_logo'] = $path;
        }
        $customer = $this->customerModel->updateOrCreate(array('id' => $data['id']), $data);
        if (empty($data['id'])) {
            $customerid = $customer->id;
        } else {
            $customerid = $data['id'];
        }

        $stcDetails['customer_id'] = $customerid;
        $customerObject = $this->customerModel->find($customerid);
        $stcDetails['customer_stc_details_id'] = $customerObject->stcDetails ? $customerObject->stcDetails->id : null;
        $customer_stc_details = $this->stcDetailsRepository->storeStcDetails($stcDetails, $customerid);

        if ($data['fencecount'] > 0) {
            for ($i = 1; $i < $data['fencecount'] + 1; $i++) {
                if (isset($data["fhidtit-" . $i])) {
                    $title = $data["fhidtit-" . $i];
                    $lat = $data["fhidlat-" . $i];
                    $lon = $data["fhidlon-" . $i];
                    $rad = $data["fhidrad-" . $i];
                    $contractualvisit = $data["fhidcontractual-" . $i];
                    geofence::updateOrCreate(['title' => $title, 'customer_id' => $customerid, 'geo_lat' => $lat, 'geo_lon' => $lon, 'geo_rad' => $rad, 'contractual_visit' => $contractualvisit, 'created_by' => Auth::user()->id]);
                }
            }
        }
        CpidCustomerAllocations::where('customer_id', $customerid)->delete();
        //dd($data['row-no']);
        if (isset($data['row-no'])) {
            //dd($data['row-no']);
            foreach ($data['row-no'] as $row_no) {
                $cpid_lookup = intval($data['cpid_' . $row_no]);
                //dd($cpid_lookup);
                if ($cpid_lookup != 0) {
                    $allocation_data = [
                        'cpid' => $cpid_lookup,
                    ];

                    $allocation_data['customer_id'] = $customerid;
                    $allocation_data['created_by'] = Auth::user()->id;
                    CpidCustomerAllocations::updateOrCreate(
                        [
                            'customer_id' => $customerid,
                            'cpid' => $cpid_lookup,
                        ],
                        $allocation_data
                    );
                }
            }
        }

        /* CustomerIncidentSubjectAllocation::where('customer_id', $customerid)->delete();
        if(isset($data['incidentsubject']))
        {
        foreach ($data['incidentsubject'] as $key => $subject_id) {
        CustomerIncidentSubjectAllocation::create(['customer_id' => $customerid, 'subject_id' => $subject_id]);
        }
        }*/

        return $customer;
    }

    public function incidentSubjectMapping()
    {
        # code...
    }

    /**
     * Update Customer Latitude and Longitude
     * @param $data
     * @return object
     */
    public function updateCustomerLatLong($data)
    {
        $customer_update = $this->customerModel->where('id', $data['id'])->update(['geo_location_lat' => $data['lat'], 'geo_location_long' => $data['long'], 'radius' => $data['radius']]);
        return $customer_update;
    }

    /**
     * Remove the specified customer from storage.
     *
     * @param  $id
     * @return object
     */
    public function destroyCustomer($id)
    {
        $customer_delete = $this->customerModel->destroy($id);
        $allocated_customer_id = $this->customerEmployeeAllocationModel->pluck('customer_id')->toArray();
        if (in_array($id, $allocated_customer_id)) {
            $customer_allocation_update = $this->customerEmployeeAllocationModel->where('customer_id', $id)->update(['to' => date('Y-m-d'), 'updated_by' => \Auth::user()->id]);
            $customer_allocation_delete = $this->customerEmployeeAllocationModel->where('customer_id', $id)->delete();
        }

        return $customer_delete;
    }

    /**
     * Get all Candidates and Customers in Map
     *
     * @param type Request $request, $status
     * @return object
     */
  //v8 changes- optional parameter after required param  
 /*   public function getAllCandidatesMap($status = 'Applied', $request)
    {
        $id = $request->get('candidate_id_array');
        $candidate_id = explode(',', $id);
        $query = $this->candidateModel->with(['availability', 'jobs', 'guardingexperience', 'wageexpectation', 'experience'])
            ->whereIn('id', $candidate_id);
        $candidates = $query->get();
        return $candidates;
    } */
    public function getAllCandidatesMap( $request, $status = 'Applied')
    {
        $id = $request->get('candidate_id_array');
        $candidate_id = explode(',', $id);
        $query = $this->candidateModel->with(['availability', 'jobs', 'guardingexperience', 'wageexpectation', 'experience'])
            ->whereIn('id', $candidate_id);
        $candidates = $query->get();
        return $candidates;
    }

    /**
     * Get all Employees and Customers in Map
     *
     * @param type Request $request, $status
     * @return object
     */
    //v8 changes- optional parameter after required param  
  /*  public function getAllEmployeesMap($status = 'Applied', $request)
    {
        $id = $request->get('employee_id_array');
        $employee_id = explode(',', $id);
        $query = Employee::with(['employee_availability', 'user.userCertificate.certificateMaster', 'user.securityClearanceUser.securityClearanceLookups', 'user' => function ($query) {
            $query->select('id', 'email', 'first_name', 'last_name', \DB::raw("CONCAT(first_name,' ',COALESCE(last_name,'')) as name"));
        }, 'user.candidate_transition.candidate.latestJobApplied', 'user.multipleFillShift', 'user.eventlog_score', 'user.employee_shift_payperiods.availableShift'])->whereHas('user.roles.permissions', function ($q) {
            $q->whereNotIn('name', ['admin', 'super_admin']);
        })->whereIn('user_id', $employee_id);
        $employees = $query->get();
        return $employees;
    } */
    public function getAllEmployeesMap($request,$status = 'Applied')
    {
        $id = $request->get('employee_id_array');
        $employee_id = explode(',', $id);
        $query = Employee::with(['employee_availability', 'user.userCertificate.certificateMaster', 'user.securityClearanceUser.securityClearanceLookups', 'user' => function ($query) {
            $query->select('id', 'email', 'first_name', 'last_name', \DB::raw("CONCAT(first_name,' ',COALESCE(last_name,'')) as name"));
        }, 'user.candidate_transition.candidate.latestJobApplied', 'user.multipleFillShift', 'user.eventlog_score', 'user.employee_shift_payperiods.availableShift'])->whereHas('user.roles.permissions', function ($q) {
            $q->whereNotIn('name', ['admin', 'super_admin']);
        })->whereIn('user_id', $employee_id);
        $employees = $query->get();
        return $employees;
    }

    /**
     * Get Customer details for Supervisor panel map
     * @param type $customer_id
     * @param type $current_template_id
     * @return type
     */
    public function getCustomerMap($customer_id = null, $current_template_id = null, $no_user_details = false)
    {
        $customer_map = $this->customerModel;
        if (!$no_user_details) {
            $customer_map = $customer_map->with('employeeLatestCustomerSupervisor', 'employeeLatestCustomerAreaManager', 'ratingDetails.user.trashedEmployee');
        }
        if (isset($current_template_id)) {
            $customer_map->with([
                'customerPayperiodTemplate',
                'customerPayperiodTemplate.customerReport' => function ($query) {
                    $query->whereHas('parentTemplateFormWithTrashed');
                },
                'customerPayperiodTemplate.customerReport.templateFormWithTrashed.questionCategory',
            ]);
        }
        return $customer_map->whereIn('id', $customer_id)->orderBy('client_name')->get();
    }

    /**
     * Get user details of a customer
     * @param type $customer_arr
     * @return type
     */
    public function getManagerDetailsArr($each_customer, $role)
    {
        //todo::modify the function.
        $customer_arr = $each_customer->toArray();
        $full_name = "";
        if ($role == "supervisor" && !isset($customer_arr['employee_latest_customer_supervisor'])) {
            return array();
        } elseif ($role == "area_manager" && !isset($customer_arr['employee_latest_customer_area_manager'])) {
            return array();
        }
        if ($role == "area_manager") {
            $relation = "employee_latest_customer_area_manager";
            $full_name = $each_customer->employeeLatestCustomerAreaManager->areamanager->full_name;
        } elseif ($role == "supervisor") {
            $relation = "employee_latest_customer_supervisor";
            $full_name = $each_customer->employeeLatestCustomerSupervisor->supervisor->full_name;
        }
        $emp_arr = array_merge($customer_arr[$relation][$role], Arr::except($customer_arr[$relation][$role]["employee"], ['id', 'user_id', 'created_at', 'updated_at', 'deleted_at']));
        $emp_arr = Arr::except($emp_arr, ['employee']);
        $emp_arr['full_name'] = $full_name;
        return $emp_arr;
    }

    /**
     * Store Customer Stc details
     *
     * @param type Request $request
     * @return object
     */
    public function storeCustomerStc($request)
    {
        $stc_data = $this->customerModel->updateOrCreate(array('id' => $request->get('id'), 'stc' => STC_CUSTOMER), $request->all());
        $locationCoordinates = $this->getLocationDetailsByPostCode($request->get('postal_code'));
        $stc_data->geo_location_lat = ($locationCoordinates['latitude'] != null) ? $locationCoordinates['latitude'] : null;
        $stc_data->geo_location_long = ($locationCoordinates['longitude'] != null) ? $locationCoordinates['longitude'] : null;
        $stc_data->save();
        return $stc_data;
    }

    /**
     * fetch latitude, longitude by post code
     *
     * @param string $postCode
     * @return array
     */
    public function getLocationDetailsByPostCode($postCode)
    {
        $result = ['latitude' => null, 'longitude' => null];
        $location_data = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=" . $postCode . "&sensor=false&key=" . config('globals.google_api_curl_key'));
        $location_data = json_decode($location_data);

        if (isset($location_data->{'results'}[0])) {
            $result['latitude'] = $location_data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $result['longitude'] = $location_data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        }
        return $result;
    }

    /**
     *Get Customer Stc details
     *
     * @param type Request $request
     * @return object
     */
    public function getCustomerStc($client_id=null)
    {
        $stc = $this->customerModel->select(['id', 'project_number', 'client_name', 'requester_name', 'contact_person_name', 'contact_person_email_id', 'contact_person_phone'])->where('stc', STC_CUSTOMER)->with('stcDetails', 'requesterDetails')->orderby('id', 'DESC')->get();
        $stc_details = $stc->when($client_id!=null, function ($q) use ($client_id) {
            return $q->where('id', $client_id);
        });
        $res = $this->getFormattedCustomerStc($stc_details);
        return $res;
    }

    /**
     *Get Formatted Customer Stc details
     *
     * @param type Request $request
     * @return object
     */
    public function getFormattedCustomerStc($stc_details)
    {
        $datatable_rows = array();
        foreach ($stc_details as $key => $each_stc) {
            $each_row["id"] = $each_stc->id;
            $each_row["project_number"] = $each_stc->project_number;
            if (is_numeric($each_stc->requester_name)) {
                if ($each_stc->requesterDetails == null) {
                    $each_row["requester_name"] = '--';
                } else {
                    $each_row["requester_name"] = $each_stc->requesterDetails->full_name;
                }
            } else {
                $each_row["requester_name"] = $each_stc->requester_name;
            }
            $each_row["client_name"] = $each_stc->client_name;
            $each_row["contact_person_name"] = $each_stc->contact_person_name;
            $each_row["contact_person_email_id"] = $each_stc->contact_person_email_id;
            $each_row["contact_person_phone"] = $each_stc->contact_person_phone;

            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     * Customer Excel Import
     * @param  $request
     * @return response
     */
    public function customerExcelImport($request)
    {
        ini_set('max_execution_time', 3000);
        $import_success_row_no = [];
        $row_no = [];
        if ($request->hasFile('import_file') && in_array($request->file('import_file')->getClientOriginalExtension(), ['xls', 'xlsx'])) {
            try {
                \DB::beginTransaction();
                $import_success_count = 0;
                $path = $request->file('import_file')->getRealPath();
                $excel = IOFactory::load($path);
                $sheet = $excel->getSheetByName('Customer Information');
                if ($sheet != null) {
                    $row = $sheet->getHighestRow();
                    $row_count = $row - 1;
                    if (!empty($sheet)) {
                        $regions_values = $this->regionLookupModel->pluck('region_name')->toArray();
                        $regions = array_map('strtolower', $regions_values);
                        $industry_sectors_values = $this->industrySectorLookupModel->pluck('industry_sector_name')->toArray();
                        $industry_sectors = array_map('strtolower', $industry_sectors_values);
                        for ($i = 2; $i <= $row; $i++) {
                            $project_numbers = $this->customerModel->pluck('project_number')->toArray();
                            $boolean = array('Yes', 'yes', 'No', 'no');
                            $boolean_null = array('Yes', 'yes', 'No', 'no', '', ' ', null, null);

                            $project_number = $sheet->getCell('A' . $i)->getValue();
                            $client_name = $sheet->getCell('B' . $i)->getValue();
                            $interval_check_in = $sheet->getCell('AC' . $i)->getValue();
                            $duration = $sheet->getCell('AD' . $i)->getValue();
                            $duration_value = true;
                            if ($interval_check_in == 'Yes' || $interval_check_in == 'yes') {
                                $duration_value = isset($duration) ? true : false;
                            }

                            $industry_sector = strtolower($sheet->getCell('Z' . $i)->getValue());
                            $region = strtolower($sheet->getCell('AA' . $i)->getValue());

                            $postal_code = $sheet->getCell('O' . $i)->getValue();
                            $contact_person_phone = $sheet->getCell('E' . $i)->getValue();
                            $contact_person_cell_phone = $sheet->getCell('G' . $i)->getValue();

                            $contact_person_phone_ext = $sheet->getCell('F' . $i)->getValue();
                            $contact_person_phone_ext_validation = true;
                            if ($contact_person_phone_ext != null) {
                                $contact_person_phone_ext_validation = preg_match('/^\d[0-9]{1,10}$/', $contact_person_phone_ext);
                            }

                            $requester_empno = $sheet->getCell('K' . $i)->getValue();
                            $requester_empno_validation = true;
                            if ($requester_empno != null) {
                                $requester_empno_validation = preg_match('/^\d{6}$/', $requester_empno);
                            }

                            $duty_officer_emp_no = $sheet->getCell('Y' . $i)->getValue();
                            $duty_officer_emp_no_validation = true;
                            if ($duty_officer_emp_no != null) {
                                $duty_officer_emp_no_validation = preg_match('/^\d{6}$/', $duty_officer_emp_no);
                            }

                            if (!empty($project_number) && preg_match('/^\d{7}$/', $project_number) && $duty_officer_emp_no_validation && $requester_empno_validation && $contact_person_phone_ext_validation && !empty($sheet->getCell('L' . $i)->getValue()) && !empty($sheet->getCell('M' . $i)->getValue()) && !empty($sheet->getCell('N' . $i)->getValue()) && !empty($sheet->getCell('O' . $i)->getValue()) && !empty($sheet->getCell('Z' . $i)->getValue()) && !empty($sheet->getCell('AA' . $i)->getValue()) && !empty($sheet->getCell('I' . $i)->getValue()) && $duration_value && in_array($industry_sector, $industry_sectors) && in_array($region, $regions)) {
                                if (!in_array($sheet->getCell('A' . $i)->getValue(), $project_numbers) && in_array($sheet->getCell('AC' . $i)->getValue(), $boolean_null) && in_array($sheet->getCell('AE' . $i)->getValue(), $boolean) && in_array($sheet->getCell('AF' . $i)->getValue(), $boolean)) {
                                    array_push($import_success_row_no, $i);

                                    $guard_tour_enabled = $sheet->getCell('AB' . $i)->getValue();
                                    if ($guard_tour_enabled == 'Yes' || $guard_tour_enabled == 'yes') {
                                        $shift_journal = 1;
                                    } else {
                                        $shift_journal = 0;
                                    }

                                    $stc_customer = $sheet->getCell('AE' . $i)->getValue();
                                    if ($stc_customer == 'Yes' || $stc_customer == 'yes') {
                                        $stc = 1;
                                    } else {
                                        $stc = 0;
                                    }

                                    $active_customer = $sheet->getCell('AF' . $i)->getValue();
                                    if ($active_customer == 'Yes' || $active_customer == 'yes') {
                                        $active = 1;
                                    } else {
                                        $active = 0;
                                    }

                                    $project_open_date = $sheet->getCell('T' . $i)->getValue();
                                    $proj_open = $project_open_date != null ? Date::excelToDateTimeObject($project_open_date) : null;

                                    $industry_sector_id = $this->getIndustrySectorLookupId($industry_sector);
                                    $region_id = $this->getRegionLookupId($region);

                                    $duty_officer_id = $this->employeeModel->where('employee_no', $duty_officer_emp_no)->value('user_id');

                                    $customer = [
                                        'project_number' => $project_number,
                                        'client_name' => $client_name,
                                        'contact_person_name' => $sheet->getCell('C' . $i)->getValue(),
                                        'contact_person_email_id' => $sheet->getCell('D' . $i)->getValue(),
                                        'contact_person_phone' => $contact_person_phone,
                                        'contact_person_phone_ext' => $contact_person_phone_ext,
                                        'contact_person_cell_phone' => $contact_person_cell_phone,
                                        'contact_person_position' => $sheet->getCell('H' . $i)->getValue(),
                                        'requester_name' => $sheet->getCell('I' . $i)->getValue(),
                                        'requester_position' => $sheet->getCell('J' . $i)->getValue(),
                                        'requester_empno' => $requester_empno,
                                        'address' => $sheet->getCell('L' . $i)->getValue(),
                                        'city' => $sheet->getCell('M' . $i)->getValue(),
                                        'province' => $sheet->getCell('N' . $i)->getValue(),
                                        'postal_code' => $postal_code,
                                        'geo_location_lat' => $sheet->getCell('P' . $i)->getValue(),
                                        'geo_location_long' => $sheet->getCell('Q' . $i)->getValue(),
                                        'radius' => $sheet->getCell('R' . $i)->getValue(),
                                        'description' => $sheet->getCell('S' . $i)->getValue(),
                                        'proj_open' => $proj_open != null ? $proj_open->format('Y-m-d') : null,
                                        'arpurchase_order_no' => $sheet->getCell('U' . $i)->getValue(),
                                        'arcust_type' => $sheet->getCell('V' . $i)->getValue(),
                                        'inquiry_date' => $sheet->getCell('W' . $i)->getValue(),
                                        'time_stamp' => $sheet->getCell('X' . $i)->getValue(),
                                        'duty_officer_id' => $duty_officer_id,
                                        'industry_sector_lookup_id' => $industry_sector_id,
                                        'region_lookup_id' => $region_id,
                                        'guard_tour_enabled' => $shift_journal,
                                        'guard_tour_duration' => $duration,
                                        'created_by' => Auth::user()->id,
                                        'updated_by' => Auth::user()->id,
                                        'stc' => $stc,
                                        'active' => $active,
                                    ];
                                    $customer = $this->customerModel->create($customer);
                                    $import_success_count++;
                                }
                            }
                            array_push($row_no, $i);
                        }

                        $import_failed_row_no = array_diff($row_no, $import_success_row_no);
                        $rows = implode(", ", $import_failed_row_no);
                        if ($import_success_count > 0) {
                            \DB::commit();
                            $import_result = $import_success_count . ' customer information(s) successfully imported out of ' . $row_count;
                            if ($rows != null) {
                                $import_result = $import_result . '. Customer information(s) of row number(s) ' . $rows . ' was not imported';
                            }
                            return $import_result;
                        } else {
                            return 'Customer information(s) of row number(s) ' . $rows . ' was not imported';
                        }
                    }
                    return 'Please import an excel file with customer informations';
                }
                return 'Please import an excel file with "Customer Information" sheet';
            } catch (\Exception $e) {
                \DB::rollBack();
                return 'Customer information(s) import was unsuccessful';
            }
        } else {
            return 'Please import an excel file of format xlsx and xls';
        }
    }

    /**
     * Get region lookup id
     * @param  $region
     * @return id
     */
    public function getRegionLookupId($region)
    {
        return $this->regionLookupModel->where('region_name', $region)->value('id');
    }

    /**
     * Get industry sector lookup id
     * @param  $industrySector
     * @return id
     */
    public function getIndustrySectorLookupId($industrySector)
    {
        return $this->industrySectorLookupModel->where('industry_sector_name', $industrySector)->value('id');
    }

    /**
     * FOR APP-Get Customerlist
     *
     * @param  \App\Models\Customer  $customer
     * @return resultset
     */
    public function getAllActiveCustomers($request)
    {
        $startItem = (int) $request->get('startItem');
        $numberOfRecord = (int) $request->get('numberOfRecord');
        $lastSyncDateTime = $request->get('lastSyncDateTime');
        $optimizationVersion = $request->get('optimizationVersion');
        $appSyncOptimizationVersion = 1;
        $queryResult = $this->customerModel->whereActive(true);
        if ($optimizationVersion === $appSyncOptimizationVersion) {
            $queryResult = $queryResult->select('id', 'project_number', 'client_name', 'geo_location_lat', 'geo_location_long', 'radius');
        } else {
            $queryResult = $queryResult->with('employeeCustomerSupervisor', 'employeeCustomerAreaManager');
        }
        $queryResult = $queryResult->where(function ($query) use ($lastSyncDateTime) {
            if (isset($lastSyncDateTime)) {
                $query->whereDate('updated_at', '>', $lastSyncDateTime);
            }
        });

        if ($numberOfRecord != 0 && $startItem != 0) {
            $queryResult->limit($numberOfRecord)->offset(($startItem - 1));
        }
        return $queryResult->get();
    }

    /**
     * Function to get details of a single user in
     * @param type $user_id
     */
    public function getFormattedProjectDetails($customer_id)
    {
        $project_details = $this->getCustomerWithMangers($customer_id);
        $formatted_project_details['area_manager_id'] = isset($project_details['areamanager']['id']) ? $project_details['areamanager']['id'] : null;
        $area_manager_first_name = isset($project_details['areamanager']['first_name']) ? $project_details['areamanager']['first_name'] : '--';
        $area_manager_last_name = isset($project_details['areamanager']['last_name']) ? $project_details['areamanager']['last_name'] : '';
        $formatted_project_details['area_manager_full_name'] = $area_manager_first_name . ' ' . $area_manager_last_name;
        $formatted_project_details['area_manager_email'] = isset($project_details['areamanager']['email']) ? $project_details['areamanager']['email'] : '--';
        $area_manager_phone = isset($project_details['areamanager']['phone']) ? $project_details['areamanager']['phone'] : '--';
        $area_manager_phone_ext = isset($project_details['areamanager']['phone_ext']) ? (' x' . $project_details['areamanager']['phone_ext']) : '';
        $formatted_project_details['area_manager_phone'] = $area_manager_phone . $area_manager_phone_ext;

        $formatted_project_details['supervisor_id'] = isset($project_details['supervisor']['id']) ? $project_details['supervisor']['id'] : null;
        $supervisor_first_name = isset($project_details['supervisor']['first_name']) ? $project_details['supervisor']['first_name'] : '--';
        $supervisor_last_name = isset($project_details['supervisor']['last_name']) ? $project_details['supervisor']['last_name'] : '';
        $formatted_project_details['supervisor_full_name'] = $supervisor_first_name . ' ' . $supervisor_last_name;
        $formatted_project_details['supervisor_email'] = isset($project_details['supervisor']['email']) ? $project_details['supervisor']['email'] : '--';
        $supervisor_phone = isset($project_details['supervisor']['phone']) ? $project_details['supervisor']['phone'] : '--';
        $supervisor_phone_ext = isset($project_details['supervisor']['phone_ext']) ? (' x' . $project_details['supervisor']['phone_ext']) : '';
        $formatted_project_details['supervisor_phone'] = $supervisor_phone . $supervisor_phone_ext;
        return $formatted_project_details;
    }

    /**
     * Function to get All Customers list
     * @return array
     */
    public function getAllCustomers()
    {
        $permanent_customers_list = array_keys($this->getList(PERMANENT_CUSTOMER));
        $stc_customers_list = array_keys($this->getList(STC_CUSTOMER));
        $all_permanent_stc_customers = array_merge($permanent_customers_list, $stc_customers_list);
        return $all_permanent_stc_customers;
    }

    /**
     * Function to get All Customers list allocated to the logged in user
     * @return array
     */
    public function getOnlyAllocatedCustomers()
    {
        $allocated_user = $this->employeeAllocationRepository->getEmployeeAssigned([\Auth::user()->id]);
        $all_allocated_permanent_stc_customers_list = $this->getAllAllocatedCustomerId(array_merge([\Auth::user()->id], $allocated_user->pluck('user_id')->toArray()), true);
        return $all_allocated_permanent_stc_customers_list;
    }

    /**
     * Get Permanent & STC customers allocated
     * @param type $arr_user
     * @return type
     */
    public function getAllAllocatedCustomerId($arr_user)
    {
        return data_get($this->customerEmployeeAllocationModel->with(['customer'])->whereIn('user_id', $arr_user)->get()->toArray(), "*.customer.id");
    }

    /**
     * Function to get ProjectsDropdownList
     * @param  $allocation_type [all or allocated]
     * @return array [Project dropdown with name and number]
     */
    public function getProjectsDropdownList($allocation_type = all)
    {
        if ($allocation_type == 'allocated') {
            $customers_list = $this->getAllAllocatedCustomerId([Auth::user()->id]);
        } else {
            $customers_list = $this->getAllCustomers();
        }
        $customer_details_arr = $this->getCustomers(array_unique($customers_list))->sortBy('client_name');
        $customers_arr = array();
        foreach ($customer_details_arr as $key => $customers) {
            $customers_arr[$customers->id] = $customers->client_name . ' (' . $customers->project_number . ')';
        }
        return $customers_arr;
    }

    /**
     * Get  customers who are allowed to show in site status dashboard
     * @param type $arr_customer
     * @return type
     */
    public function getAllShowSiteStatusCustomerId($arr_customer)
    {
        return $this->customerModel->whereIn('id', $arr_customer)->where('show_in_sitedashboard', 1)->pluck('id')->toArray();
    }

    /**
     * Function to get fence interval from customer id.
     * @param int $customer_id
     * @return int
     */
    public function getFenceInterval(int $customer_id): int
    {
        return ($this->customerModel->select('fence_interval')->where('id', $customer_id)->pluck('fence_interval')[0]) ?? 0;
    }

    /**
     * Get show_in_sitedashboard enabled customer list
     * @return array
     */
    public function getAllShowSiteDashboardEnabled()
    {
        return $this->customerModel->select('id', 'client_name', 'project_number')
            ->where('show_in_sitedashboard', 1)
            ->get();
    }

}
