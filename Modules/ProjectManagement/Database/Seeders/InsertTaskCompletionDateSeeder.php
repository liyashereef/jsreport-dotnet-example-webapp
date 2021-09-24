<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\ProjectManagement\Models\PmTask;

class InsertTaskCompletionDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $filterTask = function ($query) {
            $query->where('percentage', 100);
        };
        $pmTaskObjects = PmTask::whereHas('status', $filterTask)->with(['status' => $filterTask])->get();
        if (!empty($pmTaskObjects)) {
            foreach ($pmTaskObjects as $pmTaskObject) {
                if ($pmTaskObject->status[0]->percentage==100) {
                    PmTask::where('id', $pmTaskObject->id)->update(array('completed_date' => $pmTaskObject->status[0]->status_date));
                }
            }
        }
    }
}
