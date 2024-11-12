<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;

class fileuploadformrequest extends FormRequest
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
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,odt,txt|max:2048'
        ];
    }
    public function attributes()
    {
        return['file' => ' الملف المرفق'];
    }
    public function messages(): array
    {
        return [
            'file.required' => 'إن حقل الملف المرفق مطلوب.',
            'file.file' => 'إن حقل الملف المرفق يجب أن يكون من نوع ملف.',
            'file.mimes' => 'نوع الملف المرفق يجب أن يكون: jpg, jpeg, png, pdf, doc, docx, odt, txt.',
            'file.max' => 'حجم الملف المرفق يجب ألا يتجاوز 2 ميجابايت.',
        ];
    }

}
