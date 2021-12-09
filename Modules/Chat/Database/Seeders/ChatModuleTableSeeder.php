<?php

namespace Modules\Chat\Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ChatModuleTableSeeder extends Seeder
{
    public function run()
    {
        // create Module
        Module::create(['name' => 'Chat']);

    }
}
