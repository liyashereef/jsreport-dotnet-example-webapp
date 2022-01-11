<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\MailQueueRepository;

class MailFromQueue extends Command
{
    protected $mailQueueRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail from mail_queue table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MailQueueRepository $mailQueueRepository)
    {
        parent::__construct();
        $this->mailQueueRepository = $mailQueueRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->mailQueueRepository->mailSendingWithQueue();


}
}
