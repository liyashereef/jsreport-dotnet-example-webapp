<?php

namespace Modules\Supervisorpanel\Database\Seeders;

use Illuminate\Database\Seeder;

class AnswerTypeLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {
        \DB::table('answer_type_lookups')->insert([
            'answer_type' => 'Yes/No',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        \DB::table('answer_type_lookups')->insert([
            'answer_type' => 'Text',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        \DB::table('answer_type_lookups')->insert([
            'answer_type' => 'Employee List',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
