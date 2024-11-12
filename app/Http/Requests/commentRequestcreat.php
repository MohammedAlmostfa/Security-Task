<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

use Illuminate\Foundation\Http\FormRequest;

class commentRequestcreat extends FormRequest
{
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
            'body' => 'required|string'
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
            'body' => 'التعليق'
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
            'body.required' => 'حقل التعليق مطلوب.',
            'body.string' => 'حقل التعليق يجب أن يكون نصي.'
        ];
    }
}
