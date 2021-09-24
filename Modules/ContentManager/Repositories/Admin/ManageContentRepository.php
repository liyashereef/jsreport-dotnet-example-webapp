<?php

namespace Modules\ContentManager\Repositories\Admin;

use App\Helpers\S3HelperService;
use App\Services\HelperService;
use Carbon\Carbon;
use Auth;
use Modules\ContentManager\Models\ManageContent;
use Modules\ContentManager\Models\ContentAttachments;
use App\Repositories\S3AttachmentRepository;

class ManageContentRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    /**
     * @var HelperService
     */
    private $helperService;
    protected $s3AttachmentRepository;

    /**
     * Create a new  instance.
     * @param HelperService $helperService
     */
    public function __construct(
        ManageContent $manageContent,
        HelperService $helperService,
        S3AttachmentRepository $s3AttachmentRepository
    ) {
        $this->model = $manageContent;
        $this->helperService = $helperService;
        $this->s3AttachmentRepository = $s3AttachmentRepository;
    }

    public function getAll($id = null)
    {

        $contentList = $this->model->with('ContentAttachments')->get();
        return $this->prepareData($contentList);
    }

    public function prepareData($contentList)
    {

        $datatable_rows = array();

        foreach ($contentList as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            // $each_row["title"] = isset($each_list->title) ? $each_list->title : "--";
            $each_row["key"] = isset($each_list->key) ? $each_list->key : "--";
            $each_row["expiry_date"] = isset($each_list->expiry_date) ? date("d M Y", strtotime($each_list->expiry_date)) : "--";
            $date1 = date_create(date("Y-m-d"));
            $date2 = date_create($each_list->expiry_date);
            $diff = date_diff($date1, $date2);
            $each_row["days_remaining"] = isset($each_list->expiry_date) ? $diff->days : "--";

            $each_row["created_at"] = date("Y-m-d", strtotime($each_list->created_at));
            // $each_row["enabled"] = isset($each_list->enabled) ? $each_list->enabled : "--";
            $videoTitle = [];
            $videoLink = [];
            $attachmentTitle = [];
            $attachmentLink = [];
            foreach ($each_list->ContentAttachments as $contentAttachment) {
                if ($contentAttachment->attachment_type == 1) {
                    $videoTitle[] = $contentAttachment->attachment_title;
                    $videoLink[] = ($contentAttachment->attachment_file);
                }
                if ($contentAttachment->attachment_type == 2) {
                    $attachmentTitle[] = $contentAttachment->attachment_title;
                    $attachmentLink[] = ($contentAttachment->attachment_file);
                }
            }
            $each_row["video_attachment"] = isset($videoTitle) ? $videoTitle : "--";
            $each_row["video_link"] = isset($videoLink) ? $videoLink : "--";
            $each_row["normal_attachment"] = isset($attachmentTitle) ? $attachmentTitle : "--";
            $each_row["attachment_link"] = isset($attachmentLink) ? $attachmentLink : "--";

            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function save($request)
    {
        $contentId = $request->id;
        $data = [
            'key' => strtoupper(md5(time())),
            'status' => 1,
            'video' => 1,
            'expiry_date' => $request->expiry_date,
        ];
        if ($contentId > 0) {
            $contentVideoAttachment = ContentAttachments::where(
                [
                    "content_id" => $contentId,
                    "attachment_type" => 1,
                ]
            )->first();
            if ($contentVideoAttachment) {
                $attachmentFile = $contentVideoAttachment->attachment_file;
                $fileprefix = "contentmanager/" . date("Y-m-d", strtotime($contentVideoAttachment->created_at)) . "/" . $contentVideoAttachment->content_id . "_";
                if ($request->uploadedS3VideoFileName != "") {
                    try {
                        S3HelperService::moveFile(
                            "awsS3Bucket",
                            $fileprefix . $attachmentFile,
                            "trash/",
                            $fileprefix . $attachmentFile
                        );
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }
        }
        $uploadedFileIndex = $this->model->updateOrCreate(array('id' => $request['id']), $data);
        $Id = $uploadedFileIndex->id;
        $blockNo = $request->blockNo;
        if ($request->get("uploadedS3VideoFileName")) {
            $fNameArray = explode("/", $request->get("uploadedS3VideoFileName"));
            $length = count($fNameArray) - 1;
            $videofilename = $fNameArray[$length];
            if ($contentId > 0) {
                $contentVideoAttachment = ContentAttachments::where(
                    [
                        "content_id" => $contentId,
                        "attachment_type" => 1,
                    ]
                )->first();

                ContentAttachments::updateOrCreate(
                    [
                        "content_id" => $contentId,
                        "attachment_type" => 1,
                    ],
                    [
                        "content_id" => $contentId,
                        "attachment_title" => $request->title,
                        "attachment_file" => $videofilename,
                        "attachment_type" => 1,
                        "sequence" => 1,
                    ]
                );
            } else {
                ContentAttachments::create([
                    "content_id" => $Id,
                    "attachment_title" => $request->title,
                    "attachment_file" =>  $videofilename,
                    "attachment_type" => 1,
                    "sequence" => 1,
                ]);
            }

            //$this->s3AttachmentRepository->moveFile("s3", "temp", "02_2021", $s3File);
            //S3Helper::moveFile("awsS3Bucket", null, null, $request->uploadedS3VideoFileName, $Id);
            S3HelperService::setPersistent($request->uploadedS3VideoFileName, $Id);
        }
        for ($i = 1; $i < $blockNo + 1; $i++) {
            if ($request->get("uploadedS3AttachedFileName" . $i)) {
                $Title = $request->get("title_off_attachment" . $i);
                $uploadedFile = $request->get("uploadedS3AttachedFileName" . $i);

                $fNameArray = explode("/", $request->get("uploadedS3AttachedFileName" . $i));
                $length = count($fNameArray) - 1;
                $attachFilename = $fNameArray[$length];
                ContentAttachments::create([
                    "content_id" => $Id,
                    "attachment_title" => $Title,
                    "attachment_file" =>  $attachFilename,
                    "attachment_type" => 2,
                    "sequence" => 1,
                ]);
                S3HelperService::setPersistent($request->get("uploadedS3AttachedFileName" . $i), $Id);
            }
        }
        return $uploadedFileIndex;
    }

    public function get($id)
    {
        return $this->model->with(['room', 'room.customer'])->find($id);
    }
}
