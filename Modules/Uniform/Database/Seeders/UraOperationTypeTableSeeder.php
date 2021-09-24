<?php

namespace Modules\Uniform\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UraOperationTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('ura_operation_types')->delete();

        DB::table('ura_operation_types')->insert([
            [
                'id' => 1,
                'display_name' => 'URA Correction',
                'machine_name' => 'ura_correction',
                'restricted' => false,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'display_name' => 'Timesheet Earnings',
                'machine_name' => 'timesheet_earnings',
                'restricted' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'display_name' => 'Timesheet Earnings Revert',
                'machine_name' => 'timesheet_earnings_revert',
                'restricted' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'display_name' => 'Uniform Purchase',
                'machine_name' => 'uniform_purchase',
                'restricted' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'display_name' => 'Uniform Purchase Cancel',
                'machine_name' => 'uniform_purchase_cancel',
                'restricted' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'display_name' => 'Uniform Purchase Return',
                'machine_name' => 'uniform_purchase_return',
                'restricted' => true,
                'created_at' => Carbon::now(),
            ]
        ]);
    }
}
