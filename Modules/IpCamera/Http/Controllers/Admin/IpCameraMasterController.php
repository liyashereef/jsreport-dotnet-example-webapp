<?php

namespace Modules\IpCamera\Http\Controllers\Admin;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Customer;
use Modules\IpCamera\Http\Requests\Admin\IpCameraRequest;
use Modules\IpCamera\Repositories\Admin\IpCameraRepository;
use Modules\IpCamera\Repositories\IpCameraAuthTokenRepository;

class IpCameraMasterController extends Controller
{

    public function __construct(
        IpCameraRepository $ipCameraRepository,
        Customer $customerModel,
        IpCameraAuthTokenRepository $ipCameraAuthTokenRepository,
        HelperService $helperService
    ) {
        $this->repository = $ipCameraRepository;
        $this->helperService = $helperService;
        $this->customerModel = $customerModel;
        $this->ipCameraAuthTokenRepository = $ipCameraAuthTokenRepository;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($roomId = null)
    {
        return view('ipcamera::admin.index', compact('roomId'));
    }

    public function getList($id = null)
    {
        return datatables()->of($this->repository->getAll($id))->addIndexColumn()->toJson();
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(IpCameraRequest $request)
    {
        try {
            DB::beginTransaction();
            $ipCameraStore = $this->repository->save($request->all());
            $operation = 'add';
            if (isset($request['id']) && !empty($request['id'])) {
                $operation = 'edit';
            }
            $msUpdate = $this->repository->updateMediaServer($ipCameraStore, $operation);
            if (!$msUpdate) {
                throw new \Exception('IP Camera save in media server failed');
            }
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $ipCamera = $this->repository->get($id);
            $isCameraDeleted = $this->repository->delete($id);
            if (!$isCameraDeleted) {
                throw new \Exception('IP Camera delete failed');
            }
            $msUpdate = $this->repository->updateMediaServer($ipCamera, 'delete');
            if (!$msUpdate) {
                throw new \Exception('IP Camera delete in media server failed');
            }
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function getCameraToken(Request $request)
    {
        $id = $request->cameraId;
        $apiUrl = config('globals.ip_cam_ms_ip') .  config('globals.ip_cam_view_path') . $this->ipCameraAuthTokenRepository->getIpCameraToken($id);
        return $apiUrl;

    }
}
