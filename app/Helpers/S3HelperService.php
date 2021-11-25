<?php

namespace App\Helpers;
// Your helpers namespace
use Auth;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;

class S3HelperService
{
    public static function setPersistent($fileObjectPath = null, $incrementId = null)
    {
        $awsBucketReference = "awsS3Bucket";
        $fileArray = explode("/", $fileObjectPath);
        $lastIndex = count($fileArray) - 1;

        $destPrefix = "";
        for ($i = 1; $i < $lastIndex; $i++) {
            $destPrefix .= $fileArray[$i] . "/";
        }
        $moveFileName = ($incrementId > 0 ? $incrementId . "_" : "") . $fileArray[$lastIndex];
        
        try {
            S3HelperService::moveFile($awsBucketReference, $moveFileName, $fileObjectPath, $destPrefix, null);
            //dd($awsBucketReference, $fileObjectPath, $destPrefix, $moveFileName);
        } catch (\Throwable $th) {
            \Log::info("S3 helper Service".$th);
            return null;
        }
    }

     //v8 changes - optional parameter after required param
    //public static function moveFile($awsBucketReference, $sourceFile = null, $destPrefix = null, $fileName)
    public static function moveFile($awsBucketReference, $fileName, $sourceFile = null, $destPrefix = null)
    {
        try {
            return Storage::disk($awsBucketReference)->move($sourceFile, $destPrefix . $fileName);
        } catch (\Throwable $th) {
            \Log::info("S3 helper Service".$th);
            return null;
        }
    }

    public static function trashFile($awsBucketReference, $sourceFile): ?object
    {

        $destPrefix = "trash/";
        try {
            return Storage::disk($awsBucketReference)->move($sourceFile, $destPrefix . $sourceFile);
        } catch (\Throwable $th) {
            \Log::info("S3 helper Service".$th);

            return null;
        }
    }

    public static function getPresignedUrl($videoContentid, $ContentAttachmentAttachmentFile)
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.awsS3Bucket.region'),
            'credentials' => [
                'key' => config('filesystems.disks.awsS3Bucket.key'),
                'secret' => config('filesystems.disks.awsS3Bucket.secret'),
            ]
        ]);
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => config('filesystems.disks.awsS3Bucket.bucket'),
            'Key' =>   $ContentAttachmentAttachmentFile
        ]);

        $request = $s3Client->createPresignedRequest($cmd, '+75 minutes');

        // Get the actual presigned-url
        $presignedUrl = (string)$request->getUri();
        return $presignedUrl;
    }

    /**
     * @param String file with Folder path
     */
    public static function getSignedUrl($attachedFile, $expiryInMinutes = 5)
    {
        $expiryInMinutesString = '+' . $expiryInMinutes . ' minutes';
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.awsS3Bucket.region'),
            'credentials' => [
                'key' => config('filesystems.disks.awsS3Bucket.key'),
                'secret' => config('filesystems.disks.awsS3Bucket.secret'),
            ]
        ]);
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => config('filesystems.disks.awsS3Bucket.bucket'),
            'Key' =>   $attachedFile
        ]);

        $request = $s3Client->createPresignedRequest($cmd, $expiryInMinutesString);

        // Get the actual presigned-url
        $presignedUrl = (string)$request->getUri();
        return $presignedUrl;
    }

    public function S3PreUpload($urlExpiry = null, $fileName = null, $prefix = null, $recBucket = false)
    {
        $access_key = config('filesystems.disks.awsS3Bucket.key');
        $secret_key = config('filesystems.disks.awsS3Bucket.secret');
        $my_bucket = $recBucket ? config('filesystems.disks.s3-recruitment.bucket') : config('filesystems.disks.awsS3Bucket.bucket');
        $region = config('filesystems.disks.awsS3Bucket.region');
        $short_date = gmdate('Ymd'); //short date
        $iso_date = gmdate("Ymd\THis\Z"); //iso format date
        $presigned_url_expiry = $urlExpiry ?? 3600; //Presigned URL validity expiration time (3600 = 1 hour)

        $policy = array(
            'expiration' => gmdate('Y-m-d\TG:i:s\Z', strtotime('+' . $presigned_url_expiry . ' seconds')),
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
        $policybase64 = base64_encode(json_encode($policy));
        $kDate = hash_hmac('sha256', $short_date, 'AWS4' . $secret_key, true);
        $kRegion = hash_hmac('sha256', $region, $kDate, true);
        $kService = hash_hmac('sha256', "s3", $kRegion, true);
        $kSigning = hash_hmac('sha256', "aws4_request", $kService, true);
        $signature = hash_hmac('sha256', $policybase64, $kSigning);
        $arr = array(
            'policybase64' => $policybase64,
            'access_key' => $access_key,
            'short_date' => $short_date,
            'region' => $region,
            'iso_date' => $iso_date,
            'presigned_url_expiry' => $presigned_url_expiry,
            'signature' => $signature,
            'my_bucket' => $my_bucket,
            'amz_credentials' => $amz_credentials,
            'url' => "https://" . $my_bucket . ".s3-" . $region . ".amazonaws.com",
            'key' => $this->getS3Key($fileName, $prefix)
        );
        return $arr;
    }

    /**
     * @param $fileName
     * @param null $prefix
     * @param bool $retainPrefix
     * @return string
     */
    public function getS3Key($fileName, $prefix = '', $retainPrefix = true): string
    {
        $urlPrefix = $prefix;
        $key = "temp/";
        $fileNameArr = explode(".", $fileName);
        $fileNameLength = sizeof($fileNameArr);
        $fileExt = $fileNameArr[$fileNameLength - 1];
        $fileHash = md5('prefix' . gmdate('YmdHis'));
        if (!$retainPrefix) {
            $urlPrefix = $urlPrefix . "/" . substr($fileHash, 0, 4);
        }
        switch ($prefix) {
            case 'uof':
            case 'ots':
            case 'document':
            default:
                $recUserId = \Auth::user()->id;
                $key .= $recUserId . '/' . $urlPrefix . "/" . $fileHash . "." . $fileExt;
                break;
        }
        return $key;
    }
}
