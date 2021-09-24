<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Support\Facades\Log;
use Modules\Recruitment\Http\Requests\RecOnboardingDocumentsRequest;
use Modules\Recruitment\Repositories\RecOnboardingDocumentsRepository;
use Modules\Recruitment\Models\RecProcessTab;
use Modules\Recruitment\Models\RecOnboardingDocumentAttachments;

class RecOnboardingDocumentsController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\RecOnboardingDocumentsRepository $recOnboardingDocumentsRepository
     * @return void
     */
    public function __construct(RecOnboardingDocumentsRepository $recOnboardingDocumentsRepository,
                                   HelperService $helperService,RecProcessTab $recProcessTab)
    {
        $this->repository = $recOnboardingDocumentsRepository;
        $this->helperService = $helperService;
        $this->recProcessTab = $recProcessTab;
         
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $access_key 		= config('filesystems.disks.s3-recruitment.key');
        $secret_key 		= config('filesystems.disks.s3-recruitment.secret');
        $my_bucket			= config('filesystems.disks.s3-recruitment.bucket');
        $region				= config('filesystems.disks.s3-recruitment.region');
        $short_date         = gmdate('Ymd'); //short date
        $iso_date           = gmdate("Ymd\THis\Z"); //iso format date
        $presigned_url_expiry    = 3600; //Presigned URL validity expiration time (3600 = 1 hour)
       
        $policy = array(
        'expiration' => gmdate('Y-m-d\TG:i:s\Z', strtotime('+6 hours')),
        'conditions' => array(
            array('bucket' => $my_bucket),  
            array('acl' => 'private'),  
            array('starts-with', '$key', ''),  
            array('starts-with', '$Content-Type', ''),  
            array('success_action_status' => '201'),  
            array('x-amz-credential' => implode('/', array($access_key, $short_date, $region, 's3', 'aws4_request'))),  
            array('x-amz-algorithm' => 'AWS4-HMAC-SHA256'),  
            array('x-amz-date' => $iso_date),  
            array('x-amz-expires' => ''.$presigned_url_expiry.''),  
        ));
        
        // $success_redirect	= 'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; //URL to which the client is redirected upon success (currently self) 
        //$expiration_date    = gmdate('Y-m-d\TG:i:s\Z', strtotime('+1 hours')); //policy expiration 1 hour from now


         $policybase64 = base64_encode(json_encode($policy));	
         $kDate = hash_hmac('sha256', $short_date, 'AWS4' . $secret_key, true);
         $kRegion = hash_hmac('sha256', $region, $kDate, true);
         $kService = hash_hmac('sha256', "s3", $kRegion, true);
         $kSigning = hash_hmac('sha256', "aws4_request", $kService, true);
         $signature = hash_hmac('sha256', $policybase64 , $kSigning);

        return view('recruitment::masters.onboarding-documents', compact('policybase64','access_key','short_date','region','iso_date','presigned_url_expiry','signature','my_bucket'));
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
     * @param  App\Http\Requests\RecProcessStepsRequest $request
     * @return json
     */
    public function store(RecOnboardingDocumentsRequest $request)
    {
        try {
            \DB::beginTransaction();
            $document_details = $this->repository->save($request->all());
            if($document_details->id){
            RecOnboardingDocumentAttachments::updateOrCreate(array('id' => $request->video_id), ["document_id"=>$document_details->id,'file_name' => $request->video_file_id,'file_type'=>1]); 
            RecOnboardingDocumentAttachments::updateOrCreate(array('id' => $request->doc_id), ["document_id"=>$document_details->id,'file_name' => $request->doc_file_id,'file_type'=>2]); 
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
             Log::info($e);
            return response()->json($this->helperService->returnFalseResponse());
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
