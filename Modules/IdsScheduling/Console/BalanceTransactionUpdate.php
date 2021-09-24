<?php

namespace Modules\IdsScheduling\Console;

use DateTime;
use DB;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\Log;
use App\Services\HelperService;
use Modules\IdsScheduling\Models\IdsOnlinePayment;
use Modules\IdsScheduling\Repositories\IdsPaymentRepository;
use Modules\IdsScheduling\Models\IdsEntries;

class BalanceTransactionUpdate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'ids:balanceTrasactionUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Balance transaction and Online processing fees.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(IdsPaymentRepository $idsPaymentRepository)
    {
        parent::__construct();
        $this->idsPaymentRepository = $idsPaymentRepository;
        $this->helperService = new HelperService();
        $this->logger = Log::channel('idsBalanceTransactionLog');
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
            $ids_online_payment = IdsOnlinePayment::select('id', 'entry_id', 'payment_intent')
                ->where('status', 1)
                ->whereHas('idsEntries',function($query){
                    return $query->where('online_processing_fee',0);
                })
                // ->whereMonth('created_at', \Carbon::now()->month)
                // ->whereMonth('created_at', 8)
                ->withTrashed()
                ->get();

            foreach ($ids_online_payment as $each_online_payment) {

                $updateArr = [];
                $payment_intent = $this->idsPaymentRepository->retrievePaymentIntent($each_online_payment['payment_intent']);

                if(
                    isset($payment_intent['charges']) &&
                    isset($payment_intent['charges']['data']) &&
                    isset($payment_intent['charges']['data'][0]) &&
                    isset($payment_intent['charges']['data'][0]['balance_transaction'])
                ){

                    $updateArr['balance_transaction_id'] = $payment_intent['charges']['data'][0]['balance_transaction'];
                    $this->idsPaymentRepository->updatePaymentDetails($updateArr, ['id' => $each_online_payment['id']]);
                    $balanceTransaction = $this->idsPaymentRepository->retrieveBalanceTransaction($updateArr['balance_transaction_id']);
                    $online_processing_fee = 0;
                    if(isset($balanceTransaction->fee) && $balanceTransaction->fee !=''){
                        $online_processing_fee =(($balanceTransaction->fee)/100);
                    }
                    $this->logger->info('Update Balance transaction: entry id=> ' . $each_online_payment['entry_id']
                    . 'balance_transaction_id =>' . $payment_intent['charges']['data'][0]['balance_transaction'] .
                     'online_processing_fee=>' . $online_processing_fee);
                    $this->logger->info('--------------------------------------------------');
                    IdsEntries::where('id', $each_online_payment['entry_id'])->update(['online_processing_fee' => $online_processing_fee]);

                }
            }

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
