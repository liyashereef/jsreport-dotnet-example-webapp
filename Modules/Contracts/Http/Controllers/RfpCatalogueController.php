<?php

namespace Modules\Contracts\Http\Controllers;

use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\RfpCatalogueGroupRepository;
use Modules\Contracts\Repositories\RfpCatalogueRepository;
use Modules\Contracts\Http\Requests\RfpCatalogueRequest;
use View;
use DB;

class RfpCatalogueController extends Controller
{
    protected $rfpCatalogueGroupRepository;
    protected $customerRepository;
    protected $rfpCatalogueRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        RfpCatalogueGroupRepository $rfpCatalogueGroupRepository,
        RfpCatalogueRepository $rfpCatalogueRepository
    )
    {
        $this->customerRepository = $customerRepository;
        $this->rfpCatalogueGroupRepository = $rfpCatalogueGroupRepository;
        $this->rfpCatalogueRepository = $rfpCatalogueRepository;
    }


    public function index()
    {
        $rfpCatalogueGroups = $this->rfpCatalogueGroupRepository->getAll();
        return view('contracts::rfpCatalogue.list',
            compact( 'rfpCatalogueGroups')
        );
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function createView($rfpCatalogueId = null)
    {
        if($rfpCatalogueId!=null)
        {
            $rfpCatalogue_data=$this->rfpCatalogueRepository->get($rfpCatalogueId);
        }
        else
        {
            $rfpCatalogue_data=null;
        }
        $projectList = $this->customerRepository->getProjectsDropdownList('all');
        $rfpCatalogueGroups = $this->rfpCatalogueGroupRepository->getAll();
        $customer_id = null;
        $user_name = Auth::user()->getFullNameAttribute();
        $attachmentModule = 'rfp-catalogue';
        return view('contracts::rfpCatalogue.create',
            compact(
                'rfpCatalogueGroups',
                'projectList',
                'customer_id',
                'user_name',
                'attachmentModule',
                'rfpCatalogue_data'
            )
        );
    }

    public function getList()
    {
       $created_user=null;
       if(\Auth::user()->can('view_rfp_catalogue'))
       {
       $created_user=null;
       }
       else if(\Auth::user()->can('create_rfp_catalogue'))
       {
        $created_user=\Auth::user()->id;
       }  
        $rfpCatalogueData = $this->rfpCatalogueRepository->getAll($created_user);
        $rfpCatalogueDataArray = $this->rfpCatalogueRepository->prepareRfpCatalogueArray($rfpCatalogueData);
        return datatables()->of($rfpCatalogueDataArray)->addIndexColumn()->toJson();
    }

    public function create(RfpCatalogueRequest $request)
    {
        try {
            DB::beginTransaction();
            $rfpCatalogue = $this->rfpCatalogueRepository->storeRfpCatalogue($request);
            if (!$rfpCatalogue) {
                throw new Exception("Save Failed");
            }
            DB::commit();
            if($request->id==null){
                $message='RFP catalog created successfully';
            }
            else
            {
                $message= 'RFP catalog updated successfully';
            }
            return response()->json(array('success' => true,'message'=>$message));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }

    }

    public function changeStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $rfpCatalogueStatus =  $this->rfpCatalogueRepository->changeStatus($request);;
            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }
}
