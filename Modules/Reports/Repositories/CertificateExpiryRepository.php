<?php

namespace Modules\Reports\Repositories;

use DB;
use \Carbon\Carbon;
use Modules\Admin\Models\SecurityClearanceUser;
use Modules\Admin\Models\UserCertificate;
use Modules\Admin\Models\EmailNotificationType;
use Modules\Admin\Models\EmailTemplate;
use App\Services\HelperService;
use App\Repositories\MailQueueRepository;
use Modules\Documents\Models\Document;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;

class CertificateExpiryRepository
{

protected $mailQueueRepository;

public function __construct(MailQueueRepository $mailQueueRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository)
{
    $this->mailQueueRepository = $mailQueueRepository;
    $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
}


public function getsecurityClearanceReport($filter)
{
    //dd($filter->get('customerId'));
    // dd($filter->get('userId'), $filter->get('securityClearanceLookUpId'), $filter->get('certificateMasterId'), $filter->get('statusId'));
    //dd(Carbon::now()->addDays(365)->toDateString());
    $today = Carbon::now()->toDateString();
    $daysto = Carbon::now()->addDays(29)->toDateString();
    $monthsFrom = Carbon::now()->addDays(30)->toDateString();
    $monthsTo = Carbon::now()->addDays(364)->toDateString();
    $years = Carbon::now()->addDays(364)->toDateString();
    $allocated_customer_users=[];
    if (\Auth::user()->hasPermissionTo('view_allocated_site_document_report')&&  !(\Auth::user()->hasPermissionTo('view_all_site_document_report'))) {
        $allocated_customer= $this->customerEmployeeAllocationRepository->getAllocatedCustomerId([\Auth::user()->id]);
         if(!empty($allocated_customer))
        $allocated_customer_users=$this->customerEmployeeAllocationRepository->allocationList($allocated_customer)->pluck('id')->toArray();
    }
    
    $securityClearanceData = SecurityClearanceUser::with('user.employee', 'securityClearanceLookups')
    ->whereHas('user')
  ->when(
          \Auth::user()->hasPermissionTo('view_allocated_site_document_report')&&  !(\Auth::user()->hasPermissionTo('view_all_site_document_report')),
            function ($permissionFilter) use ($filter, $allocated_customer_users) {
                return $permissionFilter->whereHas(
                        'user',
                        function ($permissionFind) use ($filter, $allocated_customer_users) {
                            $permissionFind->whereIn('id', $allocated_customer_users);
                        }
                    );
          })
                
                   ->when($filter->get('activeEmployee') == 1, function ($active) use ($filter) {
                    return $active->whereHas('user', function ($activeFilter) use ($filter) {
                        return $activeFilter->where('active', $filter->get('activeEmployee'));
                    });
                   })
                   ->when(
                       $filter->get('userId') != null,
                       function ($userIdFilter) use ($filter) {
                            return $userIdFilter->whereHas(
                                'user',
                                function ($userFind) use ($filter) {
                                    $userFind->whereIn('id', $filter->get('userId'));
                                }
                            );
                        }
                   )
                   ->when(
                       $filter->get('securityClearanceLookUpId') != null,
                        function ($securityClearanceLookUpIdFilter) use ($filter) {
                            return $securityClearanceLookUpIdFilter->whereIn('security_clearance_lookup_id', $filter->get('securityClearanceLookUpId'));
                        }
                   )
                   ->when(
                       $filter->get('statusId') == 0,
                        function ($statusIdFilter) use ($filter, $today) {
                            return $statusIdFilter->where('valid_until', '<', $today);
                        }
                   )
                   ->when(
                       $filter->get('statusId') == 1,
                        function ($statusIdFilter) use ($filter, $today, $daysto) {
                            return $statusIdFilter->whereBetween('valid_until', [$today, $daysto]);
                        }
                   )
                   ->when(
                       $filter->get('statusId') == 2,
                        function ($statusIdFilter) use ($filter, $monthsFrom, $monthsTo) {
                            return $statusIdFilter->whereBetween('valid_until', [$monthsFrom, $monthsTo]);
                        }
                   )
                   ->when(
                       $filter->get('statusId') == 3,
                        function ($statusIdFilter) use ($filter, $years) {
                            return $statusIdFilter->where('valid_until', '>', $years);
                        }
                   )
                   ->when(
                       $filter->get('customerId') != 0,
                        function ($customerIdFilter) use ($filter) {
                            $allocationlist=$this->customerEmployeeAllocationRepository->allocationList($filter->get('customerId'))->pluck('id')->toArray();
                            return $customerIdFilter->whereHas(
                                'user',
                                function ($customerFind) use ($filter, $allocationlist) {
                                    $customerFind->whereIn('id', $allocationlist);
                                 }
                             );
                        }
                   )
                   ->get();
                   return $securityClearanceData;
}

public function getUserCertificateReport($filter)
{
     //dd($filter->get('customerId'));
    // dd($filter->get('userId'), $filter->get('securityClearanceLookUpId'), $filter->get('certificateMasterId'), $filter->get('statusId'));
    $today = Carbon::now()->toDateString();
    $daysto = Carbon::now()->addDays(29)->toDateString();
    $monthsFrom = Carbon::now()->addDays(30)->toDateString();
    $monthsTo = Carbon::now()->addDays(364)->toDateString();
    $years = Carbon::now()->addDays(364)->toDateString();
    $allocated_customer_users=[];
    if (\Auth::user()->hasPermissionTo('view_allocated_site_document_report') &&  !(\Auth::user()->hasPermissionTo('view_all_site_document_report'))) {
        $allocated_customer= $this->customerEmployeeAllocationRepository->getAllocatedCustomerId([\Auth::user()->id]);
        if(!empty($allocated_customer))
        $allocated_customer_users=$this->customerEmployeeAllocationRepository->allocationList($allocated_customer)->pluck('id')->toArray();
    }
        $userCertificateData = UserCertificate::with('user.employee', 'certificateMaster')
        ->whereHas('user')
      ->when(
          \Auth::user()->hasPermissionTo('view_allocated_site_document_report')&& !(\Auth::user()->hasPermissionTo('view_all_site_document_report')),
            function ($permissionFilter) use ($filter, $allocated_customer_users) {
                return $permissionFilter->whereHas(
                        'user',
                        function ($permissionFind) use ($filter, $allocated_customer_users) {
                            $permissionFind->whereIn('id', $allocated_customer_users);
                        }
                    );
          })
        
                    ->when($filter->get('activeEmployee') == 1, function ($active) use ($filter) {
                        return $active->whereHas('user', function ($activeFilter) use ($filter) {
                            return $activeFilter->where('active', $filter->get('activeEmployee'));
                        });
                    })
                    ->when(
                        $filter->get('userId') != null,
                        function ($userIdFilter) use ($filter) {
                            return $userIdFilter->whereHas(
                                'user',
                                function ($userFind) use ($filter) {
                                    $userFind->whereIn('id', $filter->get('userId'));
                                }
                            );
                        }
                    )
                    ->when(
                        $filter->get('certificateMasterId') != null,
                        function ($certificateMasterIdFilter) use ($filter) {
                            return $certificateMasterIdFilter->whereIn('certificate_id', $filter->get('certificateMasterId'));
                        }
                    )
                    ->when(
                        $filter->get('statusId') == 0,
                        function ($statusIdFilt) use ($filter, $today) {
                            return $statusIdFilt->where('expires_on', '<', $today);
                        }
                    )
                    ->when(
                        $filter->get('statusId') == 1,
                        function ($statusIdFilt) use ($filter, $today, $daysto) {
                            return $statusIdFilt->whereBetween('expires_on', [$today, $daysto]);
                        }
                    )
                    ->when(
                        $filter->get('statusId') == 2,
                        function ($statusIdFilt) use ($filter, $monthsFrom, $monthsTo) {
                            return $statusIdFilt->whereBetween('expires_on', [$monthsFrom, $monthsTo]);
                        }
                    )
                    ->when(
                        $filter->get('statusId') == 3,
                        function ($statusIdFilt) use ($filter, $years) {
                            return $statusIdFilt->where('expires_on', '>', $years);
                        }
                    )
                    ->when(
                        $filter->get('customerId') != 0,
                        function ($customerIdFilter) use ($filter) {
                            $allocationlist=$this->customerEmployeeAllocationRepository->allocationList($filter->get('customerId'))->pluck('id')->toArray();
                            return $customerIdFilter->whereHas(
                                'user',
                                function ($customerFind) use ($filter, $allocationlist) {
                                    $customerFind->whereIn('id', $allocationlist);
                                }
                            );
                        }
                    )
                    ->get();
                    return $userCertificateData;
            }

    public function getExpiryReportData($filter)
    {
        if ($filter->get('securityClearanceLookUpId') != null && $filter->get('certificateMasterId') == null) {
            $securityClearances = $this->getsecurityClearanceReport($filter);
        } elseif ($filter->get('securityClearanceLookUpId')== null && $filter->get('certificateMasterId') != null) {
            $certificateUser = $this->getUserCertificateReport($filter);
        } else {
            $securityClearances = $this->getsecurityClearanceReport($filter);
            $certificateUser = $this->getUserCertificateReport($filter);
        }

        $datatable_rows = array();

        if (isset($securityClearances)) {
            foreach ($securityClearances as $securityClearance => $data) {
                $each_row['employee_no'] = $data->user->employee->employee_no;
                $each_row['userid'] = $data->user_id;
                $each_row['employee_name'] = $data->user->full_name;
                $each_row['phone'] = $data->user->employee->phone;
                $each_row['email'] = $data->user->email;
                $each_row['security_clearance_or_certificate'] = $data->securityClearanceLookups->security_clearance;
                $each_row['doc_details']=Document::where('user_id', $data->user_id)->where('document_name_id', $data->securityClearanceLookups->id)->where('answer_type','Modules\Admin\Models\SecurityClearanceLookup')->orderBy('created_at','desc')->first();
                $each_row['expiry_date'] = $data->valid_until;
                $each_row['status'] = $data->status;
                $each_row['status_color'] = $data->status_color;
                $each_row['valid_until_text'] = $data->valid_until_text;
                array_push($datatable_rows, $each_row);
            }
        }
       
        if (isset($certificateUser)) {
            foreach ($certificateUser as $key => $value) {
                $each_row['userid'] = $value->user_id;
                $each_row['employee_no'] = $value->user['employee']['employee_no'];
                $each_row['employee_name'] = $value->user['full_name'];
                $each_row['phone'] = $value->user['employee']['phone'];
                $each_row['email'] = $value->user['email'];
                $each_row['security_clearance_or_certificate'] = $value->trashedCertificateMaster->certificate_name;
                $each_row['doc_details']=Document::where('user_id', $value->user_id)->where('document_name_id', $value->trashedCertificateMaster->id)->where('answer_type','Modules\Admin\Models\CertificateMaster')->orderBy('created_at','desc')->first();
                $each_row['expiry_date'] = $value->expires_on;
                $each_row['status'] = $value->status;
                $each_row['status_color'] = $value->status_color;
                $each_row['valid_until_text'] = $value->valid_until_text;
                array_push($datatable_rows, $each_row);
            }
        }
      
       
        return $datatable_rows;
    }

    public function sendMailToUser($mailData)
    {
        foreach ($mailData->input('0.empData') as $key => $value) {
            $model_name = 'Document Expiry Report'; //Model name to understand from which module email was send
            $subject = $mailData->input('0.email_subject'); // Email subject
            $message = $mailData->input('0.email_body'); // Body of email
            $to = $value['email']; // To whom the mail has to be send
            $helper_variables = array(
            '{receiverFullName}' => $value['employee_name'],
            '{loggedInUserEmployeeNumber}' => $value['employee_no'],
            '{expiredUserCertificateName}' => $value['document'],
            '{userCertificateExpiryDate}' => $value['expiry_date'],
            '{userCertificateExpiryDueInDays}' => $value['status']
            );
            $template = EmailNotificationType::where('type', 'document_expiry_report_notification')->get()->first();
            if (!(empty($template))) {
                $email_template = EmailTemplate::where('type_id', $template->id)->first();
                if (!(empty($email_template))) {
                   // $email_template->email_subject = $subject;
                  //  $email_template->email_body = $message;
                    $mail_content = HelperService::replaceText($value['employee_name'], $subject, $message, $helper_variables);
                    $this->mailQueueRepository->storeMail($to, $mail_content['subject'], $mail_content['body'], null);
                }
            }
        }
        return true;
    }

    public function getCustomerUserList()
    {
        $allocated_customer_users=[];
        $allocated_customer= $this->customerEmployeeAllocationRepository->getAllocatedCustomerId([\Auth::user()->id]);
            if (!empty($allocated_customer)) {
                $allocated_customer_users=$this->customerEmployeeAllocationRepository->allocationList($allocated_customer)->pluck('id')->toArray();
            }
            return   $allocated_customer_users;
    }
}
