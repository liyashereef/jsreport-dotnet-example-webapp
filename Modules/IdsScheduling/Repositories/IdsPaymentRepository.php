<?php

namespace Modules\IdsScheduling\Repositories;
use \Stripe\Stripe;
use \Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;
use Modules\IdsScheduling\Repositories\IdsEntriesRepositories;
use Modules\IdsScheduling\Models\IdsOnlinePayment;
use Modules\Admin\Repositories\IdsPaymentMethodsRepository;
use Modules\IdsScheduling\Repositories\IdsTransactionRepository;
class IdsPaymentRepository
{

    private $idsEntriesRepositories;
    protected $model;
    protected $idsPaymentMethodsRepository;
    protected $idsTransactionRepository;

    public function __construct(IdsOnlinePayment $model,
    IdsPaymentMethodsRepository $idsPaymentMethodsRepository,
    IdsEntriesRepositories $idsEntriesRepositories,
    IdsTransactionRepository $idsTransactionRepository
    )
    {
        $this->stripe_secret_key = config('globals.ids_stripe_secret_key');
        $this->idsEntriesRepositories = $idsEntriesRepositories;
        $this->model = $model;
        $this->idsPaymentMethodsRepository = $idsPaymentMethodsRepository;
        $this->idsTransactionRepository = $idsTransactionRepository;
    }
     /**
     * Initializing Payment
     * @param entry_id, amount,office_name
     */
    public function doPayment($request,$paymentParams)
    {

      $amount=$paymentParams['amount'];
      $entriId=$paymentParams['id'];
      $officeName=$paymentParams['office_name'];
      $serviceName=$paymentParams['service_name'];
      if($paymentParams['photoService'])
      {
        $serviceDetails='( '. $serviceName. ' and '.$paymentParams['photoService'].' )';

      }else{
          $serviceDetails='( '.$serviceName.' )';
      }
      \Stripe\Stripe::setApiKey($this->stripe_secret_key);
      $productArr=[
        'name' => 'IDS Appoinment Scheduling - '.$serviceDetails,
        'description'=>'Please complete the payment within 10 minutes. Do not refresh or close the tab while processing the payment.',
      ];
      $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
            'currency' => 'cad',
            'product_data' =>$productArr,
            'unit_amount' => $amount *100,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => url('ids/paymentSuccess?session_id={CHECKOUT_SESSION_ID}'),
        'cancel_url' => route('idsscheduling'),
        ]);
        $updateArr=[
            'entry_id'=>$entriId,
            'status'=>0,
            'started_time'=>\Carbon\Carbon::now(),
            'amount'=>$amount,
            'transaction_id'=>$session->id,
            'payment_intent'=>$session->payment_intent
            ];
        $this->updatePaymentDetails($updateArr,['transaction_id'=>$session->id]);
        $this->paymentLog($session,"IDS Session Initiate:");
        // update metadata
        $metadata=array('type' => 'IDS','office' => $officeName,'service'=>$serviceName,'photoService'=>$paymentParams['photoService'] ?? '');
        $updatePaymentIntent=$this->updatePaymentIntent($session->payment_intent,$metadata);


        return $session;
    }
     /**
     * Update payment details to ids_online_payment table
     * @param payment related parameters
     */
    public function updatePaymentDetails($updateArr,$condArr)
    {
        return $this->model->updateOrCreate($condArr,$updateArr);

    }
    /**
     * After payment checkout, need to retrieve the payment details like status for updation
     * @param sessionId
     */
    public function retrievePayment($sessionId)
    {
        $status='Failed';
        $session=$this->retrieveSession($sessionId);
        $payment_intent=$session->payment_intent ?? '';
        $paymentDetails=$this->getByPaymentIntent($payment_intent);
        if($paymentDetails->status !=1){

            $this->paymentLog($session,"Retrieve IDS Session Details in success page:");
            if(isset($session->customer_details->email) && $session->customer_details->email !=''){
                $email=$session->customer_details->email;
            }
            if($payment_intent){
                $intent = $this->retrievePaymentIntent($payment_intent);
                $status=$intent->status ?? '';
            }

            if($status == 'succeeded'){
                $updateArr=array(
                    'status'=>1,
                    'email'=>$email ?? '',
                    'end_time'=>\Carbon\Carbon::now()
                );
                $entriArr=array('is_online_payment_received'=>1,'id'=>$paymentDetails->entry_id);
                if (isset($intent->charges->data[0]->balance_transaction) && $intent->charges->data[0]->balance_transaction != '') {
                    $updateArr['balance_transaction_id'] = $intent->charges->data[0]->balance_transaction;
                    $balanceTransaction = $this->retrieveBalanceTransaction($intent->charges->data[0]->balance_transaction);
                    if (isset($balanceTransaction->fee) && $balanceTransaction->fee != '') {
                        $entriArr['online_processing_fee'] = (($balanceTransaction->fee) / 100);
                    }
                }
                $this->idsEntriesRepositories->updateEntry($entriArr);
                $this->storeTransactionHistory($paymentDetails);

            }else if($status == 'processing'){
                $updateArr=array(
                    'status'=>2,
                    'email'=>$email ?? ''
                );

            }else{
                $updateArr=array(
                    'status'=>0,
                    'email'=>$email ?? '',
                    'end_time'=>\Carbon\Carbon::now()
                );
            }
            $this->model->where('payment_intent','=',$payment_intent)->update($updateArr);
            $returnArr=array('status'=>$status,'entry_id'=>$paymentDetails->entry_id,'refreshMultipleTime'=>false);
            return $returnArr;
         }else{
            if($paymentDetails->status ==1){
                $status = 'succeeded';
                $returnArr=array('status'=>$status,'entry_id'=>$paymentDetails->entry_id,'refreshMultipleTime'=>true);
                return $returnArr;
            }
         }
    }

     /**
     * Stripe api for retrieve session details
     * @param sessionId
     */
    public function retrieveSession($sessionId)
    {

        \Stripe\Stripe::setApiKey($this->stripe_secret_key);
        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        $this->paymentLog($session,"Retrieve IDS Session Details:");
        return  $session;
    }
    /**
     * Stripe api for retrieve payment intent details
     * @param payment_intent_id
     */
    public function retrievePaymentIntent($paymentId)
    {

        \Stripe\Stripe::setApiKey($this->stripe_secret_key);
        $result = \Stripe\PaymentIntent::retrieve($paymentId);
        $this->paymentLog($result,"Retrieve IDS Payment Intent:");
        return  $result;

    }
    /**
     * Stripe api for update payment intent details like metadata
     * @param metadata
     */
    public function updatePaymentIntent($paymentId,$metadataArr)
    {

        \Stripe\Stripe::setApiKey($this->stripe_secret_key);
        $result = \Stripe\PaymentIntent::update($paymentId,[
                'metadata' => $metadataArr,
            ]);
        $this->paymentLog($result,"Update metadata:");
        return  $result;

    }
    /**
     * Stripe api for cancel payment intent
     * @param payment intent
     */
    public function cancelPaymentIntent($paymentId)
    {

        $stripe = new \Stripe\StripeClient(
        $this->stripe_secret_key
        );
        $result=$stripe->paymentIntents->cancel($paymentId);
        $this->paymentLog($result,"Cancel IDS Payment Intent:");
        return $result;


    }

    /**
     * Stripe api for retrieve Balance Transaction
     * @param balaceTransactionId
     */
    public function retrieveBalanceTransaction($balaceTransactionId)
    {
        \Stripe\Stripe::setApiKey($this->stripe_secret_key);
        $result = \Stripe\BalanceTransaction::retrieve($balaceTransactionId);
        $this->paymentLog($result, "Retrieve Balance Transaction Details:");
        return $result;
    }

    /**
     * Store payment log
     * @param payment response
     */
    public function paymentLog($response,$type){
        Log::channel('idsPayment')
            ->info($type.json_encode(['date' => \Carbon\Carbon::now()->format('Y-m-d'),
            'time' => \Carbon\Carbon::now()->format('H:i:s'),
            'response'=>$response
            ])
        );
    }

    public function updateOnlinePayment($inputs){
        return $this->model
        ->where('entry_id',$inputs['old_ids_entry_id'])
        ->update([
            'entry_id'=>$inputs['ids_entry_id'],
            'entry_id_updated_at'=>\Carbon::now()
        ]);
    }


    public function storeTransactionHistory($paymentDetails){
        $getMethod=$this->idsPaymentMethodsRepository->getByShortName('STRIPE');
        $transactionArr=array(
            'entry_id'=>$paymentDetails['entry_id'],
            'ids_online_payment_id'=>$paymentDetails['id'],
            'ids_payment_method_id'=>$getMethod->id,
            'amount'=>$paymentDetails['amount'],
            'transaction_type'=>'Received'
        );
        return $this->idsTransactionRepository->store($transactionArr);
    }
    public function getByPaymentIntent($paymentIntent){
        return $this->model->where('payment_intent',$paymentIntent)->first();
    }
    /**
     * Stripe api for initiate Refund
     * @param payment intent,amount
     */
    // public function initiateRefund($paymentId,$amount)
    // {
    //     try{
    //         $stripe = new \Stripe\StripeClient($this->stripe_secret_key);
    //         $result =$stripe->refunds->create([
    //         'payment_intent' => $paymentId,
    //         'amount'=>$amount*100
    //         ]);
    //         $this->paymentLog($result,"Initiate Refund:");
    //         return array('success'=>true,'result'=>$result,'message'=>'Success');
    //     }catch (\Exception $e) {
    //         return array('success'=>false,'result'=>[],'message'=>$e->getMessage());
    //     }

    // }
    /**
     * Stripe api for retrieve Refund
     * @param payment intent
    */
    // public function retrieveRefund($paymentIntent)
    // {
    //     $stripe = new \Stripe\StripeClient($this->stripe_secret_key);
    //     $result=$stripe->refunds->all(['payment_intent' => $paymentIntent]);
    //     $this->paymentLog($result,"Retrieve Refund Details:");
    //     return $result;
    // }
}
