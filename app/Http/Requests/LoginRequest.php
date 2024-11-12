<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }
    //**________________________________________________________________________________________________

    public function authorize()
    {
        return true;
    }
    //**________________________________________________________________________________________________

    public function rules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }
    //**________________________________________________________________________________________________

    public function attributes()
    {
        return [
          'email' => 'عنوان البريد الالكتروني',
            'password' => 'كلمة السر',
        ];

    }
    //**________________________________________________________________________________________________

    public function messages()
    {
        return [
            'required' => 'حقل :attribute مطلوب',
            'string' => 'يجب أن يكون حقل :attribute من نوع نصي',
            'email' => 'يجب أن يكون حقل :attributeصالح',
        ];
    }

}
