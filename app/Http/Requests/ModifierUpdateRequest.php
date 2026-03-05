<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModifierUpdateRequest extends FormRequest
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
        $modifierId = $this->route('modifier')->id;

        return [
            'edit_eng_name' => 'required|string|max:255|unique:modifiers,eng_name,' . $modifierId,
            'edit_mm_name' => 'required|string|max:255|unique:modifiers,mm_name,' . $modifierId,
            'edit_type' => 'required|in:avoid,addon,flavor,protein,portion',
            'edit_price' => 'nullable|integer|required_if:edit_type,addon|min:0',
            'edit_selection_type' => 'required|in:single,multiple',
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
            // 'name.required' => 'The name is required.',
            // 'name.unique' => 'The name must be unique.',
            // 'type.required' => 'The type is required.',
            // 'type.in' => 'The type must be one of: avoid, addon, flavor.',
        ];
    }
}
