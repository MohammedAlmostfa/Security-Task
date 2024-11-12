<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserFormRequestCreat extends FormRequest
{

    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
          'email' =>'required|string|email|max:255|unique:users',
          'name'=>'required|string|max:255',
          'password'=>'required|string|min:6',
          'role'=>'nullable|string',
        ];
    }
    public function attributes()
    {
        return [
            'name' => 'اسم المستخدم',
            'email' => 'عنوان البريد الالكتروني',
            'password' => 'كلمة السر',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'حقل :attribute مطلوب',
            'string' => 'يجب أن يكون حقل :attribute من نوع نصي',
            'unique' => 'ان حقل ال :attribute مستعمل مسبقا',
            'email' => 'يجب أن يكون حقل :attribute صالح',
            'max' => 'عدد احرف ال :attribute يجب ان يكون أقل من 255',
            'min' => 'ان عدد احرف :attribute يجب ان يكون أكبر من 6',
        ];
    }
}
