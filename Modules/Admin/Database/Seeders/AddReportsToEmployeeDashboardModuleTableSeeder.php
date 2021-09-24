<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Models\EmployeeComplianceReports;

class AddReportsToEmployeeDashboardModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmployeeComplianceReports::truncate();
        $reportarray = [
            [
                "id" => 1,
                "report_name" => "training_compliance",
                "display_name" => "Training Compliance",
                "active" => true
            ],
            [
                "id" => 2,
                "report_name" => "schedule_compliance",
                "display_name" => "Schedule Compliance",
                "active" => true
            ],
            [
                "id" => 3,
                "report_name" => "performance_reviews",
                "display_name" => "Performance Reviews",
                "active" => true
            ],
            [
                "id" => 4,
                "report_name" => "timeoff_summmary",
                "display_name" => "Timeoff Summary",
                "active" => 0
            ],
            [
                "id" => 5,
                "report_name" => "spares_compliance",
                "display_name" => "Spares Compliance",
                "active" => true
            ],
            [
                "id" => 6,
                "report_name" => "license_compliance",
                "display_name" => "License Compliance",
                "active" => true
            ],
            [
                "id" => 7,
                "report_name" => "clearences",
                "display_name" => "Clearances",
                "active" => true
            ]
        ];
        EmployeeComplianceReports::insert($reportarray);
    }
}
