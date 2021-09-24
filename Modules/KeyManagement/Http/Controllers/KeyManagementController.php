<?php

namespace Modules\KeyManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\KeyManagement\Http\Resources\V1\CustomerKeyDetailLookup\CustomerKeyDetailLookupResource;
use Modules\KeyManagement\Http\Resources\V1\CustomerKeyDetail\CustomerKeyDetailResource;
use Modules\KeyManagement\Http\Resources\V1\KeyLogDetail\KeyLogDetailResource;
use Modules\KeyManagement\Http\Resources\V1\IdentificationDocumentLookup\IdentificationDocumentLookupResource;
use Modules\KeyManagement\Http\Resources\V1\User\UserResource;
use Modules\KeyManagement\Models\CustomerKeyDetail;
use Modules\KeyManagement\Models\KeyLogDetail;
use Modules\Admin\Models\User;
use Modules\Admin\Models\IdentificationDocumentLookup;
use App\Repositories\AttachmentRepository;
use Modules\KeyManagement\Repositories\KeyLogRepository;
use App\Services\HelperService;
use Illuminate\Support\Facades\DB;

class KeyManagementController extends Controller
{
    public $successStatus = 200;
    protected $attachmentRepository;
    protected $customerKeyDetailRepository;

    public function __construct(
        KeyLogRepository $keyLogRepository,
        HelperService $helperService
    ) {
        $this->keyLogRepository = $keyLogRepository;
        $this->helperService = $helperService;
    }
    
    public function getKeyDetails(Request $request) {
        $input = $request->all();
        try {
            $result = CustomerKeyDetail::where('customer_id',$request['customer_id'])
                    ->when(isset($input['status']) && $input['status'] !=null, function ($q) use ($input) {
                        ($input['status'] == 'checkedout') ?  $status = CHECKEDOUT : $status = CHECKEDIN;
                        return $q->where('key_availability', $status);
                    })
                    ->orderBy('room_name')
                    ->get();
            return CustomerKeyDetailResource::collection($result);
        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function getKeyInfo(Request $request) {
        try {
            return KeyLogDetailResource::collection(
                CustomerKeyDetail::where('id',$request->key_id)
        ->with(['info'])
        ->orderBy('created_at','desc')
        ->get()
              
            );
        } catch (\Exception $e) {
            throw $e;
        }

    }

    public function getIdTypes (Request $request) {
        try {
            return IdentificationDocumentLookupResource::collection(IdentificationDocumentLookup::orderBy('name')->get());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function storeCheckout(Request $request){
        try{
            $submitCheckout = $this->keyLogRepository->storeCheckout($request);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;

        }
        return response()->json(['content' => $content], $content['code']);

    }

    public function getKeyLookup(Request $request){
        try {
            return CustomerKeyDetailLookupResource::collection(CustomerKeyDetail::where('key_availability',CHECKEDIN)
            ->where('customer_id',$request->customer_id)
            ->orderBy('room_name')
            ->get());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function user(Request $request) {
        try {
            return UserResource::collection(User::all());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function storeCheckin(Request $request){

        try{
            $submitCheckout = $this->keyLogRepository->storeCheckin($request);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);


    }

    
}
