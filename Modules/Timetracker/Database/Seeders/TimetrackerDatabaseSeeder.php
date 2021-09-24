<?php

namespace Modules\Timetracker\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class TimetrackerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(EmployeeShiftWorkHourTypeTableSeeder::class);
        $this->call(SeedDispatchCoordinatesIdleSettingsTableSeeder::class);
        $this->call(SeedDispatchRequestStatusTableSeeder::class);
        $this->call(SeedPushNotificationTypeTableSeeder::class);

        // $this->call(RoleAndPermissionTimeTrackerMSTSeed::class);
        // $this->call(ModulePermissionsTimeTrackerMSTSeeder::class);
        $this->call(ShiftTypeTableSeederTableSeeder::class);

        /* Employee Live Location Seeder */
       // $this->call(RoleAndPermissionEmployeeLiveLocationSeeder::class);
       // $this->call(ModulePermissionsEmployeeLiveLocationSeeder::class);
        /* Employee Live Location Seeder */

        /* Employee MyAvailability Seeder */
        $this->call(RoleAndPermissionAddMyavailabilitySeeder::class);
        $this->call(ModulePermissionsAddMyavailabilitySeeder::class);
        /* Employee MyAvailability Seeder */
    }
}
