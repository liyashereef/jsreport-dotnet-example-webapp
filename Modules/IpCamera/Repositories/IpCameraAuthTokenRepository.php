<?php

namespace Modules\IpCamera\Repositories;

use App\Services\HelperService;
use Auth;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\Sdk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\IpCamera\Models\IpCamera;

class IpCameraAuthTokenRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $helperService;
    /**
     * @var CustomerEmployeeAllocationRepository
     */
    private $customerEmployeeAllocationRepository;

    public function __construct(
        HelperService $helperService,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    )
    {
        $this->helperService = $helperService;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    public function storeDynamoDb($json)
    {
        try {
            $sdk = new Sdk(array(
                'version' => 'latest',
                'region' =>  config('globals.aws_region'),
                'credentials' => [
                    'key' => config('globals.aws_key'),
                    'secret' => config('globals.aws_secret'),
                ],
            ));

            $dynamodb = $sdk->createDynamoDb();
            $marshaler = new Marshaler();

            $tableName = config('globals.ip_cam_auth_table');

            $item = $marshaler->marshalJson($json);

            $params = [
                'TableName' => $tableName,
                'Item' => $item
            ];

            try {
                $result = $dynamodb->putItem($params);

            } catch (DynamoDbException $e) {
                echo "Unable to add item:\n";
                echo $e->getMessage() . "\n";
                Log::error("AWS Credential Error" . $e);
            }

        } catch (\Exception $e) {
            Log::error("AWS Credential Error" . $e);
            return false;
        }
    }

    public function storeIpCameraAuth()
    {

    }

    /**
     * @throws \Exception
     */
    public function getIpCameraToken($id)
    {
        try {
            $hostIP = config('globals.ip_cam_ms_ip');
            $camArr = [];

            $camera = IpCamera::findOrFail($id);

            $jsonItem = array(
                "uuid" => $camera->unique_id,
                "cameraIP" => $camera->ip,
                "cameraUsername" => $camera->credential_username,
                "cameraPassword" => $camera->credential_password,
                "cameraRtspPort" => $camera->rtsp_port,
                "cameraControllerPort" => $camera->controller_port,
                "createdAt" => Carbon::create()->timestamp,
            );

            $jsonItem['id'] = Str::uuid()->toString();

            $this->storeDynamoDb(json_encode($jsonItem));
        } catch (\Exception $e) {
            throw $e;
        }

        return $jsonItem['id'];
    }

}
