<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CpidCustomerAllocations;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CpidLookup;

class CpidCustomerAllocationRepository
{
    protected $model;
    protected $customer;
    protected $cpidLookups;

    public function __construct(CpidCustomerAllocations $cpidCustomerAllocations,
    Customer $customer,CpidLookup $cpidLookup)
    {
        $this->cpidLookups = $cpidLookup;
        $this->model = $cpidCustomerAllocations;
        $this->customer = $customer;
    }

    /**
     * Get all cpids of a customer.
     */
    public function getByCustomerId($customerId)
    {
        return $this->model->where('customer_id',$customerId)
        ->get()
        ->load(['customer','cpid_lookup','cpid_lookup.effectiveDate']);
    }

        /**
     * Get all cpids of a customer.
     */
    public function getByCpid($cpid)
    {
        return $this->model->where('cpid',$cpid)
        ->get()
        ->load(['customer','cpid_lookup']);
    }

    /**
     * Get all cpids of a customer who has active effective date.
     */
    public function getByCustomerIdWithActive($customerId)
    {
        return $this->model->where('customer_id',$customerId)
        ->whereHas('cpid_lookup.effectiveDate')
        ->with(['cpid_lookup.cpidFunction', 'customer', 'cpid_lookup'])
        ->get();
    }

     /**
     * Get all cpids of a customer who has active effective date.
     */
    public function getByCustomerIdCpid($customerId,$cpid)
    {
        return $this->model->where('customer_id',$customerId)
            ->where('cpid',$cpid)
            ->whereHas('cpid_lookup.effectiveDate')
            ->with(['cpid_lookup.cpidFunction', 'customer', 'cpid_lookup'])
            ->get();
    }

    
    function check_cpid_allocated_or_not($cpid)
    {
        return $this->model->where('cpid',$cpid)->get();
    } 

}
