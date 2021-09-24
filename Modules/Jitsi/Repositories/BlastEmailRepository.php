<?php

namespace Modules\Jitsi\Repositories;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use Modules\Admin\Models\Customer;
use Auth;
use App\Models\MailQueue;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Models\CustomerEmployeeAllocation;

class BlastEmailRepository extends Controller
{
    protected $customerEmployeeAllocationRepository, $customer_repository;

    public function __construct(
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        CustomerRepository $customerRepository
    ) {
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->customer_repository = $customerRepository;
    }

    function changeFormat($str)
    {

        if (strlen($str) == 3) {
            return strtoupper($str);
        } else {
            $returnName = "";
            $frags = explode("_", $str);
            for ($i = 0; $i < count($frags); $i++) {
                if ($returnName == "") {
                    $returnName = ucfirst($frags[$i]);
                } else {
                    $returnName = $returnName . " " . ucfirst($frags[$i]);
                }
            }

            return $returnName;
        }
    }
    public function getCustomerList($status = ACTIVE, $areamanager = [], $supervisor = [])
    {
        $customers = [];
        $customerarray = [];
        $perm = 1;
        if (\Auth::user()->can('create_blastcom_all_customers') || \Auth::user()->hasAnyPermission('admin', 'super_admin')) {
            $perm = 1;
            $customers = Customer::with('employeeCustomerAreaManager')
                ->where('active', $status)->when($areamanager, function ($q) use ($areamanager) {
                    $q->whereHas('employeeCustomerAreaManager', function ($query) use ($areamanager) {
                        return $query->whereIn('user_id', $areamanager);
                    });
                })
                ->when($supervisor, function ($q) use ($supervisor) {
                    $q->whereHas('employeeCustomerSupervisor', function ($query) use ($supervisor) {
                        return $query->whereIn('user_id', $supervisor);
                    });
                })->get();
        } else {
            $perm = 2;
            $customers = CustomerEmployeeAllocation::where(['user_id' => \Auth::user()->id])
                ->when($areamanager, function ($q) use ($areamanager) {
                    $q->whereHas('customer.employeeCustomerAreaManager', function ($query) use ($areamanager) {
                        return $query->whereIn('user_id', $areamanager);
                    });
                })
                ->when($supervisor, function ($q) use ($supervisor) {
                    $q->whereHas('customer.employeeCustomerSupervisor', function ($query) use ($supervisor) {
                        return $query->whereIn('user_id', $supervisor);
                    });
                })
                ->get();
        }

        $i = 0;
        foreach ($customers as $customer) {

            if ($perm == 1) {
                if ($customer->active == 1) {
                    $customerarray[$i]["id"] = $customer->id;
                    $customerarray[$i]["project_number"] = $customer->project_number;
                    $customerarray[$i]["client_name"] = $customer->client_name;
                    try {
                        $managername = "";
                        foreach ($customer->employeeCustomerAreaManager as $aremanagersarray) {
                            if ($managername == "") {
                                $managername .= $aremanagersarray->trashedUser->getFullNameAttribute();
                            } else {
                                $managername .= " , " . $aremanagersarray->trashedUser->getFullNameAttribute();
                            }
                        }

                        $full_name = $managername;
                        $customerarray[$i]["areamanager"] = $full_name;
                    } catch (\Throwable $th) {
                        $customerarray[$i]["areamanager"] = "";
                    }

                    try {
                        $supervisorname = "";
                        foreach ($customer->employeeCustomerSupervisor as $supervisorarray) {
                            if ($supervisorname == "") {
                                $supervisorname .= $supervisorarray->trashedUser->getFullNameAttribute();
                            } else {
                                $supervisorname .= " , " . $supervisorarray->trashedUser->getFullNameAttribute();
                            }
                        }
                        $customerarray[$i]["supervisor"] = $supervisorname;
                    } catch (\Throwable $th) {
                        $customerarray[$i]["supervisor"] = "";
                    }
                }
            } else if ($perm == 2) {
                if ($customer->customer->active == 1) {
                    $customerarray[$i]["id"] = $customer->customer->id;
                    $customerarray[$i]["project_number"] = $customer->customer->project_number;
                    $customerarray[$i]["client_name"] = $customer->customer->client_name;

                    try {
                        $customerarray[$i]["areamanager"] = $customer->customer->employeeLatestCustomerAreaManager->trashedUser->getFullNameAttribute();
                    } catch (\Throwable $th) {
                        $customerarray[$i]["areamanager"] = "";
                    }

                    try {
                        $customerarray[$i]["supervisor"] = $customer->customer->employeeLatestCustomerSupervisor->trashedUser->getFullNameAttribute();
                    } catch (\Throwable $th) {
                        $customerarray[$i]["supervisor"] = "";
                    }
                }
            }
            $i++;
        }
        return ($customerarray);
    }

    public function getUserCustomerAllocation($user_model)
    {
        $allocatedCustomers = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
    }
    public function getAllocatedCustomers($user_model)
    {
        // $customer_arr_perm = $this->getAllocatedPermanentCustomers($user_model);
        // $customer_arr_stc = $this->getAllocatedStcCustomers($user_model);
        // return array_unique(array_merge($customer_arr_perm, $customer_arr_stc));
        if (\Auth::user()->hasAnyPermission(["super_admin", "create_blastcom_all_customers"])) {
            $customers = Customer::where("active", 1)->pluck("id")->toArray();
        } else {
            $customers = CustomerEmployeeAllocation::where("user_id", $user_model->id)->pluck("customer_id")->toArray();
        }
        return $customers;
    }

    public function getAllocatedPermanentCustomers($user_model, $is_shift_enabled = null)
    {
        $customers_list = array();
        if ($user_model->hasAnyPermission(['create_blastcom_all_customers', 'admin', 'super_admin'])) {
            $customers_list = array_keys($this->customer_repository->getList(PERMANENT_CUSTOMER, $is_shift_enabled));
        } else {
            /**
             * Customer list -- direct & assigned employee alocated customer list.
             * $allocated_user = $this->employeeAllocationRepository->getEmployeeAssigned([$user_model->id]);
             * $customers_list = $this->getAllocatedCustomerId(array_merge([$user_model->id], $allocated_user->pluck('user_id')->toArray()), false);
             */
            $customers_list = $this->getAllocatedCustomerId([$user_model->id], false);
        }
        return $customers_list;
    }
    public function getAllocatedStcCustomers($user_model, $is_shift_enabled = null)
    {
        $customers_list = array();
        $user = Auth::user();
        if (auth()->user()->hasAnyPermission(['create_blastcom_all_customers', 'super_admin', 'admin'])) {
            $customers_list = array_keys($this->customer_repository->getList(STC_CUSTOMER, $is_shift_enabled));
        } else {
            /**
             * Customer list -- direct & assigned employee alocated customer list.
             *$allocated_user = $this->employeeAllocationRepository->getEmployeeAssigned([$user_model->id]);
             *$customers_list = $this->getAllocatedCustomerId(array_merge([$user_model->id], $allocated_user->pluck('user_id')->toArray()), true);
             */
            $customers_list = $this->getAllocatedCustomerId([$user_model->id], true);
        }
        return $customers_list;
    }

    public function SendQueueMail(
        $mailDetails,
        $mailSubject,
        $mailMessage
    ) {
        $from = null;
        $to = $mailDetails["email"];
        $cc = null;
        $bcc = null;
        $subject = $mailSubject;
        $message = $mailMessage;
        $mail_time = null;
        $created_by = null;
        $attachment_id = null;
        $model_name = "Modules\Jitsi\Models\EmailBlastLog";
        $mailQueue = new MailQueue;
        $mailQueue->from = ($from != null) ? $from : \Config::get('mail.from.address');
        $mailQueue->to = $to;
        $mailQueue->cc = ($cc) ? $cc : '';
        $mailQueue->bcc = ($bcc) ? $bcc : '';
        $mailQueue->subject = $subject;
        $mailQueue->message = $message;
        $mailQueue->mail_time = ($mail_time != null) ? $mail_time : \Carbon::now();
        $mailQueue->created_by = ($created_by != null) ? $created_by : \Auth::id();
        // $mailQueue->attachment_id = $attachment_id;
        $mailQueue->attachment_id = $attachment_id ? $attachment_id : NULL;
        $mailQueue->s3_bucket_name =  NULL;
        $mailQueue->s3_repo_filename =  NULL;
        $mailQueue->model_name = $model_name ? $model_name : NULL;
        $mailQueue->save();
    }
}
