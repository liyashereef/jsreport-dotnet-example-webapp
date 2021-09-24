<?php

namespace Modules\Uniform\Database\Seeders;

use App\Services\SeederService;
use Illuminate\Database\Seeder;

class UniformDeleteRateAppliedPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deletePermissionArr = [
            'edit_rate_applied',
            'view_uniform_orders',
            'change_uniform_order_status'
        ];
        SeederService::deletePermission($deletePermissionArr);
    }
}
