<?php

namespace Modules\Contracts\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RfpProcessStepsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('rfp_process_steps')->truncate();
        \DB::table('rfp_process_steps')->insert([
            0=>[
                //"id"=>1,
                "process_steps"=>"RFP Summary Entered	",
                "step_number"=>1,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ],
            1=>[
                //"id"=>1,
                "process_steps"=>"RFP Approved And Resources Allocated	",
                "step_number"=>2,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ]
            ,2=>[
                //"id"=>1,
                "process_steps"=>"Template Downloaded",
                "step_number"=> 3,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ],
            3=>[
                //"id"=>1,
                "process_steps"=> "Site Visit",
                "step_number"=> 4,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ],
            4=>[
                //"id"=>1,
                "process_steps"=> "Question & Answer",
                "step_number"=> 5,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ],
            5=>[
                //"id"=>1,
                "process_steps"=> "Pricing Model",
                "step_number"=>6,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ],
            6=>[
                //"id"=>1,
                "process_steps"=> "First Draft Completed",
                "step_number"=>7,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ],
            7=>[
                //"id"=>1,
                "process_steps"=>"Insurance, WSIB and Other Documents Completed",
                "step_number"=> 8,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ],
            8=>[
                //"id"=>1,
                "process_steps"=>"Review Session With Executive",
                "step_number"=> 9,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ],
            9=>[
                //"id"=>1,
                "process_steps"=>"Revisions And Edits",
                "step_number"=>10,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ],
            10=>[
               // "id"=>1,
                "process_steps"=>"Submit RFP",
                "step_number"=>11,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ]
        ]);
        // $this->call("OthersTableSeeder");
    }
}
