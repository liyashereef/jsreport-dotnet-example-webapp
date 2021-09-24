<?php

namespace Modules\Vehicle\Database\Seeders;

use Illuminate\Database\Seeder;

class VehicleMaintenanceDatatype extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('vehicle_maintenance_datatypes')->insert([
           0 =>
            [
            'id' => 1,
            'name' => 'Kilometer',
            'shortname' => 'km',
            'short_description' => 'Enter kilometers',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
            'deleted_at' => NULL,
        ],
        1 =>
        [
            'id' => 2,
            'name' => 'Date',
            'shortname' => 'date',
            'short_description' => 'Enter date',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
            'deleted_at' => NULL,
        ]
    ]);
    }
}
