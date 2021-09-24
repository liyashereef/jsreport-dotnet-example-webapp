<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\CpidFunctionRepository;
use Modules\Admin\Repositories\CpidLookupRepository;

class CpidFunctionConroller extends Controller
{
    protected $cpidFunctionRepository;
    protected $helperService;
    protected $cpidLookupRepository;

    public function __construct(
        CpidFunctionRepository $cpidFunctionRepository,
        HelperService $helperService,
        CpidLookupRepository $cpidLookupRepository
    ) {
        $this->repository = $cpidFunctionRepository;
        $this->helperService = $helperService;
        $this->cpidLookupRepository = $cpidLookupRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::masters.cpid-functions');
    }

    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $id = $request->input('id');
        //Validation
        $request->validate([
            'name' => 'required|regex:/^[a-zA-Z0-9-\.\s]+$/u|unique:cpid_functions,name,'.$id.',id,deleted_at,NULL',
            'description' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            $inputs = $request->all();
            if ($request->filled('id')) {
                $inputs['updated_by'] = Auth::id();
            } else {
                $inputs['created_by'] = Auth::id();
            }

            $result = $this->repository->store($inputs);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            //Check allocation
            if ($this->cpidLookupRepository->checkFunctionAllocation($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CPID function allocation exists. Please unallocate and try again'
                ]);
            }

            $this->repository->destroy($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }
}
