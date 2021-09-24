<?php

namespace Modules\Recruitment\Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class RecruitmentModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         Module::create(['name' => 'Recruitment']);
    }
}
