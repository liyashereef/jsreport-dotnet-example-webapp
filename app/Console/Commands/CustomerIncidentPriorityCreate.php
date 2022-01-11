<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Repositories\CustomerIncidentSubjectAllocationRepository;

class CustomerIncidentPriorityCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incident:customerincidentprioritycreate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Customer Incident Priority';

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
        $response = $this->customerIncidentSubjectAllocationRepository->updateCustomerIncidentPriority();
        Log::info('Create Customer Incident Priority'.$response);
    }
}
