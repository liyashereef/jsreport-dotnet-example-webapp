<?php

namespace Modules\Contracts\Http\Controllers\Admin;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Contracts\Http\Requests\ContractExpirySettingsRequest;
use Modules\Contracts\Repositories\Admin\ContractExpirySettingsRepository;
use Modules\Contracts\Models\ContractExpirySettings;


class ContractExpirySettingsController extends Controller
{


    public function __construct(ContractExpirySettingsRepository $contractExpirySettingsRepository, HelperService $helperService)
    {

        $this->helperService = $helperService;
        $this->repository = $contractExpirySettingsRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $email_time = ContractExpirySettings::select('*',
        \DB::raw("TIME_FORMAT(email_1_time, '%h:%i %p') as email_1_time"),
        \DB::raw("TIME_FORMAT(email_2_time, '%h:%i %p') as email_2_time"),
        \DB::raw("TIME_FORMAT(email_3_time, '%h:%i %p') as email_3_time")
        )->first();
        $contract_expiry = ContractExpirySettings::first();
        return view('contracts::admin.contract-expiry-settings',compact('contract_expiry','email_time'));
    }

    public function store(ContractExpirySettingsRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

}
