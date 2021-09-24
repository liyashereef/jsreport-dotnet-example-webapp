<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Validator;
use Modules\Admin\Repositories\UserCertificateExpirySettingsRepository;
use Modules\Admin\Models\Color;
use Modules\Admin\Models\DocumentExpiryColorSettings;

use App\Services\HelperService;

class UserCertificateExpiryController extends Controller
{

    protected $settingRepository;
    protected $helperService;

    /**
     * Display a listing of the resource.
     * @param UserCertificateExpirySettingsRepository $userCertificateExpirySettingsRepository
     * @param HelperService $helperService
     */
    public function __construct(
        UserCertificateExpirySettingsRepository $userCertificateExpirySettingsRepository,
        HelperService $helperService
    ) {
        $this->settingRepository = $userCertificateExpirySettingsRepository;
        $this->helperService = $helperService;
    }

    public function index()
    {
        //        $this->settingRepository->sendUserCertificateExpiryReminders();
        $Colors = Color::get();
        $colorArray = [];
        foreach ($Colors as $color) {
            $colorArray[$color->id] =  $color->color_name;
        }
        $notificationValue = '';
        $emailSettings = $this->settingRepository->getAllByType('mail');
        $notificationSettings = $this->settingRepository->getAllByType('notification');
        if (!empty($notificationSettings) && isset($notificationSettings[0])) {
            $notificationValue = (int) $notificationSettings[0]->value;
        }
        $documentColorSettings = DocumentExpiryColorSettings::find(1);
        return view('admin::user-certificate-expiry.index', compact(
            'emailSettings',
            'notificationValue',
            'colorArray',
            'documentColorSettings'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {


            \DB::beginTransaction();
            $emailSection = $this->settingRepository->save($request->email_reminder, 'mail');
            $notificationSection = $this->settingRepository->save($request->notification_reminder, 'notification');
            $expiryGraceDays = $request->expiry_grace_in_days;
            $expiryGraceColorCode = $request->grace_period_color_code;
            $expiryGraceFontColorCode = $request->grace_period_font_color_code;
            $expiryAlertDays = $request->expiry_alert_in_days;
            $expiryAlertColorCode = $request->alert_period_color_code;
            $expiryAlertFontColorCode = $request->alert_period_font_color_code;
            $overdueColorCode = $request->overdue_period_color_code;
            $overdueFontColorCode = $request->overdue_period_font_color_code;


            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /*
     * Send user certificate expiry reminder mail
     * @return Json
     */
    public function userCertificateExpiryDueReminderMail()
    {
        try {
            \DB::beginTransaction();
            $result = $this->settingRepository->sendUserCertificateExpiryReminders();
            \DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }
}
