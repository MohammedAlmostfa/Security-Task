<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class assiganTaskformrequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function attributes()
    {
        return [
            'user_id' => '  المستخدم',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'ان المستخدم مطلوب',
            'user_id.exists' => 'ان المستخدم سجب ان يكون موجود في حل المستخدمين',
        ];
    }
}
