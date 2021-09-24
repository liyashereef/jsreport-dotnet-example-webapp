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
use Modules\Admin\Repositories\PostOrderTopicRepository;
use Modules\Admin\Repositories\PostOrderGroupRepository;
use Modules\Contracts\Repositories\PostOrderRepository;
use Modules\Contracts\Http\Requests\PostOrderRequest;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use View;
use DB;

class PostOrderController extends Controller
{
    protected $postOrderTopicRepository;
    protected $postOrderGroupRepository;
    protected $customerRepository;
    protected $postOrderRepository;
    protected $customerEmployeeAllocationRepository;

    public function __construct(
        PostOrderTopicRepository $postOrderTopicRepository,
        CustomerRepository $customerRepository,
        PostOrderGroupRepository $postOrderGroupRepository,
        PostOrderRepository $postOrderRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    )
    {
        $this->postOrderTopicRepository = $postOrderTopicRepository;
        $this->customerRepository = $customerRepository;
        $this->postOrderGroupRepository = $postOrderGroupRepository;
        $this->postOrderRepository = $postOrderRepository;
        $this->customerEmployeeAllocationRepository=$customerEmployeeAllocationRepository;
    }


    public function index()
    {
        $postOrderTopics = $this->postOrderTopicRepository->getAll();
        $postOrderGroups = $this->postOrderGroupRepository->getAll();
        if (\Auth::user()->can('view_post_order')) {
            $customerList = $this->customerRepository->getProjectsDropdownList('all');
        } else if (\Auth::user()->can('view_allocated_post_order')) {
            $customerList = $this->customerRepository->getProjectsDropdownList('allocated');
        } else {
            $customerList = [];
        }
        return view('contracts::postOrder.list',
            compact('postOrderTopics', 'postOrderGroups', 'customerList')
        );
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function createView($postOrderId = null)
    {
        if($postOrderId!=null)
        {
        $postorder_data=$this->postOrderRepository->get($postOrderId);
        }
        else
        {
        $postorder_data=null;
        }
       if(\Auth::user()->can('create_post_order'))
        $projectList = $this->customerRepository->getProjectsDropdownList('all');
       else
        $projectList = $this->customerRepository->getProjectsDropdownList('allocated');
        $postOrderTopics = $this->postOrderTopicRepository->getList();
        $postOrderGroups = $this->postOrderGroupRepository->getList();
        $customer_id = null;
        $user_name = Auth::user()->getFullNameAttribute();
        $attachmentModule = 'post-order';
        return view('contracts::postOrder.create',
            compact(
                'postOrderTopics',
                'postOrderGroups',
                'projectList',
                'customer_id',
                'user_name',
                'attachmentModule',
                'postorder_data'
            )
        );
    }

     public function getList(Request $request)
    {
        $client_id = $request->get('client_id');
        $created_user=null;
        $allocated_customers=null;
       if(\Auth::user()->can('view_post_order'))
       {
       $allocated_customers=null;
       }
        else if(\Auth::user()->can('view_allocated_post_order'))
       {
       $allocated_customers=$this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
       }
       else
       {
       $created_user=\Auth::user()->id;
       }
        $postOrderData = $this->postOrderRepository->getAll($allocated_customers,$created_user,false,$client_id);
        $postOrderDataArray = $this->postOrderRepository->preparePostOrderArray($postOrderData);
        return datatables()->of($postOrderDataArray)->addIndexColumn()->toJson();
    }

    public function create(PostOrderRequest $request)
    {
        try {
            DB::beginTransaction();
            $postOrder = $this->postOrderRepository->storePostOrder($request);
            if (!$postOrder) {
                throw new Exception("Save Failed");
            }
            DB::commit();
             if($request->id==null){
              $message='Post Order created successfully';
              }
            else
              {
             $message= 'Post Order updated successfully';
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
            $postOrderStatus =  $this->postOrderRepository->changeStatus($request);;
            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }


    }



}
