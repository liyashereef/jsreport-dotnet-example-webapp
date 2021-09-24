<?php

namespace Modules\Uniform\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Uniform\Repositories\UraSettingsRepository;

class UraSettingsController extends Controller
{
    protected $uraSettingsRepository;
    protected $helperService;

    public function __construct(
        UraSettingsRepository $uraSettingsRepository,
        HelperService $helperService
    ) {
        $this->uraSettingsRepository = $uraSettingsRepository;
        $this->helperService = $helperService;
    }

    public function index()
    {
        $upt = $this->uraSettingsRepository->getByKey('uniform-purchase-threshold', '');

        return view('uniform::admin.ura-settings', [
            'uniformPurchseThreshold' => $upt
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'uniform-purchase-threshold' => 'required|numeric|max:0'
        ], [
            'uniform-purchase-threshold.required' => 'The uniform purchase threshold field is required.',
            'uniform-purchase-threshold.max' => 'The uniform purchase threshold may not be greater than :max'
        ]);

        try {
            DB::beginTransaction();
            $this->uraSettingsRepository->store($request);
            DB::commit();
            return response()->json(['success' => 'true']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()]);
        }
    }
}
