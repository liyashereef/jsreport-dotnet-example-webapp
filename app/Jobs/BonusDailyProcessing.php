<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Modules\Hranalytics\Models\BonusEmployeeData;
use Modules\Hranalytics\Models\BonusFinalizedData;
use Modules\Hranalytics\Models\BonusSettings;
use Modules\Hranalytics\Repositories\BonusRepository;

class BonusDailyProcessing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7200;
    protected $bonusRepository;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bonusRepository = new BonusRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel('BonusLog')->info("Bonus Log: Job started");
        $existingBonus = BonusSettings::where("active", 1)->first();
        if ($existingBonus) {
            $poolId = $existingBonus->id;
            $bonusData = $this->bonusRepository->saveBasicData($poolId);
            try {
                if ($existingBonus->end_date <= \Carbon::now()->format("Y-m-d")) {
                    BonusFinalizedData::where("bonus_pool_id", $poolId)->delete();
                    BonusFinalizedData::insert($bonusData, $poolId);
                    $this->bonusRepository->processFinalData($poolId);
                }
            } catch (\Throwable $th) {
                Log::channel('BonusLog')->info($th);
            }
        }
        $upcomingBonus = BonusSettings::where("active", 0)->orderBy("start_date", "asc")->first();
        if (isset($upcomingBonus)) {
            $start_date = $upcomingBonus->start_date;
            if ($start_date == \Carbon::now()->addDays(1)->format("Y-m-d")) {
                BonusSettings::find($upcomingBonus->id)->update([
                    "active" => 1
                ]);
            }
        }
        Log::channel('BonusLog')->info("Bonus Log: Job Ended");
    }
}
