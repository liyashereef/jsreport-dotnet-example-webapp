<?php

namespace Modules\CapacityTool\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CapacityTool\Repositories\CapacityToolEntryRepository;
use Modules\CapacityTool\Repositories\CapacityToolQuestionRepository;
use Modules\CapacityTool\Repositories\CapacityToolRepository;

class CapacityToolController extends Controller
{

    /**
     * Create new Repository instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->capacityToolQuestionRepository = new CapacityToolQuestionRepository();
        $this->capacityToolEntryRepository = new CapacityToolEntryRepository();
        $this->capacityToolRepository = new CapacityToolRepository();
        $this->helperService = new HelperService();
    }

    /**
     * Display a listing of the capacity tools.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('capacitytool::list');
    }

    /**
     * Show the form for creating a new capacity tool.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lookups = $this->capacityToolQuestionRepository->getAllParentQuestions();
        return view('capacitytool::create', compact('lookups'));
    }

    /**
     * Fetch subquestions for a given question
     *
     * @param integer question id
     * @param integer answer id
     *
     * @return json
     */
    public function subquestion(Request $request)
    {
        $question_id = $request->question_id;
        $answer_id = $request->answer_id;
        return $lookups = $this->capacityToolQuestionRepository->getSubQuestions($question_id, $answer_id);
    }

    /**
     *get capacity tools in storage.
     *
     * @param  null
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->capacityToolEntryRepository->getList())->addIndexColumn()->toJson();

    }

    /**
     * Store a newly created capacity tool in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            $result = $this->capacityToolRepository->store($request);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Show the form for editing the specified capacity tool.
     *
     * @param  int  $capacity_tool_entry_id
     * @return edit
     */
    public function edit($capacity_tool_entry_id)
    {
        $lookups = $this->capacityToolRepository->getEditCapacityTool($capacity_tool_entry_id);
       // dd($lookups);
        return view('capacitytool::edit', compact('lookups','capacity_tool_entry_id'));
    }

    /**
     * Update capacity tool.
     *
     * @param  int  $capacity_tool_entry_id
     * @return edit
     */
    public function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            $result = $this->capacityToolRepository->update($request);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }


    /**
     * Show the form for viewing the specified capacity tool.
     *
     * @param  int  $capacity_tool_entry_id
     * @return view
     */
    public function show($capacity_tool_entry_id)
    {
        $lookups = $this->capacityToolRepository->getSingleCapacityTool($capacity_tool_entry_id);
        return view('capacitytool::view', compact('lookups'));
    }




}
