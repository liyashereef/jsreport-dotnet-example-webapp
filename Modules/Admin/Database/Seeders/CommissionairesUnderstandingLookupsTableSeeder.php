<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class CommissionairesUnderstandingLookupsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('commissionaires_understanding_lookups')->delete();

        \DB::table('commissionaires_understanding_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'commissionaires_understandings' => 'I thought Commissionaires only hires veterans',
                'short_name' => null,
                'order_sequence' => 1,
                'created_at' => '2019-03-21 15:58:00',
                'updated_at' => '2019-03-21 15:58:00',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'commissionaires_understandings' => 'Commissionaires tries to hire veterans, but also tries to target regular security guards with no military experience',
                'short_name' => null,
                'order_sequence' => 2,
                'created_at' => '2019-03-21 15:58:00',
                'updated_at' => '2019-03-21 15:58:00',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'commissionaires_understandings' => 'Commissionaires hires veterans from the Canadian Armed Forces only',
                'short_name' => null,
                'order_sequence' => 3,
                'created_at' => '2019-03-21 15:58:00',
                'updated_at' => '2019-03-21 15:58:00',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'commissionaires_understandings' => 'Commissionaires considers anyone who served in the military as a veteran even if they didn\'t serve in the canadian military',
                'short_name' => null,
                'order_sequence' => 4,
                'created_at' => '2019-03-21 15:58:00',
                'updated_at' => '2019-03-21 15:58:00',
                'deleted_at' => null,
            ),
        ));

    }
}
