<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\TemplateQuestionsCategoryRequest;
use Modules\Admin\Models\TemplateQuestionsCategory;

class TemplateQuestionsCategoryController extends Controller
{

    /**
     * Display a listing of the Template Categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::masters.templatequestioncategory');
    }

    /**
     * Show the datatables for Template Categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of(TemplateQuestionsCategory::select([
            'id', 'description', 'average', 'safety_type',
        ])->get())->addIndexColumn()->toJson();
    }

    /**
     *  Show the details of a template Categories.
     *
     * @param Request $request
     * @return json
     */
    public function getSingle(Request $request)
    {
        return response()->json(TemplateQuestionsCategory::find($request->get('id')));
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param  Modules\Admin\Http\Requests\TemplateQuestionsCategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TemplateQuestionsCategoryRequest $request)
    {
        $templatequestionscategory = TemplateQuestionsCategory::updateOrCreate(array('id' => $request->get('id')), $request->all());
        $created = $templatequestionscategory->wasRecentlyCreated;
        $templatequestionscategory->save();
        return response()->json(array('success' => true, 'created' => $created));
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        TemplateQuestionsCategory::find($request->get('id'))->delete();
        return response()->json(array('success' => true));
    }

}
