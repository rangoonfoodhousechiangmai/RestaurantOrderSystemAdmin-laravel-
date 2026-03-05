<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModifierStoreRequest extends FormRequest
{
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
            'eng_name' => 'required|string|max:255|unique:modifiers,eng_name',
            'mm_name' => 'required|string|max:255|unique:modifiers,eng_name',
            'type' => 'required|in:addon,flavor,protein,portion,avoid',
            'price' => 'integer|required',
            'selection_type' => 'required|in:single,multiple',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name is required.',
            'name.unique' => 'The name must be unique.',
            'type.required' => 'The type is required.',
            'type.in' => 'The type must be one of: avoid, addon, flavor.',
        ];
    }
}
