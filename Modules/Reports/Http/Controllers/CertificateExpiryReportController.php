<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Reports\Repositories\CertificateExpiryRepository;
use Modules\Admin\Models\SecurityClearanceLookup;
use Modules\Admin\Models\CertificateMaster;
use Modules\Admin\Models\User;
use Modules\Admin\Models\EmailNotificationType;
use Modules\Admin\Repositories\ClientEmailTemplateRepository;
use Modules\Admin\Repositories\CustomerRepository;

class CertificateExpiryReportController extends Controller
{
    protected $certificateExpiryRepository, $clientEmailTemplateRepository;

    public function __construct(CertificateExpiryRepository $certificateExpiryRepository, ClientEmailTemplateRepository $clientEmailTemplateRepository, CustomerRepository $customerRepository)
    {
        $this->certificateExpiryRepository = $certificateExpiryRepository;
        $this->clientEmailTemplateRepository = $clientEmailTemplateRepository;
        $this->customerRepository=$customerRepository;
    }

    public function certificateExpiryReport()
    {
        $securityClearance = SecurityClearanceLookup::select('id', 'security_clearance')
        ->orderBy('security_clearance', 'ASC')->get();
        $certificateMaster = CertificateMaster::select('id', 'certificate_name')
        ->orderBy('certificate_name', 'ASC')->get();
        if (\Auth::user()->hasPermissionTo('view_all_site_document_report')) {
            $customer =$this->customerRepository->getProjectsDropdownList('all');
            $employeeName = User::where('active', 1)
            ->orderBy('first_name', 'ASC')
            ->get();
            $employeeAll = User::orderBy('first_name', 'ASC')
            ->get();
        } else {
             $customer =$this->customerRepository->getProjectsDropdownList('allocated');
             $allocated_customer_users= $this->certificateExpiryRepository->getCustomerUserList();
              $employeeName = User::where('active', 1)->whereIn('id', $allocated_customer_users)
            ->orderBy('first_name', 'ASC')
            ->get();
            $employeeAll = User::whereIn('id', $allocated_customer_users)->orderBy('first_name', 'ASC')
            ->get();
        }
      
    
        return view('reports::certificateexpiryreport.certificateexpiryreport', compact('securityClearance', 'certificateMaster', 'employeeName', 'employeeAll', 'customer'));
    }

    public function getcertificateexpiryreport(Request $request)
    {
        $certificateExpiryReportData = $this->certificateExpiryRepository->getExpiryReportData($request);
        return datatables()->of($certificateExpiryReportData)->toJson();
    }

    public function getSingle()
    {
        $notificationType = EmailNotificationType::where('type', 'document_expiry_report_notification')->first();
        return response()->json($this->clientEmailTemplateRepository->get($notificationType->id));
    }

    public function sendExpiryMail(Request $request)
    {
        $resp = $this->certificateExpiryRepository->sendMailToUser($request);
        return response()->json(['status'=>$resp]);
    }
}
