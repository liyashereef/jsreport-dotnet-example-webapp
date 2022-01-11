<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\FeverScan\Http\Controllers\FeverScanController;
use Illuminate\Support\Facades\Log;

class SetFeverScanModuleForCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feverscan:setFeverScanModuleForCustomers {customer_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To add Fever Scan in Shift Modules';

    public function __construct(FeverScanController $feverScanController)
    {
        parent::__construct();
        $this->feverScanController = $feverScanController;

        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $customer = false;
        $customer_id =$this->argument('customer_id');
        if(isset($customer_id)){
            $customer = $customer_id;
        }
        $response = $this->feverScanController->setFeverScanModule($customer);
     
    }
}
