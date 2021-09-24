<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CandidateSecurityAwarenessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('candidate_security_awareness')->delete();

        \DB::table('candidate_security_awareness')->insert(array(
            0 => array(
                'id' => 1,
                'answer' => 'I have never heard of these companies within the security industry',
                'order_sequence' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'answer' => "I am somewhat familiar with these companies in the security industry",
                'order_sequence' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
             2 => array(
                'id' => 3,
                'answer' => 'I am very familiar with these companies. They are well known in security',
                'order_sequence' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            
           
        ));
    }
}
