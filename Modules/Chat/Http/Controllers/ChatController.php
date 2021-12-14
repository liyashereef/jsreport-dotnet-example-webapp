<?php

namespace Modules\Chat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Chat\Models\Message;
use App\Events\MessageSent;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Chat\Models\ChatContacts;

class ChatController extends Controller
{
    
    protected $userModel;

    public function __construct(User $userModel,CustomerRepository $customerRepository, UserRepository $userRepository, EmployeeAllocationRepository $employeeAllocationRepository)
    {

        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->userModel = $userModel;
    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
    $user = \Auth::user();
    $contacts = ChatContacts::where('user_id',\Auth::user()->id)->pluck('contact_id')->toArray();
    if ($user->can('view_all_customer_qrcode_summary')) {
        $user_list = $this->userRepository->getUserLookup(null,['admin','super_admin'],null,true,null,true)
        ->orderBy('first_name', 'asc')->get();
        $customer_details_arr = $this->customerRepository->getProjectsDropdownList('all');
    }else if($user->can('view_allocated_customer_qrcode_summary')){
        $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
        $user_list = $this->userModel
        ->whereIn('id',$employees)->get();
        //todo:: filter users
        $customer_details_arr = $this->customerRepository->getProjectsDropdownList('allocated');
    }else{
        $user_list = [];
        $customer_details_arr = [];
    }
       return view('chat::chats', compact('user_list', 'customer_details_arr'));
     //   return view('chat::chats', ['user' => $user]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('chat::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('chat::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('chat::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    public function sendMessage(Request $request)
    {
        $message = auth()->user()->messages()->create([
            'message' => $request->message
        ]);

        broadcast(new MessageSent($message->load('user')))->toOthers();

        return ['status' => 'success'];
    }
}
