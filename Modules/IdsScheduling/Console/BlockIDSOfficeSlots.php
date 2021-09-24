<?php

namespace Modules\IdsScheduling\Console;
use DateTime;
use DB;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\Log;
use App\Services\HelperService;
use Modules\Admin\Repositories\IdsOfficeSlotsBlocksRepositories;

class BlockIDSOfficeSlots extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'ids:officeslotblock {start_date} {end_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Blocking IDS office slotes on saturday,sunday by start and end date.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(IdsOfficeSlotsBlocksRepositories $idsOfficeSlotsBlocksRepositories)
    {
        parent::__construct();
        $this->idsOfficeSlotsBlocksRepositories = $idsOfficeSlotsBlocksRepositories;
        $this->helperService = new HelperService();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            \DB::beginTransaction();

            $start_date =$this->argument('start_date');
            $end_date = $this->argument('end_date');
            $startDate = new DateTime($start_date);
            $endDate = new DateTime($end_date);

            $days['sundays'] = array();
            $days['saturdays'] = array();

            while ($startDate <= $endDate) {

                if ($startDate->format('w') == 0) {
                    $days['sundays'][] = $startDate->format('Y-m-d');
                }
                if ($startDate->format('w') == 6) {
                    $days['saturdays'][] = $startDate->format('Y-m-d');
                }
                if ($startDate->format('w') == 5) {
                    $days['fridays'][] = $startDate->format('Y-m-d');
                }
                if($startDate->format('w') != 0 || $startDate->format('w') != 6){
                    $days['weekdays'][] = $startDate->format('Y-m-d');
                }

                $startDate->modify('+1 day');
            }

            $this->idsOfficeSlotsBlocksRepositories->storeOnCommand($days);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            echo $e;
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
