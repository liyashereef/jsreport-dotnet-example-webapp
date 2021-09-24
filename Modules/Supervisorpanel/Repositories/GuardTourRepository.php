<?php

namespace Modules\Supervisorpanel\Repositories;

use Auth;
use Carbon\Carbon;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Timetracker\Models\GuardTour;
use Modules\Timetracker\Repositories\EmailRepository;
use Modules\Timetracker\Repositories\ImageRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Models\Customer;
use Modules\Admin\Repositories\EmployeeAllocationRepository;

class GuardTourRepository {

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $guardTourModel;
    protected $imageRepository;
    protected $emailRepository, $customerRepository, $employee_allocation_repository;

    /**
     * Create a new Repository instance.
     *
     * @param  \App\Models\Customer $customerModel
     * @param  \App\Models\IndustrySectorLookup $industrySectorLookupModel
     * @param  \App\Models\RegionLookup $regionLookupModel
     * @param  \App\Models\RegionLookupRepository $regionLookupRepository
     * @param  \App\Models\IndustrySectorLookupRepository $industrySectorLookupRepository
     */
    public function __construct(CustomerRepository $customerRepository, GuardTour $guardTourModel, ImageRepository $imageRepository, EmailRepository $emailRepository, EmployeeAllocationRepository $employee_allocation_repository) {

        $this->directory_seperator = "/";
        $this->extension_seperator = ".";
        $this->guardTourModel = $guardTourModel;
        $this->imageRepository = $imageRepository;
        $this->emailRepository = $emailRepository;
        $this->customerRepository = $customerRepository;
        $this->employee_allocation_repository = $employee_allocation_repository;
    }

    /**
     * Get the path including file name to guard tool attachment
     * @param $shift_id
     * @return string
     */
    public function guardTourAttachment($shift_id, $image_id) {
        $path = array();
        $guard_tour = $this->guardTourModel->select('image', 'id')->where('shift_id', $shift_id)->where('id', $image_id)->first();
        $guard_tour_id = $guard_tour->id;
        if (!empty($guard_tour->image)) {
            $path['path'] = storage_path('app') . $this->directory_seperator . config('globals.guardtour_images_folder') . $this->directory_seperator . $shift_id . $this->directory_seperator . $guard_tour->image;
            $file_name_arr = explode(".", $guard_tour->image);
            if (isset($file_name_arr) && count($file_name_arr) >= 2) {
                $path['file'] = "Guard_Tour_" . $this->extension_seperator . $file_name_arr[(count($file_name_arr) - 1)];
            }
            //$path = storage_path($file_location);
        }
        return $path;
    }

    /**
     * Get the List of guard tour
     * @param $customer_id
     * @return string
     */
    public function getList($customer_id) {
        $customer_ids = array();
        $role_name = auth()->user()->roles->first()->name;
        $id_array = [];
        if (!\Auth::user()->can('view_all_guard_tour')) {
            $id_array[] = \Auth::user()->id;

            if (\Auth::user()->can('view_guard_tour')) {
                $customer_ids = $this->customerRepository->getAllAllocatedCustomerId([\Auth::user()->id]);
                $id_array = $this->employee_allocation_repository->getEmployeeIdAssigned(\Auth::user()->id)->toArray();
                array_push($id_array, \Auth::user()->id);
            }
        } else {
            $customer_ids = Customer::all()->pluck('id')->toArray();
        }

        if ($customer_id != 0) {
            $id_array = [];
            $tempArray = [];
            if (is_array($customer_id)) {
                foreach ($customer_id as $id) {
                    if (in_array($id, $customer_ids)) {
                        $tempArray[] = $id;
                    }
                }
            } elseif (in_array($customer_id, $customer_ids)) {
                $tempArray[] = $customer_id;
            }
            $customer_ids = $tempArray;
        }

        $supervisor_id = auth()->user()->id;
//        $guards = EmployeeAllocation::where('supervisor_id', $supervisor_id)->pluck('user_id')->toArray();
        $employee_shift_payperiods = EmployeeShiftPayperiod::with('shifts')
                ->whereIn('customer_id', $customer_ids)
                ->orWhereIn('employee_id', $id_array)
                ->get();
        $shift_ids = array();
        $datavalues = array();
        $index = 1;
        foreach ($employee_shift_payperiods as $employee_shifts) {
            foreach ($employee_shifts->shifts as $shift) {
                $shift_ids[] = $shift->id;
            }
        }
//        $condiitonquery = "->where('submitted_date','>=',Carbon::now()->subDays(7)->format('Y-m-d'))";
        $guard_tour_data = GuardTour::with('shift', 'shift.shift_payperiod.trashed_user.trashedEmployee')->whereIn('shift_id', $shift_ids)->select([
                    'id', 'shift_id', 'submitted_date', 'submitted_time', 'notes', 'image', \DB::raw('DATE_FORMAT(created_at,\'%Y-%m-%d\') as date, DATE_FORMAT(created_at,\'%l:%i %p\') as time , created_at'),
                ])->get();

        foreach ($guard_tour_data as $each_data) {
            $clientid = $each_data->shift->shift_payperiod->customer_id;
            // $each_row['ID'] = $each_data->id;
            $each_row['Created Date'] = ($each_data->submitted_date ) ? $each_data->submitted_date : '--'; //hidden value for shift module
            $each_row['Date'] = ($each_data->submitted_date != null) ? $each_data->submitted_date : $this->calcDateFromAcutalShiftTime($each_data->submitted_time, $each_data->time, $each_data->date);
            // $each_row['shift_id'] = $each_data->shift_id;
            // $each_row['Submitted Time'] = $each_data->submitted_time;
            $each_row['Time'] = ($each_data->submitted_time != null) ? Carbon::parse($each_data->submitted_time)->format('h:i A') : $each_data->time;
            $each_row['Guard'] = $each_data->shift->shift_payperiod->trashed_user->full_name;
            $each_row['Employee ID'] = $each_data->shift->shift_payperiod->trashed_user->trashedEmployee->employee_no;
            $each_row['Location'] = $each_data->notes;
            $each_row['Client'] = $each_data->shift->shift_payperiod->customer != null ? $each_data->shift->shift_payperiod->customer->client_name : '';
            if (!empty($each_data->image)) {
                $attachment = route('guardTour.attachement', ['shift_journal_id' => $each_data->shift_id, 'image_id' => $each_data->id]);
                $each_row['Image'] = "<a title='Guard Tour Image' class='fa fa-lg fa-list-alt cgl-font-blue' target='_blank' href='" . $attachment . "'> </a>";
            } else {
                $each_row['Image'] = '';
            }
            if (in_array($role_name, ['admin', 'super_admin'])) {
                array_push($datavalues, $each_row);
            } else {
//                $userid = $each_data->shift->shift_payperiod->trashed_user->id;
                if (in_array($clientid, $customer_ids)) {
                    array_push($datavalues, $each_row);
                }
            }
            $index += 1;
        }
        if (count($guard_tour_data) == 0 || count($datavalues) == 0) {
            $each_row['Date'] = null;
            $each_row['Time'] = null;
            $each_row['Guard'] = null;
            $each_row['Employee ID'] = null;
            $each_row['Location'] = null;
            $each_row['Image'] = null;
            array_push($datavalues, $each_row);
        }

        return $datavalues;
    }

    /**
     * To save guard tours against a shift
     *
     * @param [type] $shiftJornals
     * @param [type] $employee_shift_id
     * @return void
     */
    public function saveGuardTour($guardTours, $employee_shift) {
        foreach ($guardTours as $key => $guard_tour) {
            $guardTour['shift_id'] = $employee_shift->id;
            $guardTour['submitted_time'] = $guard_tour->time;
            $guardTour['submitted_date'] = $guard_tour->date ?? null;
            $guardTour['notes'] = $guard_tour->notes;
            $guardTour['image'] = isset($guard_tour->image) ? $this->imageRepository->saveImage($guard_tour->image, $employee_shift->id) : null;
            $this->guardTourModel->create($guardTour);
        }
        /**
         * If shiftjornal enabled and interval is set
         */
        $guard_tour_counts = $this->getGuardTourCount($employee_shift);
        if ($guard_tour_counts['expected'] > $guard_tour_counts['actual']) {
            $this->sendNotification($employee_shift, $guard_tour_counts);
        }
        //info('Customer ID:' . $employee_shift->shift_payperiod->trashed_customer->id . 'Shift Journal enabled:' . (int) $employee_shift->shift_payperiod->trashed_customer->guard_tour_enabled . ' Shift Journal duration:' . $employee_shift->shift_payperiod->trashed_customer->guard_tour_duration . ' Shift ID:' . $employee_shift->id . ' Expected:' . $shift_journal_counts['expected'] . ' Actual:' . $shift_journal_counts['actual']);
    }

    /**
     * To get the needed vs actual shiftjornalentries of last shift
     *
     * @param [type] $customer_id
     * @return void
     */
    public function getGuardTourCount($employee_shift) {
        $result['expected'] = $result['actual'] = 0;
        if ($employee_shift->shift_payperiod->trashed_customer->guard_tour_enabled && (int) $employee_shift->shift_payperiod->trashed_customer->guard_tour_duration > 0) {
            $employee_shift->load('guardTours');
            $guard_duration = $employee_shift->shift_payperiod->trashed_customer->guard_tour_duration;
            $guard_tour_duration = $guard_duration > 0 ? $guard_duration : 1;
            $start_date = isset($employee_shift->start) ? $employee_shift->start : "";
            $end_date = isset($employee_shift->given_end_time) ? $employee_shift->given_end_time : "";
            $time_diff = (floor((strtotime($end_date) - strtotime($start_date)) / 3600));
            $result['expected'] = intval($time_diff) / $guard_tour_duration;
            $result['actual'] = $employee_shift->guardTours->count();
        }
        return $result;
    }

    /**
     * Send notification mails to reporting officers(area manager)
     *
     * @param [type] $employee_shift
     * @return void
     */
    public function sendNotification($employee_shift, $guard_tour_counts = null) {
        $this->emailRepository->emailAreaManager($employee_shift, $guard_tour_counts);
    }

    /**
     * Get Lastest updated customer shift details
     *
     * @param [type] $shiftPayperiods
     * @return $recent_shift
     */
    public function getLatestShift($shiftPayperiods) {
        foreach ($shiftPayperiods as $key => $shiftPayperiod) {
            foreach ($shiftPayperiod->shifts->sortByDesc('created_at') as $key => $shift_entries) {
                $shift_max[] = $shift_entries;
                break;
            }
        }
        usort($shift_max, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });
        foreach ($shift_max as $key => $latest_shift) {
            $recent_shift = $latest_shift;
            break;
        }
        return $recent_shift;
    }

    /**
     * Where there is no date info, this function can be used
     * to approximate the date from shift submit time and
     * provided time from app.
     * Will not work if time difference is more than 24 hours.
     */
    public function calcDateFromAcutalShiftTime($given_time, $shift_submit_time, $shift_submit_date) {
        $journal_time = Carbon::parse($given_time);
        $shift_submit_time = Carbon::parse($shift_submit_time);
        $journal_calc_date = $shift_submit_date;
        if ($journal_time->diffInSeconds($shift_submit_time, false) < 0) {
            $journal_calc_date = Carbon::parse($journal_calc_date)->subDays(1)->toDateString();
        }
        return $journal_calc_date;
    }

}
