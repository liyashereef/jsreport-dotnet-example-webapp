<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Http\Requests\RecUniformItemRequest;
use Modules\Recruitment\Models\RecUniformItems;
use Modules\Recruitment\Models\RecUniformItemSizeMeasurementMapping;
use Modules\Recruitment\Models\RecUniformMeasurementPoint;
use Modules\Recruitment\Models\RecUniformSizes;
use Modules\Recruitment\Repositories\RecUniformItemRepository;

class RecUniformItemController extends Controller
{
    /**
     * Create Repository instance.
     * @param  Modules\Recruitment\Repositories\RecUniformItemRepository $recUniformItemRepository
     * @return void
     */
    public function __construct(RecUniformItemRepository $recUniformItemRepository, HelperService $helperService)
    {
        $this->repository = $recUniformItemRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('recruitment::masters.uniform-item');
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     *
     */
    public function addUniformItemSizeMeasurement($uniformItemId = null)
    {
        $measuringPoints = RecUniformMeasurementPoint::pluck('name', 'id')->toArray();
        $sizes = RecUniformSizes::get();

        if (isset($uniformItemId)) {
            $edit = 1;
            $itemName = RecUniformItems::where('id', $uniformItemId)->select('id', 'item_name')->get();
            $itemMapping = RecUniformItemSizeMeasurementMapping::with(
                'uniformItem',
                'uniformSize',
                'uniformMeasurementPoint'
            )
            ->where('item_name_id', $uniformItemId)->get()->groupBy('size_name_id')->toArray();
            $measuringIdArr = RecUniformItemSizeMeasurementMapping::where('item_name_id', $uniformItemId)->pluck('measurement_name_id', 'id')->toArray();
            return view('recruitment::masters.uniform-item-add', compact('edit', 'itemName', 'measuringPoints', 'sizes', 'itemMapping', 'measuringIdArr'));
        } else {
            $edit = 0;
            $measuringIdArr =[];
            return view('recruitment::masters.uniform-item-add', compact('edit', 'measuringPoints', 'sizes', 'measuringIdArr'));
        }
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Recruitment\Http\Requests\RecUniformItemRequest $request
     * @return json
     */
    public function store(RecUniformItemRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            $result = ($lookup->wasRecentlyCreated);
            $mappingLookup = $this->repository->saveMapping($request->all(), $lookup->id);

            \DB::commit();
            return response()->json(array('success' => true, 'result' => $result));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
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
