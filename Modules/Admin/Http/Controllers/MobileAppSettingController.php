<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Validator;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\MobileAppSettingRequest;
use Modules\Admin\Models\MobileAppSetting;
use Modules\Admin\Models\DocumentExpiryColorSettings;

class MobileAppSettingController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Services\HelperService
     */
    protected $helperService;

    /**
     * Create  instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the template settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mobile_app_settings = MobileAppSetting::first();
        $documentColorSettings = DocumentExpiryColorSettings::find(1);

        return view('admin::mobile-app-settings.index', compact(
            'mobile_app_settings',
            'documentColorSettings'
        ));
    }

    /**
     * Store a newly created template setting in storage.
     *
     * @param  Modules\Admin\Http\Requests\MobileAppSettingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeMobileSettings(MobileAppSettingRequest $request)
    {
        $validatedData = Validator::make(
            $request->all(),
            [
                'email_reminder.*' => 'required',
                'notification_reminder.*' => 'required',
                'expiry_grace_in_days' => 'required|greater_than_field:expiry_alert_in_days',
                'grace_period_color_code' => 'required',
                'expiry_alert_in_days' => 'required',
                'alert_period_color_code' => 'required',
                'schedule_grace_period_color_code' => 'required',
                'schedule_alert_color_code' => 'required',

            ],
            array(
                'email_reminder.*.required' => 'This is required',
                'notification_reminder.*.required' => 'This is required',
                'expiry_grace_in_days.required' => 'This is required',
                'grace_period_color_code.required' => 'This is required',
                'expiry_alert_in_days.required' => 'This is required',
                'alert_period_color_code.required' => 'This is required',
                'expiry_grace_in_days.greater_than_field' => 'Should greater than grace period'

            )
        );
        if ($validatedData->fails()) {
            return response()->json(array("errors" => $validatedData->errors()), 422);
        }
        $inputs = $request->all();
        $inputs['view_ura_balance'] = isset($inputs['view_ura_balance']) ? 1 : 0;
        $result = MobileAppSetting::first()->update($inputs);
        $expiryGraceDays = $request->expiry_grace_in_days;
        $expiryGraceColorCode = $request->grace_period_color_code;
        $expiryGraceFontColorCode = $request->grace_period_font_color_code;
        $expiryAlertDays = $request->expiry_alert_in_days;
        $expiryAlertColorCode = $request->alert_period_color_code;
        $expiryAlertFontColorCode = $request->alert_period_font_color_code;
        $overdueColorCode = $request->overdue_period_color_code;
        $overdueFontColorCode = $request->overdue_period_font_color_code;

        $schedule_grace_period_days = $request->schedule_grace_period_days;
        $schedule_grace_period_color_code = $request->schedule_grace_period_color_code;
        $schedule_grace_period_font_color_code = $request->schedule_grace_period_font_color_code;

        $schedule_alert_period_days = $request->schedule_grace_period_days + 11;
        $schedule_alert_color_code = $request->schedule_alert_color_code;
        $schedule_alert_period_font_color_code = $request->schedule_alert_period_font_color_code;

        DocumentExpiryColorSettings::updateOrCreate([
            "id" => 1
        ], [
            "grace_period_in_days" => $expiryGraceDays,
            "grace_period_color_code" => $expiryGraceColorCode,
            "grace_period_font_color_code" => $expiryGraceFontColorCode,
            "alert_period_in_days" => $expiryAlertDays,
            "alert_period_color_code" => $expiryAlertColorCode,
            "alert_period_font_color_code" => $expiryAlertFontColorCode,
            "overdue_period_color_code" => $overdueColorCode,
            "overdue_period_font_color_code" => $overdueFontColorCode,
            "schedule_grace_period_days" => $schedule_grace_period_days,
            "schedule_grace_period_color_code" => $schedule_grace_period_color_code,
            "schedule_grace_period_font_color_code" => $schedule_grace_period_font_color_code,
            "schedule_alert_period_days" => $schedule_alert_period_days,
            "schedule_alert_color_code" => $schedule_alert_color_code,
            "schedule_alert_period_font_color_code" => $schedule_alert_period_font_color_code
        ]);
        return response()->json($this->helperService->returnTrueResponse());
        //return back()->with('settings-updated', __('Mobile app settings has been successfully updated'));

    }
}
