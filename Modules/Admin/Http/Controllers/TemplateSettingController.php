<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Modules\Admin\Http\Requests\TemplateSettingRequest;
use Modules\Admin\Models\Color;
use Modules\Admin\Models\TemplateSetting;
use Modules\Admin\Models\TemplateSettingRules;

class TemplateSettingController extends Controller
{
    /**
     * Display a listing of the template settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arr_color = Color::select('id', 'color_name')->get();
        $existing_template = TemplateSetting::first();
        $template_setting_rules = TemplateSettingRules::get();
        return view('admin::template.templatesetting', compact('arr_color', 'existing_template', 'template_setting_rules'));
    }

    /**
     * Store a newly created template setting in storage.
     *
     * @param  Modules\Admin\Http\Requests\TemplateSettingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeTemplateSettings(TemplateSettingRequest $request)
    {
        try {
            DB::beginTransaction();
            $template_settings_data['min_value'] = $request->get('template_min_value');
            $template_settings_data['max_value'] = $request->get('template_max_value');
            $template_settings_data['last_update_limit'] = $request->get('template_limit');
            $template_settings_data['color_id'] = $request->get('template_color');
            $obj_template_settings = TemplateSetting::updateOrCreate(array('id' => $request->get('id')), $template_settings_data);
            $template_settings_id = $obj_template_settings->id;

            $arr_template_settings_form_pos = $request->get('position');

            TemplateSettingRules::whereNull('deleted_at')->delete(); //Delete everything

            foreach ($arr_template_settings_form_pos as $key => $template_settings_form_pos) {
                if (!$request->get('rule_color')[$key] == '') {
                    TemplateSettingRules::Create([
                        'template_setting_id' => $template_settings_id,
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
