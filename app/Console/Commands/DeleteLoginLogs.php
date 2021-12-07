<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\LoginLog;

class DeleteLoginLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-log:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Respectively delete login logs dalily, when the created date reached into six months.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $table = LoginLog::all();

        if(!$table->isEmpty()){
            $date = \Carbon::now()->subDays(180);
            $formatted = $date->format('Y-m-d H:i:s');
            LoginLog::where('updated_at', '<=', $formatted)->delete();
        }

    }
}
