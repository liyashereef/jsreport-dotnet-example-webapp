<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ContractsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

	$this->call(SubmissionReasonTableSeeder::class);
	$this->call(BusinessSegmentTableSeeder::class);
	$this->call(LineOfBusinessTableSeeder::class);
        $this->call(BillingRateChangesTableSeeder::class);
        $this->call(BillingCyclesTableSeeder::class);        
        $this->call(PaymentMethodTableSeeder::class);        
        $this->call(DeviceAccesTableSeeder::class);
        $this->call(OfficeAddressesTableSeeder::class);
        $this->call(CellPhoneProviderTableSeeder::class);
        $this->call(HolidayPaymentAllocationTableSeeder::class);        



    }
}
