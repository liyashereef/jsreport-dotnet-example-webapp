<?php

namespace Modules\Uniform\Http\Controllers\Admin;

use App\Helpers\S3HelperService;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Expense\Models\ExpenseTaxMaster;
use Modules\Expense\Models\ExpenseTaxMasterLog;
use Modules\Uniform\Http\Requests\UniformProductRequest;
use Modules\Uniform\Models\UniformProduct;
use Modules\Uniform\Models\UniformProductVariant;
use Modules\Uniform\Repositories\UniformProductRepository;

class UniformProductController extends Controller
{
    /**
     * Create Repository instance.
     * @param  Modules\Recruitment\Repositories\RecUniformItemRepository $recUniformItemRepository
     * @return void
     */
    public function __construct(
        UniformProductRepository $uniformProductRepository,
        HelperService $helperService,
        S3HelperService $s3HelperService
    ) {
        $this->repository = $uniformProductRepository;
        $this->helperService = $helperService;
        $this->s3HelperService = $s3HelperService;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('uniform::admin.uniform-products');
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
    public function addUniformProductVariants($uniformProductId = null)
    {
        $measuringPoints = [];
        $sizes = [];
        $today = date("Y-m-d");
        $uploadDet = $this->s3HelperService->S3PreUpload();
        $taxes = ExpenseTaxMasterLog::with('taxMaster')->get();

        if (isset($uniformProductId)) {
            $edit = 1;
            $productName = UniformProduct::where('id', $uniformProductId)->select('id', 'name', 'selling_price', 'vendor_price', 'tax_id')->with(['images'])->first();
            $productVariantMapping = UniformProductVariant::where('uniform_product_id', $uniformProductId)->get();
            return view('uniform::admin.uniform-product-add', compact(
                'measuringPoints',
                'sizes',
                'today',
                'uploadDet',
                'productName',
                'productVariantMapping',
                'edit',
                'taxes'
            ));
        } else {
            $edit = 0;
            $productName = [];
            $productVariantMapping = [];
            return view('uniform::admin.uniform-product-add', compact(
                'measuringPoints',
                'sizes',
                'today',
                'uploadDet',
                'productName',
                'productVariantMapping',
                'edit',
                'taxes'
            ));
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Recruitment\Http\Requests\RecUniformItemRequest $request
     * @return json
     */
    public function store(UniformProductRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            $result = ($lookup->wasRecentlyCreated);
            if (!empty($request->uploadedS3AttachedFileName) && $request->uploadedS3AttachedFileName[0] != null) {
                $this->repository->saveProductImageMapping($request->all(), $lookup->id);
            }
            if (!empty($request->variant_name) && $request->variant_name[0] != null) {
                $this->repository->saveProductVariantMapping($request->all(), $lookup->id);
            }
            \DB::commit();
            return response()->json(array('success' => true, 'result' => $result));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function getVideoUrl()
    {
        $videoPathUrl = request('filepath');
        return $this->s3HelperService->getPresignedUrl(null, $videoPathUrl);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function destroyAttachment($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->repository->deleteAttachment($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
