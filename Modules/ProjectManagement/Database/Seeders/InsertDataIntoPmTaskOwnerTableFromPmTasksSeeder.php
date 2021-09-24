<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InsertDataIntoPmTaskOwnerTableFromPmTasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pmTaskObjects = \DB::table('pm_tasks')->orderBy('id', 'ASC')->get();
        if (!empty($pmTaskObjects)) {
            foreach ($pmTaskObjects as $pmTaskObject) {
                $taskOwnerArray = [];
                $taskOwnerArray['task_id'] = $pmTaskObject->id;
                $taskOwnerArray['user_id'] = $pmTaskObject->assigned_to;
                $taskOwnerArray['type'] = 0;
                $taskOwnerArray['created_at'] = Carbon::now();
                $taskOwnerArray['updated_at'] = Carbon::now();
                $taskOwnerArray['deleted_at'] = $pmTaskObject->deleted_at;

                $taskOwnerArr[] = $taskOwnerArray;
            }

            \DB::table('pm_task_owners')->insert($taskOwnerArr);
        }
    }
}
