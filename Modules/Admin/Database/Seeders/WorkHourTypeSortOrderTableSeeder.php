<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Timetracker\Models\EmployeeShiftWorkHourType;

class WorkHourTypeSortOrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $workHourTypes = EmployeeShiftWorkHourType::orderBy("name", "asc")->get();
        foreach ($workHourTypes as $key => $workHourType) {
            $order = ($key + 1);
            EmployeeShiftWorkHourType::find($workHourType->id)->update(
                ["sort_order" => $order]
            );
        }
        // $this->call("OthersTableSeeder");
    }
}
