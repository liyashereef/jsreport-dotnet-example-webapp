<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileDelete implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path)
    {
        Log::channel('fileDeleteJobLog')->info($this->filePath);
        $this->filePath = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Delete file if present
        try {
            if (File::exists($this->filePath)) {
                File::delete($this->filePath);
                Log::channel('fileDeleteJobLog')->info($this->filePath);
            } else {
                throw new \Exception("File not found");
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile() . " File - " . $this->filePath;
            Log::channel('fileDeleteJobLog')
                ->error($errorMessage);
        }
    }
}
