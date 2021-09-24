<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Validator;
use Modules\Admin\Http\Requests\ClientOnboardingRequest;
use Modules\Admin\Repositories\ClientOnboardingSettingRepository;
use Modules\Admin\Repositories\ClientOnboardingTemplateRepository;
use App\Services\HelperService;

class ClientOnboardingSettingsController extends Controller
{
    protected $model;
    protected $repository;
    protected $settingRepository;
    protected $helperService;

    /**
     * Display a listing of the resource.
     * @param ClientOnboardingTemplateRepository $clientOnboardingTemplateRepository
     * @param ClientOnboardingSettingRepository $clientOnboardingSettingRepository
     * @param HelperService $helperService
     */
    public function __construct(
        ClientOnboardingTemplateRepository $clientOnboardingTemplateRepository,
        ClientOnboardingSettingRepository $clientOnboardingSettingRepository,
        HelperService $helperService
    )
    {
        $this->repository = $clientOnboardingTemplateRepository;
        $this->settingRepository = $clientOnboardingSettingRepository;
        $this->helperService = $helperService;
    }

    public function index()
    {
        $onboardingList = $this->settingRepository->getAllByType('mail');
        return view('admin::contracts.onboarding-settings', compact('onboardingList'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $validatedData = Validator::make(
                $request->all(),
                [
                    'reminder.*' => 'required',
                ],
                array(
                    'reminder.*.requried' => 'This is required'
                )
            );
            if ($validatedData->fails()) {
                return response()->json(array("errors"=>$validatedData->errors()), 422);
            }
            \DB::beginTransaction();
            $section = $this->settingRepository->save($request->reminder,'mail');
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function getSingle($id)
    {
        return response()->json($this->repository->getSectionWithTask($id));
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
