<?php

namespace Modules\VideoPost\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\VideoPost\Repositories\VideoPostRepository;
use Modules\Admin\Repositories\CustomerRepository;
use App\Services\HelperService;
use App\Helpers\S3HelperService;
use Modules\VideoPost\Models\VideoPost;
use Modules\VideoPost\Http\Requests\VideoPostRequest;
use phpDocumentor\Reflection\Types\Null_;

class VideoPostController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function __construct(VideoPost $videoPost,S3HelperService $s3HelperService, CustomerRepository $customerRepository, HelperService $helperService, VideoPostRepository $videoPostRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->videoPostRepository = $videoPostRepository;
        $this->helperService = $helperService;
        $this->s3HelperService = $s3HelperService;
        $this->videoPost = $videoPost;

    }


    public function index()
    {
        $today = date("Y-m-d");
        $project_list=$this->videoPostRepository->getCustomerList();
        $videoPostType = config('globals.video_post_type');
        $videoPostFileType = config('globals.video_post_file_type');
        $uploadDet=$this->s3HelperService->S3PreUpload();
        $result=null;
        $id=null;
        return view('videopost::index',compact(
            'project_list', 'uploadDet',
            'result', 'id', 'today', 'videoPostType', 'videoPostFileType'
        ));

    }

    public function editVideoPost($id=null)
    {
        $today = date("Y-m-d");
        $project_list = $this->videoPostRepository->getCustomerList();
        $videoPostType = config('globals.video_post_type');
        $videoPostFileType = config('globals.video_post_file_type');
        $uploadDet = $this->s3HelperService->S3PreUpload();
        $result = $this->videoPost->with('customer')->where('id',$id)->get();
        return view('videopost::index',compact(
            'result','project_list','uploadDet','id','today',
            'videoPostType', 'videoPostFileType'
        ));
    }


    public function storeVideoPosting(VideoPostRequest $request)
    {

        try {
            DB::beginTransaction();
            $path = substr($request->video_url, 5);
            $request->request->add(['path' => $path]);
            $data = $this->s3HelperService->setPersistent($request->video_url,null);
            $model = $this->videoPostRepository->save($request);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse($request->id));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }


    /**
     * Show the specified resource.
     * @return Response
     */
    public function showVideoPost()
    {
        $project_list = $this->videoPostRepository->getCustomerList();
        return view('videopost::view-video-post-summary',compact('project_list'));
    }

    public function getVideoPostlist(Request $request)
    {
        $customerId = request('client_id');
        $getVideoPostSummarylist = $this->videoPostRepository->getVideoPostSummarylist($customerId);
        return datatables()->of($getVideoPostSummarylist)->addIndexColumn()->toJson();
    }

    public function getVideoUrl()
    {
        $videoPathUrl = request('filepath');
        return $this->s3HelperService->getPresignedUrl(null,$videoPathUrl);
    }
    /* Remove the specified resource from storage.
    *
    * @param  $id
    * @return json
    */
   public function destroy()
   {
       try {
           DB::beginTransaction();
           $id = request('id');
           $delete = $this->videoPostRepository->delete($id);
           DB::commit();
           return response()->json($this->helperService->returnTrueResponse());
       } catch (\Exception $e) {
           DB::rollBack();
           return response()->json($this->helperService->returnFalseResponse());
       }
   }

}
