<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Admin\Http\Requests\UserSkillsOptionRequest;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\UserSkillOptionRepository;
use Modules\Admin\Repositories\UserSkillsLookupRepository;
use App\Services\HelperService;
use Modules\Admin\Models\UserSkillOption;
use Modules\Admin\Models\UserSkillOptionValue;
use Modules\Admin\Models\UserSkillOptionAllocation;
use Modules\Admin\Repositories\UserSkillOptionValueRepository;
use Modules\Admin\Http\Requests\UserSkillOptionRequest;

class UserSkillOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function __construct(UserSkillOptionRepository $userSkillOptionRepository, HelperService $helperService, UserSkillsLookupRepository $userSkillsLookupRepository, UserSkillOptionValueRepository $userSkillOptionValueRepository)
    {
        $this->repository = $userSkillOptionRepository;
        $this->helperService = $helperService;
        $this->userSkillsLookupRepository=$userSkillsLookupRepository;
        $this->userSkillOptionValueRepository=$userSkillOptionValueRepository;
    }

    public function index()
    {
        return view('admin::user-skills.user-skill-option');
    }

    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single bank
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created bank in storage.
     *
     * @param  App\Http\Requests\UserSalutationRequest $request
     * @return json
     */
    public function store(UserSkillOptionRequest $request)
    {
        
        try {
            \DB::beginTransaction();
            $user_skill_options=['name'=>$request->option_name];
            $userSkillOptionEntry=UserSkillOption::updateOrCreate(array('id' => $request->id), $user_skill_options);

            for ($i = 0; $i < count($request->get('option_value')); $i++) {
                $UserSkillOptionValueEntry = [
                    'user_skill_option_id' =>$userSkillOptionEntry->id,
                    'name' => $request->option_value[$i],
                    'order' => $request->order[$i],
                ];
        
                UserSkillOptionValue::updateOrCreate(array('id' => $request->value_id[$i]), $UserSkillOptionValueEntry);
            }
            for ($i = 0; $i < count($request->get('skill_id')); $i++) {
                $UserSkillOptionAllocationEntry = [
                    'user_skill_option_id' =>$userSkillOptionEntry->id,
                    'user_skill_id' => $request->skill_id[$i],
                ];
                UserSkillOptionAllocation::updateOrCreate(array('user_skill_option_id' => $userSkillOptionEntry->id,'user_skill_id'=>$request->skill_id[$i]), $UserSkillOptionAllocationEntry);
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function addOption($option_id = null)
    {
        $skillOptionName='';
        $userSkillArr=array();
        if (isset($option_id)) {
            $userSkillOptionAllocation= UserSkillOptionAllocation::where('user_skill_option_id', $option_id)->with('skillOption')->get();
            $userSkillArr=$userSkillOptionAllocation->pluck('user_skill_id')->toArray();
            $skillOptionName=data_get($userSkillOptionAllocation, '*.skillOption.name')[0];
            $userSkillOptionValue= UserSkillOptionValue::where('user_skill_option_id', $option_id)->get();
        }
        $skill=$this->userSkillsLookupRepository->getAll();
        return view('admin::user-skills.user-skill-option-add', compact('skill', 'skillOptionName', 'userSkillArr', 'userSkillOptionValue', 'option_id'));
    }
 
    public function destroyOptionValue($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete =$this->userSkillOptionValueRepository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    public function getSkillValue($id)
    {
        try {
            \DB::beginTransaction();
            $userSkillOptionAllocation= UserSkillOptionAllocation::where('user_skill_id', $id)->with('skillOption.skillOptionValues')->get();
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($userSkillOptionAllocation));
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
