<?php

namespace App\Repositories;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class S3AttachmentRepository
{
    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function moveFile($awsBucketReference, $sourcePrefix, $destPrefix, $fileName)
    {
        //dd($awsBucketReference);
        return Storage::disk($awsBucketReference)
            ->move($sourcePrefix . "/" . $fileName, $destPrefix . "/" . $fileName);
    }

    public function trashS3File($awsBucketReference, $sourcePrefix, $fileName)
    {
        return Storage::disk($awsBucketReference)->move($sourcePrefix . "/" . $fileName, "trash/" . $fileName);
    }
}
