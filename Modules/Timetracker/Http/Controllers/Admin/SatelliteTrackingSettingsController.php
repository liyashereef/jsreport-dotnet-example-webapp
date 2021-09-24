<?php

namespace Modules\Timetracker\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use DB;
use Modules\Timetracker\Http\Requests\SatelliteTrackingSettingsRequest;
use Modules\Admin\Models\TemplateSettingRules;
use Modules\Timetracker\Models\SatelliteTrackingSetting;

class SatelliteTrackingSettingsController extends Controller
{
    public function index()
    {
        return view('timetracker::admin.satellite-tracker-settings', [
            'availableColors' => SatelliteTrackingSetting::colors(),
            'satelliteTrackingSettings' => SatelliteTrackingSetting::all()
        ]);
    }

    public function save(SatelliteTrackingSettingsRequest $request)
    {
        try {
            DB::beginTransaction();
            SatelliteTrackingSetting::whereNull('deleted_at')->delete(); //Delete everything
            foreach ($request->get('position') as $key => $pos) {
                if (!$request->get('rule_color')[$key] == '') {
                    SatelliteTrackingSetting::Create(
                        [
                            'color' => $request->get('rule_color')[$key],
                            'min' => $request->get('min_value')[$key],
                            'max' => $request->get('max_value')[$key],
                        ]
                    );
                }
            }
            DB::commit();
            return response()->json(array('success' => 'true'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return response()->json(
                array(
                    'success' => 'false',
                    'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()
                )
            );
        }
    }
}
