<?php

namespace Modules\LearningAndTraining\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('team_id');
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
            case 'POST':
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'name' => "required|max:250|unique:teams,name,{$id},id,deleted_at,NULL",
                    ];
                }
            default: return [];
        }

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
