<?php

namespace Modules\Vehicle\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class VehicleVendorLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('vehicle_vendor_lookups')->delete();

        \DB::table('vehicle_vendor_lookups')->insert(array (
            0 =>
            array (
                'id' => 1,
                'vehicle_vendor' => 'Canadian Tire',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'vehicle_vendor' => 'MechaniQ',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'vehicle_vendor' => 'Midas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
        ));

    }
}
