<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateBrandAwarenessTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_brand_awareness')->delete();

        \DB::table('candidate_brand_awareness')->insert(array(
            0 => array(
                'id' => 1,
                'answer' => 'I have never heard of Commissionaires - but I am familiar with Garda, G4S, Securitas or Palladin',
                'order_sequence' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'answer' => "I am somewhat familiar about Commissionaires, but don't know much about the company",
                'order_sequence' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
             2 => array(
                'id' => 3,
                'answer' => 'I am very familiar with Commissionaires and know a lot about the company and what they do',
                'order_sequence' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            
           
        ));

    }
}
