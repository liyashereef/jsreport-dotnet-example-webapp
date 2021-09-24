<?php

namespace Modules\Contracts\Repositories\Admin;

use Modules\Contracts\Models\ContractExpirySettings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\HelperService;
use Carbon\Carbon;

class ContractExpirySettingsRepository
{
    protected $model;


    public function __construct(ContractExpirySettings $contractExpirySettings, HelperService $helperService)
    {
        $this->model = $contractExpirySettings;
        $this->helperService = $helperService;
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $table = ContractExpirySettings::all();

        if($table->isEmpty()){
            $result = new ContractExpirySettings();
            $result->alert_period_1=$data['alert_period_1'];
            $result->alert_period_2=$data['alert_period_2'];
            $result->alert_period_3=$data['alert_period_3'];
            $result->email_1_time = Carbon::createFromFormat('h:i a', $data['email_1_time']);
            $result->email_2_time = Carbon::createFromFormat('h:i a', $data['email_2_time']);
            $result->email_3_time = Carbon::createFromFormat('h:i a', $data['email_3_time']);
            $result->save($data);

            return response()->json($this->helperService->returnTrueResponse());

        }else{
            $data['email_1_time'] = Carbon::createFromFormat('h:i a', $data['email_1_time']);
            $data['email_2_time'] = Carbon::createFromFormat('h:i a', $data['email_2_time']);
            $data['email_3_time'] = Carbon::createFromFormat('h:i a', $data['email_3_time']);
            $result = ContractExpirySettings::first()->update($data);
            return response()->json($this->helperService->returnTrueResponse());
        }
    }


}
