<?php

namespace Modules\Timetracker\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\UserRepository;
use Modules\Timetracker\Repositories\DispatchRequestRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
class MSTDispatchDashboardController extends Controller
{
    protected $dispatchRequestRepository, $emp_allocation_repo;

    public function __construct(DispatchRequestRepository $dispatchRequestRepository,
                                CustomerRepository $customerRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocation)
    {
        $this->dispatchRequestRepository = $dispatchRequestRepository;
        $this->emp_allocation_repo = new EmployeeAllocationRepository();
        $this->user_repo = new UserRepository();
        $this->customer_repo = $customerRepository;
        $this->customer_emp_allocation_repo = $customerEmployeeAllocation;
    }

    public function index()
    {
//        return view('timetracker::admin.dashboard.index',[
//            'dispatch_requests' => $this->dispatchRequestRepository->all(),
//            'count_request_closed' => count($this->dispatchRequestRepository->getAllByStatus(4)),
//            'count_request_in_progress' => count($this->dispatchRequestRepository->getAllByStatusArray([2,3])),
//            'count_request_open' => count($this->dispatchRequestRepository->getAllByStatus(1)),
//            'mst_drivers' => User::all()
//        ]);

   return view('timetracker::admin.dashboard.index',[
            'dispatch_requests' => $this->getAllDispatchRequests(),
            'count_request_closed' => count($this->getAllDispatchRequests([4])),
            'count_request_in_progress' => count($this->getAllDispatchRequests([2,3])),
            'count_request_open' => count($this->getAllDispatchRequests([1])),
            'mst_drivers' => $this->getAllUsers()
        ]);

    }

    public function getAllocatedUsers(){

/**START**
 * get All Employees allocated to Auth User and his customers.
 *return allocated user_id as an array.
 */
        $data = array();
        $data['user_ids'] = array();
        $data['customer_ids'] = array();
        $roles = $this->getMSTRelatedRoles();

        $user_lists =  $this->customer_emp_allocation_repo->getAllocatedUsers($roles,Auth::id());
        $user_ids = array();
        if(isset($user_lists) && !empty($user_lists)){
            foreach ($user_lists as $user_list){
                array_push($data['user_ids'],$user_list->id);
            }
        }
/**END** get All Employees allocated to Auth User and his customers. */

/**START**
 * get All Customers allocated to Auth User.
 *return allocated customer_id as an array.
*/
        $user = User::find(Auth::id());
        $data['customer_ids'] = $this->customer_emp_allocation_repo->getAllocatedCustomers($user);

        return $data;

    }

    public function getMSTRelatedRoles(){
        $roles = array();
        $roles_lists = $this->user_repo->getRoleLookup(null,null);
        foreach ($roles_lists as $role){
            $exist = '';
            $exist = stripos($role,'MST');
            if($exist === 0){
                array_push($roles,$role);
            }
        }
        return $roles;

    }

    public function getAllUsers(){

        $active_request_users = $this->dispatchRequestRepository->getActiveRespondUserIds();

        $user_ids = [];
        if(Auth::user()->hasAnyPermission(['view_all_mst','admin', 'super_admin'])){
            $user_ids = $active_request_users;
        }else{
            $data = $this->getAllocatedUsers();
            // $user_ids = $data['user_ids'];
            $user_ids=array_intersect($active_request_users,$data['user_ids']);
        }

        return $this->user_repo->getAllByUserIds($user_ids,1);
    }

    public function getAllDispatchRequests($status = null){
        $user_ids = [];

        if(Auth::user()->hasAnyPermission(['view_all_mst','admin', 'super_admin'])){

        }else{
            $data = $this->getAllocatedUsers();
            $user_ids = $data['user_ids'];
        }

        return $this->dispatchRequestRepository->getAllByUserIds($user_ids,$status);
    }
}
