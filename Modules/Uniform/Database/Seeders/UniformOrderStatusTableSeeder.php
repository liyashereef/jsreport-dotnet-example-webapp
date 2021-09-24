<?php

namespace Modules\Uniform\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UniformOrderStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('uniform_order_status')->delete();

        DB::table('uniform_order_status')->insert([
            [
                'id' => 1,
                'display_name' => 'Order Received ',
                'machine_code' => 'order-received',
                'immutable' => 0,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'display_name' => 'Shipped',
                'machine_code' => 'product-shipped',
                'immutable' => 0,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'display_name' => 'Delivered',
                'machine_code' => 'product-delivered',
                'immutable' => 0,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'display_name' => 'Cancelled',
                'machine_code' => 'product-cancelled',
                'immutable' => 0,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'display_name' => 'Returned',
                'machine_code' => 'product-returned',
                'immutable' => 0,
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}
