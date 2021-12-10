<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\VisitorLogCustomerTemplateAllocation;
use Modules\Admin\Models\VisitorLogTemplateFeature;
use Modules\Admin\Models\VisitorLogTemplateFields;
use Modules\Admin\Models\VisitorLogTemplates;
use Modules\Admin\Repositories\VisitorLogTemplateRepository;
use Modules\VisitorLog\Repositories\VisitorLogDeviceRepository;
use Validator;

class VisitorLogTemplateController extends Controller
{

    protected $repository;

    public function __construct(
        VisitorLogTemplateRepository $visitorLogTemplateRepository,
        VisitorLogDeviceRepository $visitorLogDeviceRepository
    ){
        $this->repository = $visitorLogTemplateRepository;
        $this->visitorLogDeviceRepository = $visitorLogDeviceRepository;
    }

    /**
     */
    public function index()
    {
        return view('admin::client.visitorlog-template');
    }

    /**
     * Show the datatables for templates.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of(VisitorLogTemplates::select([
            'id',
            'template_name',
            'created_at', 'updated_at'
        ])->get())->addIndexColumn()->toJson();
    }

    public function addTemplate($template_id = null)
    {

        if (isset($template_id)) {

            $template = VisitorLogTemplates::select('id', 'template_name')->where('id', $template_id)->get();
            $basic_fields = VisitorLogTemplateFields::select('id', 'fieldname', 'field_displayname', 'field_type', 'is_required', 'is_visible', 'is_custom')->where('template_id', $template_id)->get();
            $features = VisitorLogTemplateFeature::select('id', 'feature_name', 'feature_displayname', 'is_required', 'is_visible')->where('template_id', $template_id)->get();

            // $visitor_type = AnswerTypeLookup::select('id', 'answer_type')->get();
            return view('admin::client.visitorlog-add-template', compact('template', 'basic_fields', 'features'));
        } else {
            $basic_fields = $this->repository->getBasicTemplateFields();
            $features = $this->repository->getTemplateFeatures();
            // $visitor_type = AnswerTypeLookup::select('id', 'answer_type')->get();
            return view('admin::client.visitorlog-add-template', compact('basic_fields', 'features'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            //Validating request
            $id = $request->get('id');
            $rule = array(
                'template_name' => 'bail|unique:visitor_log_templates,template_name,' . $id . ',id,deleted_at,NULL',
            );
            $validator = Validator::make($request->all(), $rule);

            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json(array('success' => false, 'error' => $messages));
            }
            $template_name = $request->get('template_name');
            $template = VisitorLogTemplates::updateOrCreate(array('id' => $request->get('id')), ['template_name' => $template_name]);
            $templateId = $template->id;

            //Processing template store
            if ($templateId) {
                VisitorLogTemplateFields::where(['template_id' => $templateId])->delete();
                foreach ($request->get('uid') as  $uid) {
                    $rfn = $request->get('fieldname_' . $uid);

                    $computedFieldName = ($rfn == null || empty($rfn))
                    ? snake_case($request->get('field_displayname_' . $uid))
                    :$rfn;

                    VisitorLogTemplateFields::create(
                        [
                            'template_id' => $templateId,
                            'fieldname' => $computedFieldName,
                            'field_displayname' => $request->get('field_displayname_' . $uid),
                            'field_type' => $request->get('field_type_' . $uid),
                            'is_required' => $request->get('is_required_' . $uid) == null ? 0 : 1,
                            'is_visible' => $request->get('is_visible_' . $uid) == null ?  0 : 1,
                            'is_custom' => $request->get('is_custom_' . $uid)
                        ]
                    );
                }

                VisitorLogTemplateFeature::where(['template_id' => $templateId])->delete();
                $feature_count = $request->get('feature_count');
                for ($j = 1; $j <= $feature_count; $j++) {
                    VisitorLogTemplateFeature::create(
                        [
                            'template_id' => $templateId,
                            'feature_name' => $request->get($j . '_feature_name'),
                            'feature_displayname' => $request->get($j . '_feature_displayname'),
                            'is_required' => ($request->get($j . '_feature_is_required') != null) ? 1 : 0,
                            'is_visible' => ($request->get($j . '_feature_is_visible') != null) ? 1 : 0
                        ]
                    );
                }

                $result = ($template->wasRecentlyCreated);
                //Triger pusher on edit.
                if($request->get('id')){
                    $this->visitorLogDeviceRepository->getByTemplateId($request->get('id'));
                }
            }
            DB::commit();
            return response()->json(array('success' => 'true', 'result' => $result));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Function to delete template
     * @param Request $request
     * @return json
     */
    public function destroy(Request $request)
    {
        VisitorLogCustomerTemplateAllocation::where('template_id', $request->get('id'))->delete();
        VisitorLogTemplates::find($request->get('id'))->delete();
        return response()->json(array('success' => true));
    }
}
