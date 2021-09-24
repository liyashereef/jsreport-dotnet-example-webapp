<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ClientFeedbackLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        \DB::table('client_feedback_lookups')->delete();
        \DB::table('client_feedback_lookups')->insert([

            0 => [
                'id' => 1,
                'feedback' => 'General',
                'short_name' => 'General',
                'is_editable' => 1,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            1 => [
                'id' => 2,
                'feedback' => "Employee Specific",
                'short_name' => 'Employee Specific',
                'is_editable' => 0,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            2 => [
                'id' => 3,
                'feedback' => "Service Delivery",
                'short_name' => 'Service Delivery',
                'is_editable' => 1,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            3 => [
                'id' => 4,
                'feedback' => "Technology",
                'short_name' => 'Technology',
                'is_editable' => 1,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            4 => [
                'id' => 5,
                'feedback' => "Other",
                'short_name' => 'Other',
                'is_editable' => 0,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],

        ]);

    }
}
