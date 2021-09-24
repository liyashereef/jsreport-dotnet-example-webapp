<?php

namespace Modules\ClientApp\Http\Controllers;


use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;

use Modules\ClientApp\Http\Resources\V1\Visitor\VisitorLogDetailsResource;
use Modules\Client\Models\VisitorLogDetails;

class VisitorLogController extends Controller
{
    protected $userRepository;
    protected $loginCollection;
    protected $helperService;
    protected $attachmentRepository;

    public function __construct(
        HelperService $helperService
    ) {
        
        $this->helperService = $helperService;
    }


    public function visitorLogDetails(Request $request) {
        try {
            $last24h = \Carbon::now()->subDay(1);
            return VisitorLogDetailsResource::collection(VisitorLogDetails::where('created_at', '>=', $last24h)->where('customer_id',$request->customerId)->get());
        } catch (\Exception $e) {
            throw $e;
        }
    }

   
}
