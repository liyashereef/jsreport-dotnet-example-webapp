<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\SiteSettingsRequest;
use Modules\Admin\Models\SiteSettings;
use Modules\Admin\Models\User;
use Modules\Admin\Models\DailyHealthSchedules;

class SiteSettingsController extends Controller
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
        $site_settings = SiteSettings::first();
        $users = User::whereHas("roles", function ($q) {
            return $q->whereNotIn("name", ['admin', 'super_admin']);
        })->get();
        $reportUsers = DailyHealthSchedules::get()->pluck("user_id")->toArray();
        return view('admin::settings.site-settings', compact('site_settings', "users", "reportUsers"));
    }

    /**
     *
     * @param  Modules\Admin\Http\Requests\SiteSettingsRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeSiteSettings(SiteSettingsRequest $request)
    {

        $inputs = $request->all();
        $result = SiteSettings::first()->update($inputs);
        DailyHealthSchedules::truncate();
        if ($request->get("daily_heathscreen_to") != null) {
            foreach ($request->get("daily_heathscreen_to") as $reportsTo) {
                DailyHealthSchedules::insert([
                    "user_id" => $reportsTo
                ]);
            }
        }

        //DailyHealthSchedules::insert($request->get("daily_heathscreen_to"));
        return response()->json($this->helperService->returnTrueResponse());
    }
}
