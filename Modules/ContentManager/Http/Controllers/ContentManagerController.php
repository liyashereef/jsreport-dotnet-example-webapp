<?php

namespace Modules\ContentManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\ContentManager\Models\ManageContent;
use Modules\ContentManager\Models\ContentAttachments;
use Aws\S3\S3Client;
use S3Helper;


class ContentManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('contentmanager::index');
    }

    public function login(Request $request)
    {
        $request->session()->put('content.session', false);
        return view('content-manager-home');
    }

    public function validateLogin(Request $request)
    {
        $videoContent = ManageContent::where("key", $request->content_id)->first();
        try {
            $expiryDate = $videoContent->expiry_date;
            $expiresOn = 1;
            $past = 0;
            if ($expiryDate != null) {
                $date1 = date_create(date("Y-m-d"));
                $date2 = date_create($expiryDate);
                $date1 = new \DateTime(date("Y-m-d"));
                $date2 = new \DateTime($expiryDate);
                $diff = $date1->diff($date2);
                $expiresOn = date_diff($date1, $date2)->days;
                if ($diff->invert === 1) {
                    $past = 1;
                }
            }
            if ($videoContent && $past === 0) {
                $request->session()->put('content.session', false);
                $content['success'] = true;
                $content['message'] = 'Exist';
                $content['code'] = 200;
            } else {
                $content['success'] = false;
                $content['message'] = 'Sorry no records has been found';
                $content['code'] = 401;
            }
        } catch (\Throwable $th) {
            throw $th;
            $content['success'] = false;
            $content['message'] = 'Sorry no records has been found';
            $content['code'] = 401;
        }

        return response()->json($content);
    }

    public function listVideos(Request $request)
    {
        $contentSession = $request->session()->get('content.session');
        $key = $request->key;
        try {
            $videoContent = ManageContent::where("key", $key)->first();
            $ContentAttachments = $videoContent->ContentAttachments;
            $videoList = [];
            $attachmentList = [];
            if ($contentSession == "false") {
                return redirect('/content-manager');
            }
            foreach ($ContentAttachments as $ContentAttachment) {
                $fileprefix = "contentmanager/" . date("Y-m-d", strtotime($ContentAttachment->created_at)) . "/" . $ContentAttachment->content_id . "_";
                if ($ContentAttachment->attachment_type == 1) {

                    // Get the actual presigned-url
                    $presignedUrl = S3Helper::getPresignedUrl($videoContent->id, $fileprefix . $ContentAttachment->attachment_file);



                    $videoList[] = [
                        "content_id" => $ContentAttachment->content_id,
                        "attachment_type" => $ContentAttachment->attachment_type,
                        "attachment_file" => $presignedUrl,
                        "attachment_title" => $ContentAttachment->attachment_title,

                    ];
                } else {
                    $attachFiles = $ContentAttachment->attachment_file;

                    $presignedUrlAttachment = S3Helper::getPresignedUrl($videoContent->id, $fileprefix . $ContentAttachment->attachment_file);

                    // Get the actual presigned-url
                    $attachmentList[] = [
                        "content_id" => $ContentAttachment->content_id,
                        "attachment_type" => $ContentAttachment->attachment_type,
                        "attachment_file" => $presignedUrlAttachment,
                        "attachment_title" => $ContentAttachment->attachment_title,

                    ];
                }
            }

            return view('contentmanager::list-contents', compact(
                "videoContent",
                "attachmentList",
                "videoList"
            ));
        } catch (\Throwable $th) {
            return redirect('/content-manager');
        }
    }

    public function videoOperations(Request $request)
    {
        $return = ["code" => 200, "data" => [], "message" => "warning"];
        if ($request->operation == "getVideoDetail") {
            try {
                $videoContent = ManageContent::with("ContentAttachments")
                    ->whereHas("ContentAttachments", function ($q) {
                        return $q->whereIn("attachment_type", [1, 2]);
                    })->find($request->id);
                //dd($request->id);
                if ($videoContent->ContentAttachments) {
                    $Content = ($videoContent->ContentAttachments->first());
                    $data = [
                        "id" => $Content->id, "attachment_title" => $Content->attachment_title, "expiry_date" => $videoContent->expiry_date != "" ? date("Y-m-d", strtotime($videoContent->expiry_date)) : ""
                    ];

                    $return["data"] = $data;
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        } else if ($request->operation == "removeAttachment") {
            $id = $request->id;
            $file = $request->file;
            $fileloc = "contentmanager/" . $request->created . "/" . $request->id . "_" . $file;

            try {
                S3Helper::trashFile(
                    "awsS3Bucket",
                    $fileloc
                );
                $deleteFile = ContentAttachments::where(["content_id" => $id, "attachment_file" => $file])->delete();
                if ($deleteFile) {
                    $return["data"] = ["content_id" => $id];
                }
            } catch (\Throwable $th) {

                $return["code"] = "403";
            }
        } else if ($request->operation == "removeAll") {
            $id = $request->id;
            $videoContents = ManageContent::with("ContentAttachments")
                ->find($id);
            if (isset($videoContents->ContentAttachments)) {
                $videoContents->delete();
            }
            try {
                foreach ($videoContents->ContentAttachments as $videoContent) {

                    $file = $videoContent->attachment_file;
                    $fileloc = "contentmanager/" . date("Y-m-d", strtotime($videoContents->created_at)) . "/" . $request->id . "_" . $file;
                    try {
                        S3Helper::trashFile(
                            "awsS3Bucket",
                            $fileloc
                        );
                        $deleteFile = ContentAttachments::where(["content_id" => $id, "attachment_file" => $file])
                            ->delete();
                        if ($deleteFile) {
                            $return["data"] = ["content_id" => $id];
                        }
                    } catch (\Throwable $th) {
                        $return["code"] = "200";
                    }
                    $videoContents->delete();
                }
            } catch (\Throwable $th) {
                $videoContents->delete();
                $return["code"] = "403";
            }
        } else if ($request->operation == "refreshToken") {
            try {
                $id = $request->id;
                $key = strtoupper(md5(time()));
                ManageContent::updateOrCreate([
                    "id" => $id
                ], [
                    "key" => $key
                ]);
                if ($key != "") {
                    $return["code"] = 200;
                    $return["data"] = $key;
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        return json_encode($return, true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('contentmanager::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('contentmanager::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('contentmanager::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
