<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class userFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }

    public function authorize()
    {
        return auth()->check();

    }

    public function rules()
    {
        // for store useer an register
        if ($this->isMethod('post')) {
  
            $rules['email'] = 'required|string|email|max:255|unique:users';
            $rules['password'] = 'required|string|min:6';
            $rules['name'] = 'required|string|max:255';
        }
        // for update user
        elseif($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['email'] = 'nullable|string|email|max:255';
            $rules['password'] = 'nullable|string|min:6';
            $rules['name'] = 'nullable|string|max:255';
   
        }

        return $rules;
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
            'exists' => 'يجب أن يكون حقل :attribute موجود ضمن جدول الكتب',
        ];
    }
}
