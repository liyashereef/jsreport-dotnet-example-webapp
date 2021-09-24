<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class IdsCustomQuestionOptionSeeder extends Seeder
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
        
        \DB::table('ids_custom_question_options')->insert(
            [
                0 => [
                    'id' => 1,
                    'custom_question_option' => 'Other',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('y-m-d H:i:s')
                ],
            ]
        );
    }
}
