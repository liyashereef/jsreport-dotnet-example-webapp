<?php

namespace Modules\ContentManager\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\ContentManager\Repositories\Admin\ManageContentRepository;
use App\Services\HelperService;
use Illuminate\Support\Facades\DB;
use Modules\ContentManager\Http\Requests\Admin\ManageContentRequest;

class ManageContentController extends Controller
{

    public function __construct(
        ManageContentRepository $manageContentRepository,
        HelperService $helperService
    ) {
        $this->manageContentRepository = $manageContentRepository;
        $this->helperService = $helperService;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        $access_key =  config('filesystems.disks.awsS3Bucket.key');
        $secret_key = config('filesystems.disks.awsS3Bucket.secret');
        $my_bucket = config('filesystems.disks.awsS3Bucket.bucket');
        $region = config('filesystems.disks.awsS3Bucket.region');
        $short_date = gmdate('Ymd'); //short date
        $iso_date = gmdate("Ymd\THis\Z"); //iso format date
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
                array('x-amz-expires' => '' . $presigned_url_expiry . ''),
            )
        );
        $amz_credentials = $access_key . '/' . $short_date . '/' . $region . '/s3/aws4_request';
        // $success_redirect	= 'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; //URL to which the client is redirected upon success (currently self) 
        //$expiration_date    = gmdate('Y-m-d\TG:i:s\Z', strtotime('+1 hours')); //policy expiration 1 hour from now

        $today = date("Y-m-d");
        $policybase64 = base64_encode(json_encode($policy));
        $kDate = hash_hmac('sha256', $short_date, 'AWS4' . $secret_key, true);
        $kRegion = hash_hmac('sha256', $region, $kDate, true);
        $kService = hash_hmac('sha256', "s3", $kRegion, true);
        $kSigning = hash_hmac('sha256', "aws4_request", $kService, true);
        $signature = hash_hmac('sha256', $policybase64, $kSigning);
        $url = "https://" . $my_bucket . ".s3-" . $region . ".amazonaws.com";
        return view('contentmanager::admin.index', compact(
            'url',
            'policybase64',
            'access_key',
            'short_date',
            'region',
            'iso_date',
            'presigned_url_expiry',
            'signature',
            'my_bucket',
            'amz_credentials',
            'today'
        ));
    }

    public function s3index(Request $request){
        return view("contentmanager::admin.s3index");
    }

    public function s3uploader(Request $request){
        $blockId=intval($request->id);
        return view("contentmanager::admin.s3uploader",compact("blockId"));
    }

    public function getList($id = null)
    {
        return datatables()->of($this->manageContentRepository->getAll($id))->addIndexColumn()->toJson();
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
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //ManageContentRequest
        try {
            DB::beginTransaction();
            $this->manageContentRepository->save($request);
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
            $this->repository->delete($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
