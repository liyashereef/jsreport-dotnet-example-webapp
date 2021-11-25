Reference 

https://github.com/TappNetwork/laravel-uppy-s3-multipart-upload

composer require tapp/laravel-uppy-s3-multipart-upload
https://github.com/aws/aws-sdk-php/issues/2264  
$ composer require league/flysystem-aws-s3-v3:"^1.0"


Add required JS libraries
Add on your package.json file the Uppy JS libraries and AlpineJS library:

    ...
    "devDependencies": {
        "alpinejs": "^2.7.3",
        ...
    },
    "dependencies": {
        "@uppy/aws-s3-multipart": "^2.0.2",
        "@uppy/core": "^2.0.2",
        "@uppy/drag-drop": "^2.0.1",
        "@uppy/status-bar": "^2.0.1"
        ...
    }
    ...

Add in your resources/js/bootstrap.js file:

...

require('@uppy/core/dist/style.min.css')
require('@uppy/drag-drop/dist/style.min.css')
require('@uppy/status-bar/dist/style.min.css')

import Uppy from '@uppy/core'
import DragDrop from '@uppy/drag-drop'
import StatusBar from '@uppy/status-bar'
import AwsS3Multipart from '@uppy/aws-s3-multipart'

window.Uppy = Uppy
window.DragDrop = DragDrop
window.StatusBar = StatusBar
window.AwsS3Multipart = AwsS3Multipart
Add in your resources/js/app.js:

...
require('alpinejs');

Install the JS libraries:

$ npm install
$ npm run dev


Publish config file
Publish the config file with:

php artisan vendor:publish --tag=uppy-s3-multipart-upload-config
This is the contents of the published config file:

return [
    's3' => [
        'bucket' => [
            /*
             * Folder on bucket to save the file
             */
            'folder' => '',
        ],
        'presigned_url' => [
            /*
             * Expiration time of the presigned URLs
             */
            'expiry_time' => '+1 hour',
        ],
    ],
];



php artisan vendor:publish --tag=uppy-s3-multipart-upload-views


AWS S3 Setup
This package installs the AWS SDK for PHP and use Laravel's default s3 disk configuration from config/filesystems.php file.

You just have to add your S3 keys, region, and bucket using the following env vars in your .env file:

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_URL="https://s3.amazonaws.com"
AWS_POST_END_POINT="https://${AWS_BUCKET}.s3.amazonaws.com/"
To allow direct multipart uploads to your S3 bucket, you need to add some extra configuration on bucket's CORS configuration. On your AWS S3 console, select your bucket. Click on "Permissions" tab. On "CORS configuration" add the following configuration:

[
    {
        "AllowedHeaders": [
            "Authorization",
            "x-amz-date",
            "x-amz-content-sha256",
            "content-type"
        ],
        "AllowedMethods": [
            "PUT",
            "POST",
            "DELETE",
            "GET"
        ],
        "AllowedOrigins": [
            "*"
        ],
        "ExposeHeaders": [
            "ETag"
        ]
    }
]
On AllowedOrigins:

"AllowedOrigins": [
    "*"
]
You should list the URLs allowed, e.g.:

"AllowedOrigins": [
    "https://example.com"
]
https://uppy.io/docs/aws-s3-multipart/#S3-Bucket-Configuration

https://uppy.io/docs/aws-s3/#S3-Bucket-configuration


Add S3 Transfer Acceleration
To use S3 transfer acceleration, enable it by adding a AWS_USE_ACCELERATE_ENDPOINT=true env var on your .env file, and add 'use_accelerate_endpoint' => env('AWS_USE_ACCELERATE_ENDPOINT') in s3 options on your config/filesystems.php:

       's3' => [
            ...
            'use_accelerate_endpoint' => env('AWS_USE_ACCELERATE_ENDPOINT'),
        ],
Configuration
You can configure the folder to upload the files and the expiration of the presigned URLs used to upload the parts, with the config/uppy-s3-multipart-upload.php file:

return [
    's3' => [
        'bucket' => [
            /*
             * Folder on bucket to save the file
             */
            'folder' => 'videos',
        ],
        'presigned_url' => [
            /*
             * Expiration time of the presigned URLs
             */
            'expiry_time' => '+30 minutes',
        ],
    ],
];


Endpoints added
This package add the following routes:

POST    /s3/multipart
OPTIONS /s3/multipart
GET     /s3/multipart/{uploadId}
GET     /s3/multipart/{uploadId}/batch
POST    /s3/multipart/{uploadId}/complete
DELETE  /s3/multipart/{uploadId}
Usage
Add a hidden field for the uploaded file url
Add a hidden input form element on your blade template. When the upload is finished, it will receive the url of the uploaded file:

E.g.:

<input type="hidden" name="file" id="file" />
Add the uppy blade component to your blade view:
<x-input.uppy />
Passing data to the uppy blade component
Hidden field name

Use the hiddenField attribute to provide the name of the hidden field that will receive the url of uploaded file:

$hiddenField = 'image_url';
<x-input.uppy :hiddenField="$hiddenField" />
The file name will be used if none is provided.

Uppy Core Options

https://uppy.io/docs/uppy/#Options

You can pass any uppy options via options attribute:

<x-input.uppy :options="$uppyOptions" />
Uppy core options are in this format:

$uppyOptions = "{
    debug: true,
    autoProceed: true,
    allowMultipleUploads: false,
}";
Default core options if none is provided:

{
    debug: true,
    autoProceed: true,
    allowMultipleUploads: false,
}


Clear caches
Run:

php artisan optimize
php artisan view:clear
