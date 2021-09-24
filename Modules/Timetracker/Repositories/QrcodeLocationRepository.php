<?php

namespace Modules\Timetracker\Repositories;
use App\Models\MailQueue;
use Modules\Admin\Models\CustomerQrcodeLocation;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Timetracker\Models\CustomerQrcodeWithShift;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;

use PDF;
use File;
use App\Repositories\MailQueueRepository;
use App\Services\HelperService;
use Modules\Admin\Models\Customer;
class QrcodeLocationRepository
{
    protected $customerQrcodeWithShift;

    public function __construct(CustomerQrcodeWithShift $customerQrcodeWithShift,
    EmployeeShift $employeeShift,
    CustomerRepository $customer_repository,
    MailQueueRepository $mailQueueRepository,
    UserRepository $userRepository,
    EmployeeAllocationRepository $employeeAllocationRepository
    )
    {
        $this->customerQrcodeWithShift = $customerQrcodeWithShift;
        $this->employeeShiftModel = $employeeShift;
        $this->customer_repository = $customer_repository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->userRepository=$userRepository;

        $this->mailQueueRepository = $mailQueueRepository;
    }

    /**
     *  To List Mobile Security Patrol Trips
     *
     */
    public function index($limit = null, $fromdate, $todate, $client_id = null, $employee_id = null)
    {
        $qry = $this->getQrCodeHistoryDetails($fromdate, $todate, $client_id, $employee_id);
        if ($limit != null) {
            $qry = $qry->take($limit);
        }
        $result = $qry->get();
        $formatted_results = [];

        foreach ($result as $key => $row) {
            $formatted_results[$key]['shift_id'] = $row['id'];
            $formatted_results[$key]['start'] = date("g:i A", strtotime($row['start']));
            $formatted_results[$key]['end'] = ($row['end'] != null) ? date("g:i A", strtotime($row['end'])) : '--';
            $formatted_results[$key]['created_at'] = $row['created_at']->toFormattedDateString(); //->format('g:i A');
            $formatted_results[$key]['employee_no'] = $row['shift_payperiod']['trashed_user']['trashedEmployee']['employee_no'];
            $formatted_results[$key]['first_name'] = $row['shift_payperiod']['trashed_user']['full_name'];
            $formatted_results[$key]['employee_name'] = $row['shift_payperiod']['trashed_user']['full_name'];
            $formatted_results[$key]['project_number'] = $row['shift_payperiod']['trashed_customer']['project_number'];
            $formatted_results[$key]['client_name'] = $row['shift_payperiod']['trashed_customer']['client_name'];
            // $customer_id = $row['shift_payperiod']['customer_id'];

            // $formatted_results[$key]['missed'] = max($row['qrcode_history']['missed'], 0) ?? 0;
            // $total = $row['qrcode_history']['scanned'] + $row['qrcode_history']['missed'];
            $formatted_results[$key]['scanned'] = $row['qrcode_history']['scanned'] ?? 0;
            // $average_value = (($total != 0) ? number_format((($row['qrcode_history']['scanned'] / $total) * 100), 2, '.', ',') : 0);
            // if (substr($average_value, strpos($average_value, ".") + 1) == '00') {
            //     $formatted_results[$key]['avg'] = intval($average_value) . '%';
            // } else {
            //     $formatted_results[$key]['avg'] = $average_value . '%';
            // }
        }
        return array_values($formatted_results);
    }

    public function total_qrcode_count_by_customer($customer_id, $isWeekDay)
    {
        $selectedField = ($isWeekDay) ? 'no_of_attempts_week_ends' : 'no_of_attempts';
        return CustomerQrcodeLocation::where('customer_id', $customer_id)->sum($selectedField);
    }

    public function getCoordinates($id)
    {
        $coordinate = $this->customerQrcodeWithShift->where('id', $id)->first();
        $lat_lng_coordinates = [];
        $formatted_coordinates = ''; //[];

        $formatted_coordinates .= $coordinate['latitude'] . ',' . $coordinate['longitude'] . '|';
        $lat_lng_coordinates['latitude'] = $coordinate['latitude'];
        $lat_lng_coordinates['longitude'] = $coordinate['longitude'];

        $formatted_coordinates = rtrim($formatted_coordinates, '|');
        $coordinates['formatted_coordinates'] = $formatted_coordinates;
        $coordinates['original_coordinates'] = $lat_lng_coordinates;
        return $coordinates;
    }

    public function get_qrcode_count_for_each_shift($shiftid)
    {
        return $this->customerQrcodeWithShift->where('shift_id', $shiftid)->count();
    }

    /*
     *query to fetch Qr Code History Details by permissions
     */
    public function getQrCodeHistoryDetails($fromDate, $toDate, $client_id, $employee_id)
    {
        $logged_in_user = \Auth::user();
        $qry = $this->employeeShiftModel->when(($fromDate != null && $toDate != null), function ($q) use ($fromDate, $toDate) {
            $q->where('start', '>=', $fromDate)->where('end', '<=', $toDate);
        });
        if ((!$logged_in_user->hasPermissionTo('view_all_qrcode_data')) && ($logged_in_user->hasPermissionTo('view_allocated_qrcode_data'))) {
            $allocatedCustomers = $this->customer_repository->getAllAllocatedCustomerId([$logged_in_user->id]);
            $qry = $qry->whereHas('shift_payperiod.trashed_customer', function ($query) use ($allocatedCustomers) {
                $query->whereIn('customer_id', $allocatedCustomers);
            });
        } elseif (!$logged_in_user->hasPermissionTo('view_all_qrcode_data')) {
            $logged_in_user_id = $logged_in_user->id;
            $qry = $qry->whereHas('shift_payperiod', function ($query) use ($logged_in_user_id) {
                $query->where('employee_id', $logged_in_user_id);
            });
        }
        $qry->Has('qrcode_history')->orderBy('created_at', 'desc')->with([
            'shift_payperiod.trashed_customer',
            'shift_payperiod.trashed_user.trashedEmployee',
        ]);

        $qry = $qry->when($employee_id!=null, function ($q) use ($employee_id) {
            $q->whereHas('shift_payperiod', function ($query) use ($employee_id) {
                $query->where('employee_id', $employee_id);
            });
            return $q;
        });


        $qry =$qry->when($client_id!=null, function ($q) use ($client_id) {
            $q->whereHas('shift_payperiod.trashed_customer', function ($query) use ($client_id) {
                $query->where('id', $client_id);
            });
            return $q;
        });

        return $qry;
    }

    public function getQrpatrolList($shiftid)
    {
        $details = $this->customerQrcodeWithShift->with(['QrcodeWithTrashed', 'attachments', 'shift'])->where('shift_id', $shiftid)->orderBy('time', 'asc')->get();
        $formatted_results = [];
        $i = 0;
        $qrcode_details = array();
        if (!empty($details)) {

            foreach ($details as $each_data) {
                if ($i == 0) {
                    $qrcode_details['start_time'] = date("g:i A", strtotime($each_data['shift']['start']));
                    $qrcode_details['end_time'] = date("g:i A", strtotime($each_data['time']));
                } else {
                    $qrcode_details['start_time'] = date("g:i A", strtotime($end_time));
                    $qrcode_details['end_time'] = ($each_data['time'] == $end_time) ? date("g:i A", strtotime($each_data['shift']['start'])) : date("g:i A", strtotime($each_data['time']));
                }
                $end_time = $each_data['time'];
                $qrcode_details['created_at'] = $each_data['created_at']->toFormattedDateString();
                $qrcode_details['check_point'] = isset($each_data['QrcodeWithTrashed']['location']) ? $each_data['QrcodeWithTrashed']['location'] : '--';
                $qrcode_details['comments'] = $each_data['comments'] ?? '';
                $images = [];
                if (isset($each_data['attachments'])) {
                    foreach ($each_data['attachments'] as $eachimage) {
                        $images[] = $eachimage->attachment_id;
                    }
                    $qrcode_details['image'] = $images;
                } else {
                    $qrcode_details['image'] = '';
                }

                $qrcode_details['latitude'] = $each_data['latitude'] ?? '';
                $qrcode_details['longitude'] = $each_data['longitude'] ?? '';
                $qrcode_details['id'] = $each_data['id'];
                $formatted_results['qrcode_details'][] = $qrcode_details;
                $i++;

            }
            return $formatted_results;
        } else {
            return null;
        }
    }
    public function getDailyActivityReport()
     {
        $fromDate = \Carbon::yesterday()->format('Y-m-d'.' '.'07:00:00');
        $toDate = \Carbon::today()->format('Y-m-d'.' '.'07:00:00');
        $reportActivatedCustomers=Customer::select('id','client_name','project_number','contact_person_name','contact_person_email_id','billing_address','qr_recipient_email')->where('qr_daily_activity_report',1)->get();
        foreach($reportActivatedCustomers as $customer)
        {
           $customerId=$customer->id;
           $result = $this->customerQrcodeWithShift->with(['QrcodeWithTrashed','user'])->orderBy('time', 'asc')
            ->where('time', '>=', $fromDate)
            ->where('time', '<=', $toDate)
            ->where('customer_id', $customerId)
            ->get();
           if(count($result) >0)
            {
                $pdf=$this->generateDailyReport($result,$fromDate,$toDate,$customer);
            //    $customerDetails=$customer->client_name.' - '.$customer->billing_address;
            //    $pdf = PDF::loadView('timetracker::qr-patrol-daily-activity-report-pdf',compact('result','fromDate','toDate','customerDetails'));
            //     $dailyFilename = uniqid('daily_report_') . ".pdf";
            //     $path = storage_path('app');
            //     File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            //     $filename = '/'.$dailyFilename; //dd($filename);
            //     $pdf->save($path . $filename);die;

            }



        }



    }
    public function generateDailyReport($result,$fromDate,$toDate,$customer)
    {
        $customerDetails=$customer->client_name.' - '.$customer->billing_address;
        $customerId=$customer->id;
        $pdf = PDF::loadView('timetracker::qr-patrol-daily-activity-report-pdf',compact('result','fromDate','toDate','customerDetails'));
        $filename = "DailyActivityReport, ".\Carbon::today()->format('M d Y').".pdf";
        $reportPath='QrPatrolDailyReport/'.$customerId.'/'.\Carbon::today()->format('M d Y').'/'.$filename;
        $disk = \Storage::disk('awsS3Bucket');
        $savedData=$disk->put($reportPath, $pdf->output());
        if ($disk->exists($reportPath)) {
            $fileDet = $disk->get($reportPath);
            $this->QrPatrolDailyReportDailyReportMail($reportPath,$customer);
        }
        return true;

    }
    public function QrPatrolDailyReportDailyReportMail($reportPath,$customer)
    {
        $helper_variables = array(
            '{receiverFullName}' => HelperService::sanitizeInput('User'),
            );
        $toArr=explode(',',$customer->qr_recipient_email);
        $model_name = 'Modules\Timetracker\Models\CustomerQrcodeWithShift';
        foreach($toArr as $to){
            $this->mailQueueRepository->prepareMailTemplate(
                "qr_patrol_daily_activity_report",
                0,
                $helper_variables,
                $model_name,
                $requestor = 0,
                $assignee = 0,
                $from = null,
                $cc = null,
                $bcc = null,
                $mail_time = null,
                $created_by = null,
                $attachment_id = null,
                $to,
                $rec_candidate_id = 0,
                $rec_dynamic_email_text = null,
                $aws_bucket_name='awsS3Bucket',
                $reportPath
            );
        }

    }
}
