<?php

namespace Modules\Timetracker\Repositories;

use Auth;
use Mail;
use Modules\Timetracker\Models\EmployeeTimeoff;
use Modules\Timetracker\Mail\EmployeeTimeoffRequest;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\OperationCentreEmailRepository;
use Modules\Admin\Models\Customer;
use Carbon\Carbon;
use App\Repositories\MailQueueRepository;

class EmployeeTimeoffRepository
{

    public $employeeTimeoffModel, $customerRepository, $operationCentreEmailRepository, $mailQueueRepository;

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */

    /**
     * Create a new EmployeeAvailabilityRepository instance.
     *
     * @param  \App\Models\Notification $Notification
     */
    public function __construct(
        EmployeeTimeoff $employeeTimeoffModel,
        CustomerRepository $customerRepository,
        OperationCentreEmailRepository $operationCentreEmailRepository,
        MailQueueRepository $mailQueueRepository
    ) {
        $this->employeeTimeoffModel = $employeeTimeoffModel;
        $this->customerRepository = $customerRepository;
        $this->operationCentreEmailRepository = $operationCentreEmailRepository;
        $this->mailQueueRepository = $mailQueueRepository;
    }

    /*
     * get employee timeoff requests
     */

    public function getTimeoffRequests()
    {
        // $timeoff = EmployeeTimeoff::with(['user', 'reasons', 'customer', 'employee','cpidRate'])->get();
        // return $timeoff;

        $mytime = Carbon::now()->subDays(31)->format('Y-m-d');
        $timeoff = EmployeeTimeoff::select('*', \DB::raw('(TIME_FORMAT(start_time, "%l:%i %p")) as start_time'), \DB::raw('(TIME_FORMAT(end_time, "%l:%i %p")) as end_time'), \DB::raw('(select cp_id from cpid_rates where id=employee_timeoff.cpidRate_id) as cp_id '), \DB::raw('(select cpid from cpid_lookups where id=cp_id) as cp_idlabel '))
            ->with(['user', 'reasons', 'customer', 'employee', 'employee.trashedUser', 'cpidRate'])
            ->where('start_date', '>=', $mytime)
            ->orderBy('created_at', 'desc')->get();
        return $timeoff;
    }

    public function saveEmployeeTimeOff($data, $api = false)
    {
        if ($this->isTrashedCustomer($data['project_id'])) {
            return response()->json(['success' => false, 'message' => 'Customer not found or already removed'], 406);
        }

        $data['mail_send'] = 0;
        $timeOff = $this->employeeTimeoffModel->updateOrCreate($data);
        if ($timeOff->id > 0) {
            try {
                $status = false;
                $model_name = ($api) ? 'Employee Timeoff api' : 'Employee Timeoff';
                $subject = "Employee time off request";
                $message = 'Hi, ' . $timeOff->user->getFullNameAttribute() . " has submitted a time-off request.";

                $authorityDetails = $this->customerRepository->getCustomerWithMangers($data['project_id'], false);
                //                dd($authorityDetails['details']);
                if (!empty($authorityDetails)) {
                    //send mail to supervisor
                    if (array_key_exists('details', $authorityDetails) && !empty($authorityDetails['details']) && array_key_exists('employee_customer_supervisor', $authorityDetails['details']) && !empty($authorityDetails['details']['employee_customer_supervisor'])) {
                        foreach ($authorityDetails['details']['employee_customer_supervisor'] as $to) {
                            if (array_key_exists('supervisor', $to) && array_key_exists('email', $to['supervisor'])) {
                                $to = $to['supervisor']['email'];
                                if (!empty($to)) {
                                    $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name);
                                    $status = true;
                                }
                            }
                        }
                    }

                    //send mail to area manager
                    if (array_key_exists('details', $authorityDetails) && !empty($authorityDetails['details']) && array_key_exists('employee_customer_area_manager', $authorityDetails['details']) && !empty($authorityDetails['details']['employee_customer_area_manager'])) {
                        foreach ($authorityDetails['details']['employee_customer_area_manager'] as $to) {
                            if (array_key_exists('area_manager', $to) && array_key_exists('email', $to['area_manager'])) {
                                $to = $to['area_manager']['email'];
                                if (!empty($to)) {
                                    $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name);
                                    $status = true;
                                }
                            }
                        }
                    }
                }
                //send mail to operation centeres
                $ocMails = $this->operationCentreEmailRepository->getLookupList();
                if (!empty($ocMails)) {
                    $ocMails = array_unique($ocMails);
                    $ocMails = array_values($ocMails);
                    foreach ($ocMails as $to) {
                        if (!empty($to)) {
                            $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name);
                            $status = true;
                        }
                    }
                }

                if ($status) {
                    //Email success log here
                    $timeOff->mail_send = 1;
                    $timeOff->save();
                }
            } catch (\Exception $e) {
                //error log
            }

            return response()->json(['success' => true, 'message' => 'Employee time off has been submitted successfully'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to save data'], 404);
        }
    }

    /**
     * To send mail notification
     *
     * @param [type] $timeOff
     * @param [type] $to
     * @param [type] $authorityName
     * @param [type] $cc
     * @return void
     */
    public function sendNotification($timeOff, $to, $authorityName, $cc = null)
    {
        $mail = Mail::to($to);
        if ($cc != null) {
            $mail->cc($cc);
        }
        $mail->queue(new EmployeeTimeoffRequest($authorityName, $timeOff));
    }

    /*
     * check whether the customer is untrashed
     */

    public function isTrashedCustomer($id)
    {
        $customerObject = Customer::find($id);
        if (empty($customerObject) || (!empty($customerObject) && $customerObject->trashed())) {
            return true;
        }
        return false;
    }
}
