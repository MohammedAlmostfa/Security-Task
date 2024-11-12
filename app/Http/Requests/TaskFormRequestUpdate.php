<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

use Illuminate\Foundation\Http\FormRequest;

class TaskFormRequestUpdate extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:20|min:5',
            'description' => 'nullable|string|max:30|min:5',
            'type' => 'nullable|string',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|integer|exists:users,id',
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
            'assigned_to' => 'تم تعيينه إلى',
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
