<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Admin\Models\TrainingSettings;

class TrainingSettingsController extends Controller
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
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $training_settings = TrainingSettings::first();
        return view('admin::training-settings.index', compact('training_settings'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $inputs = $request->get('trainingWidgetTolerenceDays');
        $result = TrainingSettings::where('setting','trainingWidgetTolerenceDays')
        ->update(['value' => $inputs]);
        return response()->json($this->helperService->returnTrueResponse());
    }
}
