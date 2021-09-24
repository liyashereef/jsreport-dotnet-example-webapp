<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class FeedbackLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('feedback_lookups')->delete();
        
        \DB::table('feedback_lookups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'feedback' => 'Proceed',
                'created_at' => '2017-12-27 06:07:20',
                'updated_at' => '2018-01-10 10:56:53',
                'deleted_at' => '2018-01-10 10:56:53',
            ),
            1 => 
            array (
                'id' => 2,
                'feedback' => 'Excellent Fit - Wage In Line With Expectations/Very Low Flight Risk',
                'created_at' => '2017-12-27 06:07:28',
                'updated_at' => '2017-12-27 06:07:28',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'feedback' => 'Good Fit - Wage Expectations Reasonably In Line With Position And Experience/Low Flight Risk',
                'created_at' => '2017-12-27 06:07:36',
                'updated_at' => '2017-12-27 06:07:36',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'feedback' => 'Average Fit - Wage Expectations On High End But In Line/Average Flight Risk',
                'created_at' => '2017-12-27 06:07:43',
                'updated_at' => '2017-12-27 06:07:43',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'feedback' => 'Below Average Fit - Wage Expectations Exceed Position/Moderate Flight Risk',
                'created_at' => '2017-12-27 06:07:50',
                'updated_at' => '2017-12-27 06:07:50',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'feedback' => 'Poor Fit - Wage Expectations Very High/Significant Flight Risk',
                'created_at' => '2017-12-27 06:07:57',
                'updated_at' => '2017-12-27 06:07:57',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'feedback' => 'Reject',
                'created_at' => '2017-12-27 06:08:04',
                'updated_at' => '2018-01-10 10:56:56',
                'deleted_at' => '2018-01-10 10:56:56',
            ),
        ));
        
        
    }
}