<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Repositories\CustomerIncidentSubjectAllocationRepository;

class CustomerIncidentSubjectUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incident:customerincidentsubjectupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Update Customer Incident Subject';

    public function __construct(CustomerIncidentSubjectAllocationRepository $customerIncidentSubjectAllocationRepository)
    {
        parent::__construct();
        $this->customerIncidentSubjectAllocationRepository = $customerIncidentSubjectAllocationRepository;

        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->customerIncidentSubjectAllocationRepository->updateCustomerIncidentSubject();
        Log::info('Update Customer Incident Subject'.$response);
    }
}
