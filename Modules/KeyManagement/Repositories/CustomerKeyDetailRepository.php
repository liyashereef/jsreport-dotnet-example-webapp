<?php

namespace Modules\KeyManagement\Repositories;

use App\Repositories\AttachmentRepository;
use App\Services\HelperService;
use Auth;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Models\Customer;
use Modules\KeyManagement\Models\CustomerKeyDetail;
use Modules\KeyManagement\Models\KeyLogDetail;
use Modules\Timetracker\Repositories\ImageRepository;

class CustomerKeyDetailRepository
{

    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $helperService, $attachmentRepository, $customerRepository, $keyLogModel;

    /**
     * Create a new Model instance.
     *
     * @param  \Modules\KeyManagement\Models\CustomerKeyDetail $CustomerkeyModel
     */
    public function __construct(
        CustomerKeyDetail $CustomerkeyModel,
        AttachmentRepository $attachmentRepository,
        HelperService $helperService,
        CustomerRepository $customerRepository,
        KeyLogDetail $keyLogModel,
        ImageRepository $imageRepository,
        Customer $customerModel
    ) {
        $this->helperService = $helperService;
        $this->model = $CustomerkeyModel;
        $this->attachmentRepository = $attachmentRepository;
        $this->customerRepository = $customerRepository;
        $this->keylogmodel = $keyLogModel;
        $this->imageRepository = $imageRepository;
        $this->customerModel = $customerModel;
    }

    /**
     * Get  Customer Key Details list
     *
     * @param empty
     * @return array
     */
    public function getAll($id)
    {
        $result = $this->model->where('customer_id', $id)
            ->with(['customer', 'attachment', 'info', 'info.checkedoutUser', 'info.checkedinUser'])
            ->orderBy('created_at', 'desc')
            ->get();
        return $this->prepareDataForKeyDetailList($result);
    }

    public function clienLookUps(){
        $customerList = array();
        $user = Auth::user();
        if ((\Auth::user()->can('view_all_keylog_summary')) || $user->hasAnyPermission(['admin', 'super_admin'])) {
            $customerList = $this->customerModel->orderBy('client_name', 'asc')->get();
        } else {
            $customerIds = $this->customerRepository->getAllAllocatedCustomerId([Auth::user()->id]);
            $customerList = $this->customerModel
                ->whereIn('id', $customerIds)
                ->orderBy('client_name', 'asc')->get();
        }
        return $customerList;
    }

    /**
     * Prepare datatable elements as array.
     * @param  $result
     * @return array
     */
    public function prepareDataForKeyDetailList($result)
    {
        $datatable_rows = array();
        foreach ($result as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["key_id"] = isset($each_list->key_id) ? $each_list->key_id : "--";
            $each_row["key_name"] = isset($each_list->room_name) ? $each_list->room_name : "--";
            $each_row["key_availability"] = ($each_list->key_availability == 1) ? "Checked In" : "Checked Out";
            $each_row["checked_out_to"] = isset($each_list->info) ? $each_list->info->checked_out_to : '--';
            $each_row["key_checked_company_name"] = isset($each_list->info) ? $each_list->info->company_name : '--';
            //updated_by - user details --start
            $each_row["checked_in_by"] = isset($each_list->info->checkedinUser) ? $each_list->info->checkedinUser->first_name . ' ' . $each_list->info->checkedinUser->last_name : '--';
            //updated_by - user details --start
            $each_row["attachment_id"] = isset($each_list->attachment_id) ? $each_list->attachment_id : "";
            $each_row["active"] = isset($each_list->active) ? $each_list->active : "--";
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     * Display details of single customer key detail resource
     *
     * @param $id
     * @return json
     */
    public function get($id)
    {
        $data = $this->model->with(['customer', 'attachment'])->find($id);
        return $data;
    }

    /**
     * Store a newly created key Request Type in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($request)
    {
        $logged_in_user = \Auth::id();
        $data = ['customer_id' => $request->id, 'key_id' => $request->key_id, 'room_name' => $request->room_name];
        $keyStore = $this->model->updateOrCreate(array('id' => $request->get('id')), $request->all());
        $keyId = $keyStore->id;
        $attachments = $request->key_image;
        if (!empty($attachments)) {
            $file = $this->attachmentRepository->saveAttachmentFile('keymanagement-key-image', $request, 'key_image');
            $attachment_id = $file['file_id'];
            $file_path = implode('/', $this->getAttachmentPathArr($request));
            $data = ['attachment_id' => $attachment_id, 'created_by' => $logged_in_user, 'key_image_path' => $file_path];
            CustomerKeyDetail::updateOrCreate(array('id' => $keyId), $data);
        }
        return $keyStore;
    }

    /**
     * Remove the specified cutomer key from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */
    public static function getAttachmentPathArr($request)
    {
        if ($request->customer_id) {
            return array(config('globals.keymanagement'), 'keys/' . $request->customer_id);
        }
    }

    /**
     * Function to Download attachments
     * @param $file_id
     * @return array
     */
    public static function getAttachmentPathArrFromFile($file_id)
    {

        $attachment = CustomerKeyDetail::where('attachment_id', $file_id)->first();
        if (!empty($attachment)) {
            $path = $attachment->key_image_path;
        }
        return array($path);
    }

    /**
     * Display details of  customer
     *
     * @param $id
     * @return json
     */
    public function getCustomerAll($client_id=null)
    {

        $customer_type = PERMANENT_CUSTOMER;
        $customer_status = ACTIVE;
        if ((\Auth::user()->can('view_all_customers_keys')) || \Auth::user()->hasAnyPermission(['admin', 'super_admin'])) {
            $customer_list = $this->customerRepository->getCustomerList($customer_type, $customer_status);
        } else {
            $customer_ids = $this->customerRepository->getAllAllocatedCustomerId([\Auth::user()->id]);
            $customer_list = $this->customerRepository->getCustomerList(true, true, $customer_ids);
        }
        $customer_list = $customer_list->when($client_id!=null, function ($q) use ($client_id) {
            return $q->where('id', $client_id);
        });
        return $customer_list;
    }

    public function getKeyLogSummaryByCustomers($customerIds)
    {

        $qry = $this->model->with('log');
        if (!empty($customerIds)) {
            $qry->whereIn('customer_id', $customerIds);
        }
        $customerKeys = $qry->get();
        $result = [];
        $iconHtml = '<i class="fa fa-download fa-sm" aria-hidden="true"></i>';
        if (!empty($customerKeys)) {
            foreach ($customerKeys as $customerKey) {
                $logs = $customerKey->log;
                if (!empty($logs)) {
                    foreach ($logs as $key => $log) {
                        $identificationArr = [];
                        $checkOutSignatureAttchmentArr = [];
                        $checkInSignatureAttchmentArr = [];

                        if (!empty($log->identifications)) {
                            $identificationArr['_value'] = $iconHtml;
                            $identificationArr['_color'] = "";
                            $identificationArr['_bg_color'] = "";
                            $identificationArr['_title'] = "Identification Attachment";

                            $arrayData = false;
                            foreach ($log->identifications as $ky => $identification) {
                                $link = "file/show/" . $identification->identification_attachment_id . "/keymanagement-identification";
                                $identificationArr['_href'][$ky] = $link;
                                $arrayData = true;
                            }

                            if (!$arrayData) {
                                $identificationArr['_value'] = "-";
                                $identificationArr['_href'] = "";
                            }
                        }

                        $checkOutSignatureAttchmentArr['_value'] = "-";
                        $checkOutSignatureAttchmentArr['_color'] = "";
                        $checkOutSignatureAttchmentArr['_bg_color'] = "";
                        $checkOutSignatureAttchmentArr['_href'] = "";
                        $checkOutSignatureAttchmentArr['_title'] = "Signature Attachment";
                        if (!empty($log->signature_attachment_id)) {
                            $checkOutSignatureAttchmentArr['_value'] = $iconHtml;
                            $checkOutSignatureAttchmentArr['_href'] = "file/show/" . $log->signature_attachment_id . "/keymanagement-signature";
                        }

                        $checkInSignatureAttchmentArr['_value'] = "-";
                        $checkInSignatureAttchmentArr['_color'] = "";
                        $checkInSignatureAttchmentArr['_bg_color'] = "";
                        $checkInSignatureAttchmentArr['_title'] = "Signature Attachment";
                        $checkInSignatureAttchmentArr['_href'] = "";
                        if (!empty($log->check_in_signature_attachment_id)) {
                            $checkInSignatureAttchmentArr['_value'] = $iconHtml;
                            $checkInSignatureAttchmentArr['_href'] = "file/show/" . $log->check_in_signature_attachment_id . "/keymanagement-signature";
                        }

                        $result[] = [
                            "key_name" => $customerKey->room_name . ($customerKey->key_id ? " (" . $customerKey->key_id . ")" : ""),
                            "project_name" => $customerKey->customer ? $customerKey->customer->client_name . ($customerKey->customer ? " (" . $customerKey->customer->project_number . ")" : '') : '',
                            "identification_attachment_id" => !empty($identificationArr) ? $identificationArr : "",
                            "check_out_date" => $log->checked_out_date_time ? '<span class="hidden_date_span">' . $log->checked_out_date_time . '</span>' . $log->checked_out_date_time->toFormattedDateString() : "--",
                            "check_out_time" => $log->checked_out_date_time ? '<span class="hidden_date_span">' . $log->checked_out_date_time . '</span>' . $log->checked_out_date_time->format('H : i A') : "--",
                            "check_out_by" => $log->checkedoutuser ? $log->checkedoutuser->name_with_emp_no : "--",
                            "check_out_to" => $log->checked_out_to ? $log->checked_out_to : "--",
                            "signature_attachment_id" => !empty($checkOutSignatureAttchmentArr) ? $checkOutSignatureAttchmentArr : "",
                            "check_in_date" => $log->checked_in_date_time ? '<span class="hidden_date_span">' . $log->checked_in_date_time . '</span>' . $log->checked_in_date_time->toFormattedDateString() : "--",
                            "check_in_time" => $log->checked_in_date_time ? '<span class="hidden_date_span">' . $log->checked_in_date_time . '</span>' . $log->checked_in_date_time->format('H : i A') : "--",
                            "check_in_by" => $log->checkedinuser ? $log->checkedinuser->name_with_emp_no : "--",
                            "check_in_from" => $log->checked_in_from ? $log->checked_in_from : "--",
                            "check_in_signature_attachment_id" => !empty($checkInSignatureAttchmentArr) ? $checkInSignatureAttchmentArr : "",
                        ];
                    }
                }
            }
        }
        return $result;
    }

}
