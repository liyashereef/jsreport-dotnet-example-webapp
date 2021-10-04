<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\AttachmentZipDelete;
use App\Repositories\AttachmentRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;


use DB;

class AttachmentController extends Controller
{


    public function __construct(AttachmentRepository $attachment_repository)
    {
        $this->attachment_repository = $attachment_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $module_name = null, $custom_name = null)
    {

        try {
            DB::beginTransaction();
            $save_result = $this->attachment_repository->saveAttachmentFile($request->module, $request, null, $custom_name);
            DB::commit();
            return response()->json(array('success' => 'true','data'=>array('id'=>$save_result['file_id'])));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //v8 changes - optional parameter after required param
    //public function show($file_id, $module, $attachment = false, Request $request)
    public function show($file_id, $module, Request $request, $attachment = false)
    {
        try {
            $arr = array();
            $request->request->add(['file_id'=>$file_id,'module'=>$module]);
            $download_details_arr = $this->attachment_repository->downloadDetails($request);
            if ($attachment) {
                return response()->download($download_details_arr['path'], $download_details_arr['name']);
            } else {
                return response()->download($download_details_arr['path'], $download_details_arr['name'], [], 'inline');
            }
        } catch (\Exception $e) {
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Function to zip and download a list of attachments
     * @param $data - base64 encoded parameter in json format
     * {"module":"post-order","files":[{"attachmentId":32,"fileName":"PO Topic 3"}]}
     * @param bool persistent - default false
     * Pass true to prevent zip file created from being deleted
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getZipFile($data, $persistent = false)
    {
        $fileNameArr = array();
        $uniqueId = 1;
        $data = base64_decode($data);
        $fileObject = json_decode($data);
        $module = $fileObject->module;
        $attachmentArr = $fileObject->files;
        if (!file_exists(storage_path('app/') . $module)) {
            mkdir(storage_path('app/') . $module, 0755, true);
        }
        $zipFile = storage_path('app/'.$module)."/".$module.date("Y-m-d")."-".now()->micro.".zip"; // Name of our archive to download
        // Initializing PHP class
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($attachmentArr as $eachAttachment) {
            $downloadDetails = $this->attachment_repository
                ->downloadDetails(null, $eachAttachment->attachmentId, $module);
            $attachmentFileName = $eachAttachment->fileName.'.'.$downloadDetails['ext'];
            if (in_array($attachmentFileName, $fileNameArr)) {
                $attachmentFileName = $eachAttachment->fileName.'-'.$uniqueId++.'.'.$downloadDetails['ext'];
            }
            array_push($fileNameArr, $attachmentFileName);
            $attachmentFile = $module.'/'.$attachmentFileName;
            // Adding file: second parameter is what will the path inside of the archive
            // So it will create another folder called "storage/" inside ZIP, and put the file there.
            $zip->addFile($downloadDetails['path'], $attachmentFile);
        }
        $zip->close();
        // if the zip is not marked $persistent, it will be deleted after a day
        if (!$persistent) {
            AttachmentZipDelete::dispatch($zipFile)->delay(now()->addDay(1));
        }
        // We return the file immediately after download
        return response()->download($zipFile);
    }
}
