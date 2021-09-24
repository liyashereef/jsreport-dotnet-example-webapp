<?php

namespace Modules\Hranalytics\Repositories;

use Modules\Hranalytics\Models\CustomerStcDetail;

class CustomerStcDetailsRepository
{

    protected $customerStcModel;

    /**
     * Create a new CustomerStcDetail instance.
     *
     * @param  \App\Models\CustomerStcDetail $CustomerStcModel
     */
    public function __construct(CustomerStcDetail $customerStcModel)
    {
        $this->customerStcModel = $customerStcModel;
    }

    /**
     * Get Customer Stc Details
     *
     * @param $id
     * @return object
     */
    public function getSingleCustomerStcDetails($id)
    {
        $singleRecord = $this->customerStcModel->where('customer_id', $id)->first();
        return $singleRecord;
    }

    /**
     *
     * @param type $filter
     * @return type
     */
    public function getCustomerStcDetails()
    {
        return $this->customerStcModel->get();

    }

    /**
     * Function to store stc details.
     *
     * @param  $request, $last_inserted_id
     * @return object
     */
    public function storeStcDetails($request, $last_inserted_id)
    {
        if (!is_array($request)) {
            $customerStcDetailId = $request->get('customer_stc_details_id');
            $data = $request->all();
        } else {
            $customerStcDetailId = $request['customer_stc_details_id'];
            $data = $request;
        }
        $customerStcDetailId = is_array($request) ? $request['customer_stc_details_id'] : $request->get('customer_stc_details_id');
        return $this->customerStcModel->updateOrCreate(array('id' => $customerStcDetailId, 'customer_id' => $last_inserted_id), $data);

    }
    /**
     * Remove the specified customer from storage.
     *
     * @param  $id
     * @return object
     */
    public function destroyCustomerStcDetails($id)
    {
        return $this->customerStcModel->where('customer_id', $id)->delete();
    }

}
