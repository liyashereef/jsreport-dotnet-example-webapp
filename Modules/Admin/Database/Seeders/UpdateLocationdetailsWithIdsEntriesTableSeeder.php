<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class UpdateLocationdetailsWithIdsEntriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $postal_code_arr = [];

        $idsEntriesQry = \DB::table('ids_entries')
            ->where('postal_code', '!=', null)
            ->where('postal_code', 'not like', "% %")
            ->where('latitude', null)
            ->where('longitude', null)
            ->where('deleted_at', null);

        $idsEntriesCount = $idsEntriesQry->count();

        if ($idsEntriesCount > 0) {
            $output = new ConsoleOutput();
            $progressBar = new ProgressBar($output, $idsEntriesCount);

            $google_api_key = config('globals.google_api_curl_key');

            foreach ($idsEntriesQry->cursor() as $idsEntry) {
                if (!empty($idsEntry->postal_code)) {
                    $upper_postal_code = strtoupper($idsEntry->postal_code);
                    if(!isset($postal_code_arr[$upper_postal_code])) {
                        $location_data = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=" . $idsEntry->postal_code . "&sensor=false&key=" . $google_api_key);
                        $location_data = json_decode($location_data);

                        if (isset($location_data->{'results'}[0])) {
                            $latitude = $location_data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                            $longitude = $location_data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

                            $postal_code_arr[$upper_postal_code]['lat'] = $latitude;
                            $postal_code_arr[$upper_postal_code]['lng'] = $longitude;

                            \DB::table('ids_entries')->where('id', $idsEntry->id)->update([
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                            ]);
                        }
                    } else {
                        \DB::table('ids_entries')->where('id', $idsEntry->id)->update([
                            'latitude' => $postal_code_arr[$upper_postal_code]['lat'],
                            'longitude' => $postal_code_arr[$upper_postal_code]['lng'],
                        ]);
                    }
                }
                $progressBar->advance();
            }
            $progressBar->finish();
        }
    }
}
