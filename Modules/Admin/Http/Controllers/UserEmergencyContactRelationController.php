<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\UserEmergencyContactRelationRequest;
use Modules\Admin\Repositories\UserEmergencyContactRelationRepository;

class UserEmergencyContactRelationController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\UserSalutationRepository $userSalutationRepository
     * @return void
     */
    public function __construct(UserEmergencyContactRelationRepository $userEmergencyContactRelationRepository, HelperService $helperService)
    {
        $this->repository = $userEmergencyContactRelationRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the bank listing page
     * @return Response
     */
    public function index()
    {
        return view('admin::masters.user-emergency-relations');
    }

   /**
     * Display a listing of banks
     * @return \Illuminate\Http\Response
     */
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
    public function store(UserEmergencyContactRelationRequest $request)
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
     *
     * @param  Illuminate\Http\Request $request
     * @return json
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