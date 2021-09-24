<?php

namespace Modules\Client\Console;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\Log;
use App\Services\HelperService;
use Modules\Client\Models\VisitorLogScreeningSubmission;
class ScreeningTimeUpdate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'client:screeningTimeUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Screening Time (screened_at) From created_at field.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->helperService = new HelperService();
        $this->logger = Log::channel('visitorLogScreeninTimeUpdateLog');
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
            $this->logger->info('-------------------------------------------------- Command started');
            $visitoLogResult = VisitorLogScreeningSubmission::select('id', 'created_at')
                ->whereNull('screened_at')
                ->withTrashed()
                ->get();

            foreach ($visitoLogResult as $row) {
                $this->logger->info('Update Screening Time: id=> ' . $row->id
                . ' created_at =>' . $row->created_at);
                $this->logger->info('--------------------------------------------------');
                VisitorLogScreeningSubmission::where('id', $row->id)->update(['screened_at' => $row->created_at]);
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
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
