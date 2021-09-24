<?php

namespace Modules\Supervisorpanel\Repositories;

use Carbon\Carbon;
use DateTime;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\ShiftModuleEntry;
use Modules\Admin\Models\ShiftModuleField;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\ShiftModuleRepository;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\ShiftJournal;

class ShiftJournalRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $shiftJournalModel;
    protected $employee_allocation_repository;
    protected $employeeShift;
    protected $shiftModuleEntry;
    protected $shiftModuleRepository, $customerRepository;

    /**
     * Create a new Repository instance.
     *
     * @param  \App\Models\ShiftJournal $shiftJournalModel
     */
    public function __construct(CustomerRepository $customerRepository, ShiftJournal $shiftJournalModel, EmployeeAllocationRepository $employee_allocation_repository, EmployeeShift $employeeShift, ShiftModuleEntry $shiftModuleEntryModel, ShiftModuleRepository $shiftModuleRepository)
    {
        $this->shiftJournalModel = $shiftJournalModel;
        $this->employee_allocation_repository = $employee_allocation_repository;
        $this->employeeShift = $employeeShift;
        $this->shiftModuleEntryModel = $shiftModuleEntryModel;
        $this->directory_seperator = "/";
        $this->shiftModuleRepository = $shiftModuleRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get the List of Shift Journal
     * @param $customer_id
     * @return string
     */
    public function getList($customer_id, $dashboard_view = false)
    {
        $datavalues = array();
        $index = 1;
        $customer_ids = array();
        $role_name = auth()->user()->roles->first()->name;
        $user = \Auth::user();
        $query = ShiftJournal::with('shift', 'shift.shift_payperiod.trashed_user.trashedEmployee', 'createdUser');
        $id_array = [];
        if (!\Auth::user()->can('view_all_shift_journal')) {
            if (\Auth::user()->can('view_allocated_shift_journal')) {
                $customer_ids = $this->customerRepository->getAllAllocatedCustomerId([\Auth::user()->id]);
                $id_array = $this->employee_allocation_repository->getEmployeeIdAssigned(\Auth::user()->id)->toArray();
                array_push($id_array, \Auth::user()->id);
            } elseif (\Auth::user()->can('view_shift_journal')) {
                $id_array[] = \Auth::user()->id;
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

        $query->whereIn('customer_id', $customer_ids)
            ->orWhereIn('created_by', $id_array);
        $shift_journal_data = $query->select([
            'id', 'shift_id', 'submitted_date', 'notes', 'image', 'customer_id', 'created_by', \DB::raw('DATE_FORMAT(created_at,\'%Y-%m-%d\') as date, DATE_FORMAT(submitted_time,\'%l:%i %p\') as submitted_time ,DATE_FORMAT(created_at,\'%l:%i %p\') as time'),
        ])->get();
        if (count($shift_journal_data) == 0) {
            $each_row['Date'] = null;
            $each_row['Time'] = null;
            $each_row['Guard'] = null;
            $each_row['Employee ID'] = null;
            $each_row['Location'] = null;
            $each_row['Image'] = null;
            array_push($datavalues, $each_row);
        }

        foreach ($shift_journal_data as $each_data) {
            // $each_row['id'] = $each_data->id;
            // $each_row['shift_id'] = $each_data->shift_id;
            //   if (!empty($each_data->shift) && !empty($each_data->shift->shift_payperiod)) {
            $clientid = $each_data->customer_id;
            $each_row['Created Date'] = ($each_data->submitted_date) ? $each_data->submitted_date : '--'; //hidden value for shift module
            $each_row['Date'] = ($each_data->submitted_date != null) ? $each_data->submitted_date : $this->calcDateFromAcutalShiftTime($each_data->submitted_time, $each_data->time, $each_data->date);
            $each_row['Time'] = ($each_data->submitted_time != null) ? Carbon::parse($each_data->submitted_time)->format('h:i A') : $each_data->time;
            $each_row['Guard'] = $each_data->createdUser->full_name;
            $each_row['Employee ID'] = $each_data->createdUser->trashedEmployee->employee_no;
            $each_row['Shift Note'] = $each_data->notes;
            $each_row['Client'] = $each_data->customer != null ? $each_data->customer->client_name : '';
            if ($dashboard_view) {
                $each_row['Location'] = $each_data->notes;
            }
            if (in_array($role_name, ['admin', 'super_admin'])) {
                array_push($datavalues, $each_row);
            } else {
                if (in_array($clientid, $customer_ids)) {
                    array_push($datavalues, $each_row);
                }
            }
            $index += 1;
            //  }
        }
        return $datavalues;
    }

    /**
     * Where there is no date info, this function can be used
     * to approximate the date from shift submit time and
     * provided time from app.
     * Will not work if time difference is more than 24 hours.
     */
    public function calcDateFromAcutalShiftTime($given_time, $shift_submit_time, $shift_submit_date)
    {
        $journal_time = Carbon::parse($given_time);
        $shift_submit_time = Carbon::parse($shift_submit_time);
        $journal_calc_date = $shift_submit_date;
        if ($journal_time->diffInSeconds($shift_submit_time, false) < 0) {
            $journal_calc_date = Carbon::parse($journal_calc_date)->subDays(1)->toDateString();
        }
        return $journal_calc_date;
    }

    /**
     * To save guard tours against a shift-mobile
     *
     * @param [type] $shiftJornals
     * @param [type] $employee_shift_id
     * @return void
     */
    public function saveShiftJournal($shiftJournals, $employee_shift, $customer_id, $user_id)
    {
        foreach ($shiftJournals as $key => $shiftJournal) {
            $shiftJournals['shift_id'] = $employee_shift->id;
            $shiftJournals['submitted_time'] = $shiftJournal->time;
            $shiftJournals['submitted_date'] = $shiftJournal->date ?? null;
            $shiftJournals['notes'] = $shiftJournal->notes;
            $shiftJournals['created_by'] = $user_id;
            $shiftJournals['customer_id'] = $customer_id;
            $this->shiftJournalModel->create($shiftJournals);
        }
    }

    /**
     * To save shift journal against a shift in web
     *
     * @param [type] $request
     * @return void
     */
    public function saveShiftJournalWeb($request)
    {
        $shiftJournals['submitted_time'] = date("H:i:s");
        $shiftJournals['submitted_date'] = date("Y-m-d");
        $shiftJournals['notes'] = $request->note;
        $shiftJournals['shift_start_time'] = $request->shift_start_time;
        $shiftJournals['customer_id'] = $request->customer_id;
        $shiftJournals['created_by'] = \Auth::user()->id;
        //  return dd($request->all());
        return $this->shiftJournalModel->create($shiftJournals);
    }

    /**
     * To save shift journal against a shift in web
     *
     * @param [type] $request
     * @return void
     */
    public function shiftEndJournal($customer_id, $start_time, $shift_id)
    {
        $shiftDetails = $this->employeeShift->whereDate('end', date("Y-m-d"))->with(['shift_payperiod' => function ($query) use ($customer_id) {
            $query->where('customer_id', '=', $customer_id)->where('employee_id', '=', \Auth::user()->id);
        }])->first();

        if ($shiftDetails != null) {
            $shiftJournals['submitted_time'] = date("H:i:s");
            $shiftJournals['submitted_date'] = date("Y-m-d");
            $shiftJournals['notes'] = 'End of Shift';
            $shiftJournals['shift_start_time'] = $start_time;
            $shiftJournals['customer_id'] = $customer_id;
            $shiftJournals['created_by'] = \Auth::user()->id;
            $this->shiftJournalModel->create($shiftJournals);

            $shiftJournalId = ShiftJournal::where('shift_start_time', $start_time)
                ->where('customer_id', $customer_id)
                ->where('created_by', \Auth::user()->id)->get()->pluck('id');

            ShiftJournal::whereIn('id', $shiftJournalId)
                ->update(['shift_id' => $shift_id, 'shift_submitted' => 1]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * To add shift End rows in Shift module Entries
     *
     * @param [type] $customer_id
     * @param [type] $start_time
     * @param [type] $shift_id
     * @return void
     */
    public function shiftEndModule($customer_id, $start_time, $shift_id)
    {

        $modules = $this->shiftModuleRepository->getAllCustomerModule($customer_id);
        if (!empty($modules)) {
            foreach ($modules as $eachmodule) {
                $shiftModuleEntry['customer_id'] = $customer_id;
                $shiftModuleEntry['module_id'] = $eachmodule->id;
                $shiftModuleEntry['shift_id'] = $shift_id;
                $shiftModuleEntry['shift_start_date'] = $start_time;
                $shiftModuleEntry['field_id'] = 0;
                $shiftModuleEntry['field_value'] = 'End';
                $shiftModuleEntry['created_by'] = \Auth::user()->id;
                $this->shiftModuleEntryModel->create($shiftModuleEntry);
            }
        }
        return true;
    }

    /**
     * Get the List of Shift Journal
     * @param $customer_id
     * @return string
     */
    public function getTimeshiftList($customer_id)
    {
        $datavalues = array();
        $index = 1;
        $query = ShiftJournal::with('shift', 'shift.shift_payperiod.trashed_user.trashedEmployee', 'createdUser')->where('customer_id', '=', $customer_id);
        if (!\Auth::user()->can('view_all_shift_journal')) {
            $id_array[] = \Auth::user()->id;

            if (\Auth::user()->can('view_allocated_shift_journal')) {
                $id_array = $this->employee_allocation_repository->getEmployeeIdAssigned(\Auth::user()->id)->toArray();
                array_push($id_array, \Auth::user()->id);
            }
            $query->whereIn('created_by', $id_array);
        }
        $shift_journal_data = $query->select([
            'id', 'shift_start_time', 'shift_id', 'submitted_date', 'submitted_time', 'notes', 'image', 'customer_id', 'created_by', \DB::raw('DATE_FORMAT(created_at,\'%Y-%m-%d\') as date, DATE_FORMAT(created_at,\'%l:%i %p\') as time, DATE_FORMAT(shift_start_time,\'%l:%i %p\') as start_time '),
        ])->orderBy('created_by')->orderBy('created_at', 'asc')->get();
        if (count($shift_journal_data) == 0) {
            $each_row['Start Date'] = null;
            $each_row['End Date'] = null;
            $each_row['Start Time'] = null;
            $each_row['End Time'] = null;
            $each_row['Total (In Hours)'] = null;
            $each_row['Employee ID'] = null;
            $each_row['Guard'] = null;
            $each_row['Shift Note'] = null;
            array_push($datavalues, $each_row);
        }

        $temp_start = null;
        foreach ($shift_journal_data as $each_data) {

            // $each_row['id'] = $each_data->id;
            // $each_row['shift_id'] = $each_data->shift_id;
            $shift_start_time = ($each_data->shift_start_time != null) ? $each_data->shift_start_time : $each_data->created_at;
            $each_row['Created Date'] = date("Y-m-d", strtotime($shift_start_time)); //hidden value for shift module
            $each_row['Start Date'] = date("Y-m-d", strtotime($shift_start_time));
            $each_row['End Date'] = $each_data->submitted_date;
            //  $each_row['End Date'] = ($each_data->submitted_date != null) ? $each_data->submitted_date : $this->calcDateFromAcutalShiftTime($each_data->submitted_time, $each_data->time, $each_data->date);
            $end_date_time = $each_row['End Date'] . ' ' . $each_data->submitted_time;

            if (($temp_start == null) || ($temp_start != null && $temp_start != $shift_start_time)) {
                $index = 1;
                $temp2 = $temp = null;
            }
            $temp_start = $shift_start_time;
            if ($index == 1) {

                $each_row['Start Time'] = date("H:i", strtotime($shift_start_time));
                $each_row['End Time'] = date("H:i", strtotime($end_date_time));
                $start_time = new DateTime($shift_start_time);
                $end_time = new DateTime($end_date_time);
                //   $each_row['Total (In Hours)'] = $end_time->diff($start_time)->format('%H:%I');
                $interval = date_diff($start_time, $end_time);
                $each_row['Total (In Hours)'] = $interval->format('%H') + ($interval->d * 24) . ':' . $interval->format('%I');
                $temp = $end_date_time;
            } else {
                $each_row['Start Time'] = date("H:i", strtotime(isset($temp2) ? $temp2 : $temp));
                $each_row['End Time'] = date("H:i", strtotime($end_date_time));
                $start_time = new DateTime(isset($temp2) ? $temp2 : $temp);
                $end_time = new DateTime($end_date_time);
                //  $each_row['Total (In Hours)'] = $end_time->diff($start_time)->format('%H:%I');
                $interval = date_diff($start_time, $end_time);
                $each_row['Total (In Hours)'] = $interval->format('%H') + ($interval->d * 24) . ':' . $interval->format('%I');

                $temp2 = $end_date_time;
            }
            //  $each_row['Start Time'] = $each_data->time;
            //  $each_row['End Time'] = $each_data->time;

            $each_row['Employee ID'] = $each_data->createdUser->trashedEmployee->employee_no;
            $each_row['Guard'] = $each_data->createdUser->full_name;
            $each_row['Shift Note'] = $each_data->notes;

            array_push($datavalues, $each_row);
            $index += 1;
        }
        return $datavalues;
    }

    /**
     * Get the List of Shift Journal
     * @param $customer_id
     * @return string
     */
    public function getShiftModuleList($module_id, $customer_id, $time_shift_enabled, $from_date = null, $to_date = null, $emp_id = null, $widgetRequest = false)
    {
        $user = \Auth::user();
        $employees = $this->employee_allocation_repository->getEmployeeAssigned([$user->id]);
        $employeeAssignedArray = array();

        foreach ($employees as $key => $emp) {
            $employeeAssignedArray[] = $emp->user_id;
        }

        if ($user->hasPermissionTo('view_shift_journal_20_transaction')) {
            array_push($employeeAssignedArray, \Auth::id());
        }

        if ($time_shift_enabled) {
            $entries = $this->shiftModuleEntryModel
                ->select(\DB::raw('DATE(created_at) as date'), 'created_by', 'created_at', 'shift_start_date')
                ->where('module_id', '=', $module_id)
                ->where('customer_id', '=', $customer_id)
                ->where('shift_start_date', '!=', null)
                ->with(['createdUser' => function ($q) {
                    $q->select('id', 'first_name', 'last_name', 'email');
                }])
                ->when($user->hasPermissionTo('view_shift_journal_20_transaction') && !$user->hasPermissionTo('view_all_shift_journal_20_transaction'), function ($q) use ($employeeAssignedArray) {
                    return $q->whereIn('created_by', $employeeAssignedArray);
                })
                ->when($emp_id != null, function ($q) use ($emp_id) {
                    return $q->where('created_by', $emp_id);
                })
                ->when($from_date != null && $to_date != null, function ($q) use ($from_date, $to_date) {
                    return $q->whereDate('created_at', '>=', $from_date)
                        ->whereDate('created_at', '<=', $to_date);
                })
                ->groupBy('created_by', 'created_at', 'shift_start_date')
                ->get();
        } else {
            $entries = $this->shiftModuleEntryModel
                ->select(\DB::raw('DATE(created_at) as date'), 'created_by', 'created_at')
                ->where('module_id', '=', $module_id)
                ->where('customer_id', '=', $customer_id)
                ->where('field_id', '!=', 0)
                ->with(['createdUser' => function ($q) {
                    $q->select('id', 'first_name', 'last_name', 'email');
                }])
                ->when($user->hasPermissionTo('view_shift_journal_20_transaction') && !$user->hasPermissionTo('view_all_shift_journal_20_transaction'), function ($q) use ($employeeAssignedArray) {
                    $q->whereIn('created_by', $employeeAssignedArray);
                })
                ->when($emp_id != null, function ($q) use ($emp_id) {
                    return $q->where('created_by', $emp_id);
                })
                ->when($from_date != null && $to_date != null, function ($q) use ($from_date, $to_date) {
                    return $q->whereDate('created_at', '>=', $from_date)
                        ->whereDate('created_at', '<=', $to_date);
                })
                ->groupBy('created_by', 'created_at')
                ->get();
        }

        if (!empty($entries)) {
            $temp_field_id = array();
            $field_detail = array();
            foreach ($entries as $key => $each_entry) {
                $field_entries = ShiftModuleEntry::with(['attachments.attachment' => function ($q) {
                    $q->select('id');
                },
                    'type' => function ($q) {
                        $q->select('id', 'module_id', 'field_type', 'field_status', 'order_id', 'is_multiple_photo');
                    }])
                    ->where('module_id', '=', $module_id)
                    ->where('customer_id', '=', $customer_id)
                    ->where('created_by', '=', $each_entry->created_by)
                    ->where('created_at', '=', $each_entry->created_at)
                    ->get();

                $field_entries = $field_entries->map(function ($item) {

                    if ($item->attachments) {
                        $inner_data = $item->attachments;
                        $inner_data = $inner_data->map(function ($inner_data_item) {
                            if ($inner_data_item->attachment) {
                                $file_id = $inner_data_item->attachment->id;
                            } else {
                                $file_id = 0;
                            }

                            return $inner_data_item;
                        });
                    }
                    return $item;
                });

                foreach ($field_entries as $field_entry) {
                    if ($field_entry->field_id != 0) {
                        if ($field_entry->type->field_type == 1) {
                            $img_ids = array();
                            if (!empty($field_entry->attachments)) {
                                foreach ($field_entry->attachments as $value) {
                                    if ($value->attachment) {
                                        $img_ids[] = $value->attachment->id;
                                    } else {
                                        $img_ids[] = "--";
                                    }
                                }
                            }
                            $temp_field_id[$field_entry->field_id] = $img_ids;
                        } elseif ($field_entry->type->field_type == 5) {
                            $vid_ids = array();
                            if (!empty($field_entry->attachments)) {
                                foreach ($field_entry->attachments as $value) {
                                    if ($value->attachment) {
                                        $vid_ids[] = $value->attachment->id;
                                    } else {
                                        $vid_ids[] = "--";
                                    }
                                }
                            }
                            $temp_field_id[$field_entry->field_id] = $vid_ids;
                        } else {
                            $temp_field_id[$field_entry->field_id] = $field_entry->field_value;
                        }
                    } else {
                        $temp_field_id = $field_entry->field_value;
                    }
                }
                $field_detail[$key]['customer_id'] = $each_entry->customer_id;
                $field_detail[$key]['created_at'] = $each_entry->created_at;
                if ($time_shift_enabled) {
                    $field_detail[$key]['shift_start_date'] = $each_entry->shift_start_date;
                }
                $field_detail[$key]['employee_id'] = $each_entry->createdUser->trashedEmployee->employee_no;
                $field_detail[$key]['guard'] = $each_entry->createdUser->full_name;
                $field_detail[$key]['field_val'] = $temp_field_id;
                unset($temp_field_id);
            }
        }

        return $this->prepareData($field_detail, $module_id, $time_shift_enabled, $widgetRequest);
    }

    public function prepareData($data, $module_id, $time_shift_enabled, $widgetRequest = false)
    {

        $datavalues = array();
        $each_row = array();

        $fields = ShiftModuleField::where('module_id', '=', $module_id)->where('field_status', 1)->get();
        if (!empty($data)) {
            $index = 1;
            $temp_start = null;
            foreach ($data as $key => $each_data) {

                $each_row['Created Date'] = isset($each_data['created_at']) ? date("Y-m-d H: i:s", strtotime($each_data['created_at'])) : '-';
                $each_row['Employee ID'] = $each_data['employee_id'];
                $each_row['Guard'] = $each_data['guard'];

                if ($time_shift_enabled) {

                    if (($temp_start == null) || ($temp_start != null && $temp_start != $each_data['shift_start_date'])) {
                        $index = 1;
                        $temp2 = $temp = null;
                    }
                    $temp_start = $each_data['shift_start_date'];
                    if ($index == 1) {
                        $each_row['Start Date'] = isset($each_data['shift_start_date']) ? date("Y-m-d", strtotime($each_data['shift_start_date'])) : '-';
                        $each_row['End Date'] = isset($each_data['created_at']) ? date("Y-m-d", strtotime($each_data['created_at'])) : '-';

                        $each_row['Start Time'] = date("H:i", strtotime($each_data['shift_start_date']));
                        $each_row['End Time'] = date("H:i", strtotime($each_data['created_at']));
                        $start_time = new DateTime($each_data['shift_start_date']);
                        $end_time = new DateTime($each_data['created_at']);
                        $interval = date_diff($start_time, $end_time);
                        $each_row['Total (In Hours)'] = $interval->format('%H') + ($interval->d * 24) . ':' . $interval->format('%I');
                        $each_row['Shift Status'] = 'Start of Shift';
                        $temp = $each_data['created_at'];
                    } else {
                        $each_row['Start Date'] = date("Y-m-d", strtotime(isset($temp2) ? $temp2 : $temp));
                        $each_row['End Date'] = isset($each_data['created_at']) ? date("Y-m-d", strtotime($each_data['created_at'])) : '-';

                        $each_row['Start Time'] = date("H:i", strtotime(isset($temp2) ? $temp2 : $temp));
                        $each_row['End Time'] = date("H:i", strtotime($each_data['created_at']));
                        $start_time = new DateTime((isset($temp2) ? $temp2 : $temp));
                        $end_time = new DateTime($each_data['created_at']);
                        $interval = date_diff($start_time, $end_time);
                        $each_row['Total (In Hours)'] = $interval->format('%H') + ($interval->d * 24) . ':' . $interval->format('%I');

                        $each_row['Shift Status'] = '--';
                        $temp2 = $each_data['created_at'];
                    }
                } else {
                    $each_row['Date'] = isset($each_data['created_at']) ? date("Y-m-d", strtotime($each_data['created_at'])) : '-';
                    $each_row['Time'] = isset($each_data['created_at']) ? date("H:i", strtotime($each_data['created_at'])) : '-';
                }

                if (($time_shift_enabled == 1) && (($each_data['field_val'] == 'End') || ($each_data['field_val'] == 'Start'))) {
                    $each_row['Shift Status'] = ($each_data['field_val'] == 'End') ? 'End of Shift' : 'Start of Shift';
                    foreach ($fields as $eachfield) {
                        $each_row[$eachfield->field_name] = '';
                    }
                } else {
                    foreach ($fields as $nkey => $eachfield) {
                        if ($eachfield->field_type == 1) {
                            $image_icons = null;
                            if (isset($each_data['field_val'][$eachfield->id]) && !empty($each_data['field_val'][$eachfield->id])) {
                                foreach ($each_data['field_val'][$eachfield->id] as $eachimg) {
                                    $image_icons .= '<a id="location" onclick="showimage(' . "'" . $eachimg . "'" . ');"  href="javascript:void(0);"><i class="fa fa-file-image fa-2x"></i></a>&nbsp;';
                                }
                                $each_row[$eachfield->field_name] = $image_icons;
                            }
                        } elseif ($eachfield->field_type == 2) {
                            if (isset($each_data['field_val'][$eachfield->id])) {
                                $cordinates = explode('#', $each_data['field_val'][$eachfield->id]);
                                $lat = $cordinates[0];
                                $long = $cordinates[1];
                            }
                            $each_row[$eachfield->field_name] = isset($each_data['field_val'][$eachfield->id]) ? '<a id="location" onclick="showlocation(' . $lat . ',' . $long . ');"  href="javascript:void(0);"><img width="40px" src="' . url("images/map_pointer.png") . '" ></a>' : '--';
                        } elseif ($eachfield->field_type == 4) {
                            $notes = isset($each_data['field_val'][$eachfield->id]) ? $each_data['field_val'][$eachfield->id] : '--';
                            $each_row[$eachfield->field_name] = isset($each_data['field_val'][$eachfield->id]) ? '<span class="notesspan">' . $notes . ' </span>' : '--';
                        } elseif ($eachfield->field_type == 5) {
                            $video_icons = null;
                            if (isset($each_data['field_val'][$eachfield->id]) && !empty($each_data['field_val'][$eachfield->id])) {
                                foreach ($each_data['field_val'][$eachfield->id] as $eachvideo) {
                                    $video_icons .= '<a onclick="showVideo(' . "'" . $eachvideo . "'" . ');"  href="javascript:void(0);"><i class="fa fa-file-video fa-2x"></i></a>&nbsp;';
                                }
                            } else {
                                $video_icons = '--';
                            }
                            $each_row[$eachfield->field_name] = $video_icons;
                        } else {
                            $each_row[$eachfield->field_name] = isset($each_data['field_val'][$eachfield->id]) ? $each_data['field_val'][$eachfield->id] : '--';
                        }
                    }
                }
                array_push($datavalues, $each_row);
                unset($each_row);
                if ($widgetRequest && ($index == config('dashboard.shift_modules_row_limit'))) {
                    break;
                }
                $index += 1;
            }
        } else {
            $each_row['Created Date'] = null;
            $each_row['Employee ID'] = null;
            $each_row['Guard'] = null;

            if ($time_shift_enabled) {
                $each_row['Start Date'] = null;
                $each_row['End Date'] = null;

                $each_row['Start Time'] = null;
                $each_row['End Time'] = null;
                $each_row['Total (In Hours)'] = null;
                $each_row['Shift Status'] = null;
            } else {
                $each_row['Date'] = null;
                $each_row['Time'] = null;
            }

            foreach ($fields as $eachfield) {
                $each_row[$eachfield->field_name] = '';
            }

            array_push($datavalues, $each_row);
        }

        return $datavalues;
    }

    public function getShiftModuleMappingList($customer_id, $module_id, $date)
    {
        $response = [];
        $entries = $this->shiftModuleEntryModel::where('module_id', '=', $module_id)
            ->where('customer_id', '=', $customer_id)
            ->where('field_value', 'like', '%#%')
            ->with(['createdUser' => function ($q) {
                $q->select('id', 'first_name', 'last_name', 'email');
            }])
            ->whereDate('created_at', '=', $date)
            ->get()->toArray();

        $filters = [];
        $fields = ShiftModuleField::with('dropdown.shiftModuleDropdownOption')->where('module_id', '=', $module_id)->where('field_status', 1)->get()->toArray();

        foreach ($fields as $nkey => $eachfield) {
            if (($eachfield['dropdown_id'] != 0) && ($eachfield['dropdown']['dropdown_name'] != '')) {
                $filters[$nkey]['dropdown']['name'] = $eachfield['field_name'];
                $filters[$nkey]['dropdown']['id'] = $eachfield['dropdown']['id'];
                foreach ($eachfield['dropdown']['shift_module_dropdown_option'] as $key => $value) {
                    $filters[$nkey]['options'][$value['id']] = $value['option_name'];
                }
            }

        }

        foreach ($entries as $key => $value) {
            $cordinates = explode('#', $value['field_value']);
            $response[$key]['lat'] = $cordinates[0];
            $response[$key]['long'] = $cordinates[1];
            $response[$key]['userid'] = $value['created_by'];
            $response[$key]['first_name'] = $value['created_user']['first_name'];
            $response[$key]['last_name'] = ($value['created_user']['last_name'] != null) ? $value['created_user']['last_name'] : '';
            $response[$key]['email'] = $value['created_user']['email'];
            $response[$key]['created_at'] = $value['created_at'];

            $details = $this->shiftModuleEntryModel::with('fieldName')->where('module_id', '=', $module_id)
                ->where('customer_id', '=', $customer_id)
                ->where('created_at', '=', $value['created_at'])
                ->get()->toArray();
            if (!empty($details)) {
                foreach ($details as $mkey => $each) {
                    if ($each['field_value'] != null) {
                        $response[$key]['details'][$each['field_name']['field_name']] = $each['field_value'];
                    }
                }
            }

        }
        $response['filters'] = $filters;
        return $response;
    }

}
