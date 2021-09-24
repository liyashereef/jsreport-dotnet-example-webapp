<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class UpdateQrCodeLocationTableWithTotalNoOfAttemptsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qrLocationQuery = \DB::table('customer_qrcode_locations');
        $qrLocationCount = $qrLocationQuery->count();
        $qrCodeLocations = $qrLocationQuery->get();

        $output = new ConsoleOutput();
        $progressBar = new ProgressBar($output, $qrLocationCount);
        foreach ($qrCodeLocations as $qrLocation) {
            $numberOfAttempts = $qrLocation->no_of_attempts;
            $id = $qrLocation->id;

            if ($numberOfAttempts > 0) {
                \DB::table('customer_qrcode_locations')->where('id', $id)->update([
                    'no_of_attempts_week_ends' => $numberOfAttempts,
                    'tot_no_of_attempts_week_day' => $numberOfAttempts,
                    'tot_no_of_attempts_week_ends' => $numberOfAttempts,
                ]);
            }
            $progressBar->advance();
        }
        $progressBar->finish();
    }
}
