<?php

namespace Modules\Admin\Http\Controllers\IdsServices;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use  Modules\Admin\Http\Requests\IdsNoshowSettingsRequest;
use Modules\Admin\Repositories\IdsNoshowSettingsRepository;

class IdsNoshowSettingsController extends Controller
{
    protected $repository;
    protected $helperService;

    public function __construct(
        HelperService $helperService,
        IdsNoshowSettingsRepository $repository
    ){
        $this->repository = $repository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $noshow = $this->repository->getLatest();
        return view('admin::ids-scheduling.noshow-settings',compact('noshow'));
    }


    /**
     * Store a newly created resource in storage.
     * @param  IdsNoshowSettingsRequest $request
     * @return Response
     */
    public function store(IdsNoshowSettingsRequest $request)
    {
        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            if($request->filled('id')){
                $inputs['updated_by']= \Auth::id();
            }else{
                $inputs['created_by']= \Auth::id();
            }
            $this->repository->store($inputs);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
