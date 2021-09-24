<?php

namespace Modules\IdsScheduling\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\IdsScheduling\Http\Controllers\IdsSlotBookingController;
use App\Repositories\MailQueueRepository;
use App\Services\HelperService;
use Modules\IdsScheduling\Models\IdsEntries;

class IDSRemainderEmail implements ShouldQueue
{
    use Dispatchable;

    use  InteractsWithQueue, Queueable, SerializesModels;

    protected $entryId,$mailQueueRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($entryId)
    {
       
        $this->entryId =$entryId;
        $this->mailQueueRepository = new MailQueueRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $entry_ids=IdsEntries::with('IdsOfficeSlots', 'IdsOffice', 'IdsServices', 'idsPassportPhotoService')->find($this->entryId);
            $serviceName = $entry_ids->IdsServices->name;
            $photoService=$entry_ids->idsPassportPhotoService;
            if (!empty($photoService)) {
                $serviceName = $serviceName .' and '.$photoService->name;
            }
            if (isset($entry_ids)) {
                 $helper_variable = array(
                        '{receiverFullName}' =>$entry_ids->first_name.' '.$entry_ids->last_name,
                        '{bookingDate}' => date('l F d, Y', strtotime($entry_ids->slot_booked_date)),
                        '{bookingTime}' => date("h:i A", strtotime($entry_ids->IdsOfficeSlots->start_time)),
                        '{location}' => $entry_ids->IdsOffice->name.', '.$entry_ids->IdsOffice->adress,
                        '{serviceName}' => $serviceName,
                        '{serviceRate}' => '$'.$entry_ids->given_rate,
                        
                    );
                    $emailResult = $this->mailQueueRepository->prepareMailTemplate(
                        'ids_remainder_email',
                        null,
                        $helper_variable,
                        'Modules\IdsScheduling\Models\IdsEntries',
                        0,
                        0,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        $entry_ids->email
                    );
                 return $emailResult;
            } else {
                  return false;
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            Log::channel('reportLog')
                ->error($errorMessage);
        }
    }
}
