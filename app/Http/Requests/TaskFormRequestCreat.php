<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class TaskFormRequestCreat extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'title' => 'required|string|max:20|min:5',
            'description' => 'nullable|string|max:30|min:5',
            'type' => 'required|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'due_date' => 'required|date',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'title' => 'اسم المستخدم',
            'description' => 'الوصف',
            'type' => 'نوع',
            'status' => 'الحالة',
            'priority' => 'أولوية',
            'due_date' => 'تاريخ التسليم',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'required' => 'حقل :attribute مطلوب.',
            'string' => 'حقل :attribute يجب أن يكون نصي.',
            'max' => 'حقل :attribute يجب أن يكون أقل من :max حرف.',
            'min' => 'حقل :attribute يجب أن يكون أكبر من :min حرف.',
            'date' => 'حقل :attribute يجب أن يكون تاريخ صالح.',
            'integer' => 'حقل :attribute يجب أن يكون عدد صحيح.',
            'exists' => 'حقل :attribute المحدد غير موجود.',
        ];
    }
}
