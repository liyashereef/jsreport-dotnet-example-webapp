<?php

namespace Modules\UniformScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UniformSchedulingOfficesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \DB::table('uniform_scheduling_offices')->delete();

        \DB::table('uniform_scheduling_offices')->insert(array(
            0 => array(
                'id' => 1,
                'name' => 'Toronto',
                'adress' => '15 Toronto St, Suite 302, Toronto, ON M5C 2E3',
                'latitude'=> '43.6503370000',
                'longitude'=>'-79.3759000000',
                'phone_number_ext'=>'820',
                'phone_number'=>'(416)363-9072',
                'office_start_time'=>'09:30:00',
                'office_end_time'=>'17:00:00',
                'special_instructions'=>'Your safety and the welfare of our employees
                in this new normal of COVID 19 is our top priority.
                When scheduling your appointment, please make sure to answer each of
                the screening questions assigned and let us know if you will need a mask
                for your visit.   Please do not come early or you may be required to wait
                outside before we can let you in.  We are limiting the number of guests to
                no more than 2 clients in our office to maintain social distancing guidelines.
                Thank you for your cooperation.',
                'created_by'=>1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ),
        ));
    }
}
