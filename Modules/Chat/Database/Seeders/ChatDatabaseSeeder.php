<?php

namespace Modules\Chat\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ChatDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(ChatModuleTableSeeder::class);
        $this->call(RoleAndPermissionsChatTableSeeder::class);
        $this->call(ModulePermissionChatTableSeeder::class);
        // $this->call("OthersTableSeeder");
    }
}
