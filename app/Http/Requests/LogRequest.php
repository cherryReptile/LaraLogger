<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'file_where_calling' => 'required|string',
            'file_where_defined' => 'sometimes|string',
            'data' => 'sometimes',
            'type' => 'sometimes|string',
            'class' => 'sometimes|string',
            'changed_properties' => 'sometimes|array',
            'all_properties' => 'sometimes|array',
            'calling_on_line' => 'required|int'
        ];
    }
}
