<?php

namespace Modules\Reports\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ReportsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesAndPermissionRecruitingAnalyticsReportTableSeeder::class);
        $this->call(ModulePermissionRecruitingAnalyticsReportTableSeeder::class);
    }
}
