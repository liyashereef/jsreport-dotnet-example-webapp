<?php
  
 namespace Modules\ProjectManagement\Database\Seeders;
 
use App\Models\Module;
use Illuminate\Database\Seeder;

class ProjectManagementModuleSeeder extends Seeder
{
    public function run()
    {
        // create Module
        Module::create(['name' => 'Project Management']);

    }
}
