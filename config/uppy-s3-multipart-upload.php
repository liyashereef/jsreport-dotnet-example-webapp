<?php

return [
    'awsS3Bucket' => [
        'bucket' => [
            /*
             * Folder on bucket to save the file
             */
            'folder' => 'multipart',
        ],
        'presigned_url' => [
            /*
             * Expiration time of the presigned URLs
             */
            'expiry_time' => '+1 hour',
        ],
    ],
];
