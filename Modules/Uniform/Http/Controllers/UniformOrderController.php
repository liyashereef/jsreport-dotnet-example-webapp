<?php

namespace Modules\Uniform\Http\Controllers;

use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Uniform\Models\UniformOrderStatus;
use Modules\Uniform\Repositories\UniformOrderRepository;

class UniformOrderController extends Controller
{
    protected $uniformOrderRepository;
    protected $helperService;

    public function __construct(
        UniformOrderRepository $uniformOrderRepository,
        HelperService $helperService
    ) {
        $this->uniformOrderRepository = $uniformOrderRepository;
        $this->helperService = $helperService;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('uniform::uniform-orders');
    }

    public function list(Request $request)
    {
        return datatables()->of($this->uniformOrderRepository->getList($request))
            ->addIndexColumn()
            ->toJson();
    }

    public function getSingle($id)
    {
        return response()->json([
            'item' => $this->uniformOrderRepository->getByID($id),
            'status' => UniformOrderStatus::all(),
            'template' => $this->uniformOrderRepository->getTemplateByOrderId($id)
        ]);
    }

    public function updateStatus(Request $request)
    {
        try {
            $statusLogValid = $this->uniformOrderRepository->validateOrderStatus($request->all());
            if ($statusLogValid == true) {
                $result = $this->uniformOrderRepository->updateStatus($request);
                return response()->json(['success' => true, 'message' => 'Order status has been updated']);
            } else {
                return response()->json(['success' => false, 'message' => 'Order status already exist']);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function getOrderItems($orderId)
    {
        return response()->json($this->uniformOrderRepository->getOrderItems($orderId));
    }

    public function getEmailTemplate($machineCode)
    {
        return response()->json($this->uniformOrderRepository->getTemplate($machineCode));
    }
}
