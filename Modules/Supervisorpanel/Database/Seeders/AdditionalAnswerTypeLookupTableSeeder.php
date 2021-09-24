<?php

namespace Modules\Supervisorpanel\Database\Seeders;

use Illuminate\Database\Seeder;

class AdditionalAnswerTypeLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('answer_type_lookups')->insert([
            'answer_type' => 'Leave Type',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
