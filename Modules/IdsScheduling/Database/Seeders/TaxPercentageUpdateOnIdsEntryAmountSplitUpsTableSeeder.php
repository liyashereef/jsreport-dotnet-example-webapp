<?php

namespace Modules\IdsScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Eloquent\Collection;
use Modules\IdsScheduling\Models\IdsEntryAmountSplitUp;
use Modules\IdsScheduling\Models\IdsEntries;

class TaxPercentageUpdateOnIdsEntryAmountSplitUpsTableSeeder extends Seeder
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

            $idsEntries = IdsEntries::withTrashed()->whereHas('idsEntryAmountSplitUp', function ($query) {
                return $query->where('type', 0);
            })->with(['idsEntryAmountSplitUp' => function ($query) {
                return $query->select('id', 'entry_id', 'service_id','type','rate');
            }]);
            $idsEntriesCount = $idsEntries->count();
            if ($idsEntriesCount > 0) {
                $output = new ConsoleOutput();
                $progressBar = new ProgressBar($output, $idsEntriesCount);

                foreach ($idsEntries->cursor() as $idsEntry) {
                    $inputs = [];
                    $feeSum = collect($idsEntry->idsEntryAmountSplitUp)->where('type','!=',0)->sum('rate');
                    $taxEntry = collect($idsEntry->idsEntryAmountSplitUp)->where('type',0)->first();
                    if($taxEntry){
                        $taxFee = floatval($taxEntry->rate);
                        $percentage = 0;
                        if($feeSum && $taxFee){
                            $percentage = floatval(number_format(($taxFee*100) / $feeSum,2));
                        }
                        // dd($feeSum,$taxFee,$percentage);
                        IdsEntryAmountSplitUp::where('id',$taxEntry->id)->update(['tax_percentage'=>$percentage]);
                    }
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
