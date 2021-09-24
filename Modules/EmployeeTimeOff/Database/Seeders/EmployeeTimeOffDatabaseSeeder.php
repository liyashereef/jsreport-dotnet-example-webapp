<?php

namespace Modules\EmployeeTimeOff\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmployeeTimeOffDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

         $this->call(EmployeeTimeoffWorkflowTableSeeder::class);
    }
}
