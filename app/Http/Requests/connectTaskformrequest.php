<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class connectTaskFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
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
            'depend_on_task_id' => 'required|exists:tasks,id',
            'task_id' => 'required|exists:tasks,id|unique:task_dependencies,task_id,NULL,id,task_depend_on,' . $this->depend_on_task_id,
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'depend_on_task_id' => 'التاسك المعتمد عليه',
            'task_id' => 'التاسك',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'depend_on_task_id.required' => 'حقل التاسك المعتمد عليه مطلوب.',
            'depend_on_task_id.exists' => 'حقل التاسك المعتمد عليه يجب أن يكون موجودًا في جدول المهام.',
            'task_id.required' => 'حقل التاسك مطلوب.',
            'task_id.exists' => 'حقل التاسك يجب أن يكون موجودًا في جدول المهام.',
            'task_id.unique' => 'هذا التاسك تم ربطه مسبقًا بالتاسك المعتمد عليه.',
        ];
    }
}
