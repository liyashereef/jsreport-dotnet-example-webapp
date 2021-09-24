<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RecCommissionairesUnderstandingLookupsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::connection('mysql_rec')->table('rec_commissionaires_understanding_lookups')->delete();

        \DB::connection('mysql_rec')->table('rec_commissionaires_understanding_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'commissionaires_understandings' => 'I thought Commissionaires only hires senior veterans over the age of 65.',
                'short_name' => null,
                'order_sequence' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'commissionaires_understandings' => 'Although Commissionaires tries to hire veterans, they also open their doors to guards with no military experience.',
                'short_name' => null,
                'order_sequence' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'commissionaires_understandings' => 'Commissionaires only hires veterans from the Canadian Armed Forces.',
                'short_name' => null,
                'order_sequence' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'commissionaires_understandings' => 'Commissionaires values any military experience be it the Canadian Armed Forces or foreign military experience.',
                'short_name' => null,
                'order_sequence' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'commissionaires_understandings' => 'I\'ve never heard of Commissionaires prior to applying so I can\'t comment.',
                'short_name' => null,
                'order_sequence' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'commissionaires_understandings' => 'I have little understanding of Commissionaires or their social mandate.',
                'short_name' => null,
                'order_sequence' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 7,
                'commissionaires_understandings' => 'I am an existing Commissionaire. This doesn\'t apply to me.',
                'short_name' => null,
                'order_sequence' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
        ));
    }
}
