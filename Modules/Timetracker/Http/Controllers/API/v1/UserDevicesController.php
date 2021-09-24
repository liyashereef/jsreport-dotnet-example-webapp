<?php

namespace Modules\Timetracker\Http\Controllers\API\v1;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Timetracker\Http\Requests\UserDeviceRequest;
use Modules\Timetracker\Repositories\UserDeviceRepository;

class UserDevicesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public $successStatus = 200;
    public function __construct( HelperService $helper_service)
    {
       $this->repository = new UserDeviceRepository();
        $this->helper_service = $helper_service;
    }

    public function index()
    {

    }

    /**
     * Fetch all device details with User details.
     * @return Response
     */

    public function getAllUserDevice()
    {
        return response()->json([
            'success' => true,
            'content' => $this->repository->getAll()
        ], 200);
    }


    /**
     * get a device id by User.
     * @param  user_id
     * @return Response
     */

    public function getUserDevice(Request $request)
    {
        $user_id = $request->get("user_id");
        return response()->json([
            'success' => true,
            'content' => $this->repository->getByUserId($user_id)
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function storeFCMToken(Request $request){

        $data = $request->only(['id', 'device_type', 'device_token', 'description']);
        $data['user_id'] = Auth::id();
        //Update or create the record.
        try {
            \DB::beginTransaction();
            $result = $this->repository->store($data);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helper_service->returnFalseResponse($e));
        }
        return response()->json([
            'success' => true,
            'content' => $result
        ], 200);

    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('timetracker::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('timetracker::edit');
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
    public function destroy()
    {
    }
}
