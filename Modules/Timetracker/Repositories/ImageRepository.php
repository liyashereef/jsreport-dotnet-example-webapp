<?php

namespace Modules\Timetracker\Repositories;

use Config;
use Illuminate\Support\Facades\Mail;

class ImageRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */

    protected $imageUploadPath;

    /**
     * Create a new EmailRepository instance.
     *
     * @param  \App\Models\Notification $Notification
     */
    public function __construct()
    {
        $this->imageUploadPath = Config::get('globals.profilePicPath');
    }

    /**
     * Save Image
     *
     * @param type $name Description
     * @return type Description
     */
    public function saveImage($request, $shift_id)
    {
        $base64_str = $request;
        $filename = uniqid('guard_tour_');
        $image = $this->imageFromBase64($base64_str);
        $path = storage_path('app/') . config('globals.guardtour_images_folder') . "/" . $shift_id . "/" . $filename . "." . $image['extension'];
        if (!file_exists(storage_path('app/') . config('globals.guardtour_images_folder') . "/" . $shift_id)) {
            mkdir(storage_path('app/') . config('globals.guardtour_images_folder') . "/" . $shift_id, 0777, true);
        }
        $entry = file_put_contents($path, $image['image']);
        return $filename . "." . $image['extension'];

    }

    public function imageFromBase64($base64_str)
    {
        $img_arr = explode(',', $base64_str);
        $img = array();
        if (isset($img_arr[0]) && $img_arr[0] != "" && count($img_arr) > 1) {
            $image_type_arr = explode(';', $img_arr[0]);
        }
        if (isset($image_type_arr[0]) && $image_type_arr[0] != "") {
            $img['extension'] = $this->getExtension($image_type_arr[0]);
            $img['image'] = base64_decode($img_arr[1]);
        } else {
            $img['extension'] = "png";
            $img['image'] = $base64_str;
        }
        return $img;
    }

    public function getExtension($base64_type)
    {
        switch ($base64_type) {
            case "data:image/jpeg":
                return "jpg";
                break;
            case "data:image/png":
                return "png";
                break;
            default:
                return "png";
        }
    }

}
