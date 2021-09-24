<?php

namespace Modules\ProjectManagement\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HelperService;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\ProjectManagement\Models\PmRatingTolerance;
use Modules\ProjectManagement\Http\Requests\RatingToleranceRequest;


class RatingToleranceController extends Controller
{

    protected $helperService;

    public function __construct(
        HelperService $helperService
    ) {    
        $this->helperService = $helperService;
    }
    /**
     * Display Form for adding interval.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arr_color = EmployeeRatingLookup::select('id', 'rating')->get();
        $template_setting_rules = EmployeeRatingLookup::get();
        $ratings=PmRatingTolerance::pluck('max_value','rating_id')->toArray();
        return view('projectmanagement::admin.rating-tolerance',compact('template_setting_rules','arr_color','ratings'));
    }

    /**
     * Store a newly created interval in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RatingToleranceRequest $request)
    {
      
     // $request->validate([
     //        'max_value.*' => 'required|between:1,5|distinct'
     //    ],[
     //        'max_value.*.required' => 'Maximum limit is required',
     //        'max_value.*.between' => 'Maximum limit should be maximum 3 digits.',
     //        'max_value.*.distinct' => 'Maximum limit should be distinct.'
     //    ]);
       
     try {
        \DB::beginTransaction();
        $combined=array_combine( $request->rating_id, $request->max_value );
        foreach ($combined as $key => $maximum) {
            $data['max_value']=$maximum;
             PmRatingTolerance::updateOrCreate(array('rating_id' => $key), $data);
        }
        \DB::commit();
        return response()->json($this->helperService->returnTrueResponse());
    } catch (\Exception $e) {
        \DB::rollBack();
        return response()->json($this->helperService->returnFalseResponse($e));
    }

}

}