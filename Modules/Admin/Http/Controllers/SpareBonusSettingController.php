<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Validator;
use App\Services\HelperService;
use Modules\Admin\Models\SpareBonusModelSetting;

class SpareBonusSettingController extends Controller
{

    public function __construct(SpareBonusModelSetting $spareBonusModelSetting, HelperService $helperService)
    {
        $this->spareBonusModelSetting = $spareBonusModelSetting;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $spareBonusModelSettings = SpareBonusModelSetting::find(1);
        return view('admin::spare-bonus-settings.index',compact('spareBonusModelSettings'));
    }

    public function storeBonusModelSettings(Request $request){
        $validatedData = Validator::make(
            $request->all(),
            [
                'reliability_grace_period_in_days' => 'required',
                'reliability_grace_period_color_code' => 'required',
                'reliability_safe_score' => 'required|gt:reliability_grace_period_in_days',
                'reliability_safe_score_color_code' => 'required',
                'reliability_rank_top_level' => 'required|min:1||lt:reliability_rank_average_level',
                'reliability_rank_top_level_color_code'=> 'required',
                'reliability_rank_average_level' => 'required|min:1',
                'reliability_rank_average_level_color_code'=> 'required',
                'schedule_top_rank_message'=> 'required',
                'schedule_average_rank_message'=> 'required',
                'schedule_below_average_rank_message'=> 'required',

            ],
            array(
                'reliability_grace_period_in_days.required' => 'This is required',
                'reliability_grace_period_color_code.required' => 'This is required',
                'reliability_safe_score.gt' => 'Should be greater than grace score',
                'reliability_safe_score.required' => 'This is required',
                'reliability_safe_score_color_code.required' => 'This is required',
                'reliability_rank_top_level.required' => 'This is required',
                'reliability_rank_top_level.lt'  => 'Should be less than average level',
                'reliability_rank_top_level_color_code.required' => 'This is required',
                'reliability_rank_average_level.required' => 'This is required',
                'reliability_rank_average_level_color_code.required' => 'This is required',
                'schedule_top_rank_message.required' => 'This is required',
                'schedule_average_rank_message.required' => 'This is required',
                'schedule_below_average_rank_message.required' => 'This is required',
            )
        );
        if ($validatedData->fails()) {
            return response()->json(array("errors" => $validatedData->errors()), 422);
        }

        SpareBonusModelSetting::updateOrCreate([
            "id" => 1
        ], [
            "reliability_grace_period_in_days" => $request->reliability_grace_period_in_days,
            "reliability_grace_period_color_code" => $request->reliability_grace_period_color_code,
            "reliability_grace_period_font_color_code" => $request->reliability_grace_period_font_color_code,
            "reliability_safe_score" => $request->reliability_safe_score,
            "reliability_safe_score_color_code" => $request->reliability_safe_score_color_code,
            "reliability_safe_score_font_color_code" => $request->reliability_safe_score_font_color_code,
            "reliability_rank_top_level" => $request->reliability_rank_top_level,
            "reliability_rank_top_level_color_code" => $request->reliability_rank_top_level_color_code,
            "reliability_rank_top_level_font_color_code" => $request->reliability_rank_top_level_font_color_code,
            "reliability_rank_average_level" => $request->reliability_rank_average_level,
            "reliability_rank_average_level_color_code" => $request->reliability_rank_average_level_color_code,
            "reliability_rank_average_level_font_color_code" => $request->reliability_rank_average_level_font_color_code,
            "schedule_top_rank_message" => $request->schedule_top_rank_message,
            "schedule_average_rank_message" => $request->schedule_average_rank_message,
            "schedule_below_average_rank_message" => $request->schedule_below_average_rank_message,
        ]);

        return response()->json($this->helperService->returnTrueResponse());

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {

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
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
