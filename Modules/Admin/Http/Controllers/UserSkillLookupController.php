<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Admin\Http\Requests\UserSkillsRequest;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\UserSkillsLookupRepository;
use App\Services\HelperService;

class UserSkillLookupController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function __construct(UserSkillsLookupRepository $userSkillsLookupRepository, HelperService $helperService)
    {
        $this->repository = $userSkillsLookupRepository;
        $this->helperService = $helperService;
    }

    public function index()
    {
        return view('admin::user-skills.index');
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
    public function store(UserSkillsRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
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
}
