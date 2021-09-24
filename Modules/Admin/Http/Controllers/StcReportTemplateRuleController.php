<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Modules\Admin\Http\Requests\StcReportTemplateRuleRequest;
use Modules\Admin\Models\Color;
use Modules\Admin\Models\StcReportingTemplateRule;
use Modules\Admin\Models\TemplateSetting;

class StcReportTemplateRuleController extends Controller
{
    /**
     * Display a listing of the stc report template rules.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arr_color = Color::select('id', 'color_name')->get();
        $template_setting_rules = StcReportingTemplateRule::get();
        return view('admin::supervisorpanel.stc-report-template-rule', compact('arr_color', 'template_setting_rules'));
    }

    /**
     * Store a newly created stc report template rules in storage.
     *
     * @param  Modules\Admin\Http\Requests\TemplateSettingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeTemplateSettings(StcReportTemplateRuleRequest $request)
    {
        try {
            DB::beginTransaction();
            $arr_template_settings_form_pos = $request->get('position');
            StcReportingTemplateRule::whereNull('deleted_at')->delete(); //Delete everything
            foreach ($arr_template_settings_form_pos as $key => $template_settings_form_pos) {
                if (!$request->get('rule_color')[$key] == '') {
                    StcReportingTemplateRule::Create([
                        'color_id' => $request->get('rule_color')[$key],
                        'min_value' => $request->get('min_value')[$key],
                        'max_value' => $request->get('max_value')[$key],
                    ]
                    );
                }
            }
            DB::commit();
            return response()->json(array('success' => 'true'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }
}
