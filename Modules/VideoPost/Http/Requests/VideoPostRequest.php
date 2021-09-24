<?php


namespace Modules\VideoPost\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class VideoPostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'customer_id' => "bail|required",
            'file_name' => "bail|required|max:50",
            'description' => "max:500",
            'video_url'  => "bail|required",
        ];
    }

       /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
            'customer_id.required' => 'Please select customer',
            'file_name.required' => 'File name is required.',
            'file_name.max' => 'File name should not exceed 50 characters.',
            'description.max' => 'Description should not exceed 500 characters.',
            'video_url.required' => 'Video file is required.',


            ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
