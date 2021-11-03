<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Http\Requests\ClientOnboardingRequest;
use Modules\Admin\Models\ClientOnboardingTemplateSection;
use Modules\Admin\Repositories\ClientOnboardingTemplateRepository;
use App\Services\HelperService;

class ClientOnboardingTemplateController extends Controller
{
    protected $repository;
    protected $helperService;

    /**
     * Display a listing of the resource.
     * @param ClientOnboardingTemplateRepository $clientOnboardingTemplateRepository
     * @param HelperService $helperService
     */
    public function __construct(
        ClientOnboardingTemplateRepository $clientOnboardingTemplateRepository,
        HelperService $helperService
    )
    {
        $this->repository = $clientOnboardingTemplateRepository;
        $this->helperService = $helperService;
    }

    public function index()
    {
        return view('admin::contracts.clientOnboardingTemplate');
    }

    public function getList()
    {
        $model = $this->repository->getSectionListModel();
        return datatables()->eloquent($model)
            ->editColumn('steps', function (ClientOnboardingTemplateSection $section) {
                return $section->allSteps;
            })
            ->addColumn('actions', function (ClientOnboardingTemplateSection $section) {
                $user = Auth::user();
                $actionsHtml = '';
                if ($user->can('edit_masters')) {
                    $actionsHtml .= '<a href="#" class="edit '.config('globals.editFontIcon').'" data-id='.$section->id.'></a>';
                }
                if ($user->can('lookup-remove-entries')) {
                    $actionsHtml .= '<a href="#" class="delete '.config('globals.deleteFontIcon').'" data-id='.$section->id.'></a>';

                }
                return $actionsHtml;

            })
            ->rawColumns(['actions'])
            ->addIndexColumn()
            ->toJson();
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(ClientOnboardingRequest $request)
    {
        try {
            \DB::beginTransaction();
            $section = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function getSingle($id)
    {
        return response()->json($this->repository->getSectionWithTask($id));
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
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
