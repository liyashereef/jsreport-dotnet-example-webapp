<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\TemplateRequest;
use Modules\Admin\Models\AnswerTypeLookup;
use Modules\Admin\Models\Template;
use Modules\Admin\Models\TemplateForm;
use Modules\Admin\Models\TemplateQuestionsCategory;

class TemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::template.template');
    }

    /**
     * Show the datatables for templates.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of(Template::select(['id',
            'template_name',
            DB::raw('DATE_FORMAT(start_date, "%Y-%m-%d") as start_date'),
            DB::raw('DATE_FORMAT(end_date, "%Y-%m-%d") as end_date'),
            'created_at', 'updated_at'])->withCount('templateForm')->get())->addIndexColumn()->toJson();
    }

    /**
     * Controller for GET method of add and edit of template
     *  - Edit fetch the form content and populate select values
     *  - Add populates select values
     * @param int $template_id template id for edit
     */
    public function addTemplate($template_id = null)
    {
        if (isset($template_id)) {
            $question_categories = TemplateQuestionsCategory::select('id', 'description', 'average')->get();
            $template_position_arr = TemplateForm::where('template_id', $template_id)->pluck('position');
            $last_template_position = $template_position_arr[count($template_position_arr) - 1];
            $answer_type = AnswerTypeLookup::select('id', 'answer_type')->get();
            $template_obj = Template::with('templateForm')->where('id', $template_id)->get();
            $template_arr = $template_obj->toArray()[0];
            $template_form_arr = $template_arr['template_form'];
            return view('admin::template.add', compact('question_categories', 'answer_type', 'template_arr', 'template_form_arr', 'template_position_arr', 'last_template_position'));
        } else {
            $question_categories = TemplateQuestionsCategory::select('id', 'description', 'average')->get();
            $answer_type = AnswerTypeLookup::select('id', 'answer_type')->get();
            return view('admin::template.add', compact('question_categories', 'answer_type'));
        }
    }

    /**
     * Function to save template
     * @param Request $request
     * @return json
     */
    public function storeTemplate(TemplateRequest $request)
    {
        try {
            DB::beginTransaction();
            //$id = $request->get('id');
            $template_data['template_name'] = $request->get('template_name');
            $template_data['template_description'] = $request->get('template_description');
            $template_data['start_date'] = $request->get('start_date');
            $template_data['end_date'] = $request->get('end_date');
            $template_data['active'] = true;
            $template_data['created_by'] = Auth::user()->id;
            $template_data['updated_by'] = Auth::user()->id;
            $obj_template = Template::updateOrCreate(array('id' => $request->get('id')), $template_data);
            $template_id = $obj_template->id;

            $arr_template_form_pos = $request->get('position');
            TemplateForm::where(['template_id' => $request->get('id')])->whereNotIn('position', $arr_template_form_pos)->delete();
            foreach ($arr_template_form_pos as $key => $template_form_pos) {
                $templateFrom = TemplateForm::updateOrCreate([
                    'id' => $request->get('element_id')[$key]],
                    [
                        'template_id' => $template_id,
                        'position' => $template_form_pos,
                        'question_category_id' => ($request->get('question_type')[$key] == "NA") ? null : $request->get('question_type')[$key],
                        'parent_position' => ($request->get('parent_question')[$key] == 'NA') ? null : $request->get('parent_question')[$key],
                        'question_text' => $request->get('question_text')[$key],
                        'answer_type_id' => $request->get('answer_type')[$key],
                        'multi_answer' => (isset($request->get('multiple_answers')[$template_form_pos])) ? 1 : 0,
                        'show_if_yes' => (isset($request->get('show_if')[$template_form_pos]) ? (int) ($request->get('show_if')[$template_form_pos]) : null),
                        'score_yes' => ($request->get('yes_value')[$key]) ?? null,
                        'score_no' => ($request->get('no_value')[$key]) ?? null,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]
                );
            }
            DB::commit();
            $result = ($obj_template->wasRecentlyCreated && $templateFrom->wasRecentlyCreated);
            return response()->json(array('success' => 'true', 'result' => $result));
        } catch (\Exception $e) {
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
        Template::find($request->get('id'))->delete();
        return response()->json(array('success' => true));
    }

}
