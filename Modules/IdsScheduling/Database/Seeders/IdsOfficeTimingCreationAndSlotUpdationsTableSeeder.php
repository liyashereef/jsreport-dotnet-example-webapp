<?php

namespace Modules\IdsScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Models\IdsOffice;
use Modules\Admin\Models\IdsOfficeTimings;
use Modules\Admin\Models\IdsOfficeSlots;

class IdsOfficeTimingCreationAndSlotUpdationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try{

            \DB::beginTransaction();

            $idsOffices = IdsOffice::doesnthave('IdsOfficeTimings');
            $idsOfficeCount = $idsOffices->count();
            if ($idsOfficeCount > 0) {
                $output = new ConsoleOutput();
                $progressBar = new ProgressBar($output, $idsOfficeCount);

                foreach ($idsOffices->cursor() as $idsOffice) {
                    $inputs = [];
                    $inputs['ids_office_id'] = $idsOffice->id;
                    $inputs['start_time'] = $idsOffice->office_hours_start_time;
                    $inputs['end_time'] = $idsOffice->office_hours_end_time;
                    $inputs['start_date'] = date('Y-m-d',strtotime($idsOffice->created_at));
                    $inputs['intervals'] = $idsOffice->intervals;
                    $timings = IdsOfficeTimings::create($inputs);
                    IdsOfficeSlots::where('ids_office_id',$idsOffice->id)
                    ->update(['ids_office_timing_id'=>$timings->id]);

                    $progressBar->advance();
                    
                }

                $progressBar->finish();
            }

            \DB::commit();

        }catch(\Exception $e) {
            \DB::rollBack();
            echo $e->getMessage();
        }
    }
}
