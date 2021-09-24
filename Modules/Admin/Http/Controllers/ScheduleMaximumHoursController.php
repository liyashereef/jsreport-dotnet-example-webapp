<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\ScheduleMaximumHourRequest;
use Modules\Admin\Models\ScheduleMaximumHour;

class ScheduleMaximumHoursController extends Controller
{
    /**
     * Display Form for adding generic password.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cs = ScheduleMaximumHour::first();
        if (is_null($cs)) {
            $cs = new ScheduleMaximumHour();
            $cs->hours = '8';
            $cs->save();
        }
        return view('admin::masters.maximum-shift-timing', compact('cs'));
    }

    /**
     * Store a newly created password in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ScheduleMaximumHourRequest $request)
    {
        $inputs = $request->all();
        $data['hours'] = $inputs['hour'];
        ScheduleMaximumHour::first()->update($data);
        return response()->json(['success' => true]);
        // return back()->with('Hour-updated', __('Maximum Shift Hour has been successfully updated'));
    }

}
