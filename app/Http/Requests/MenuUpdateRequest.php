<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuUpdateRequest extends FormRequest
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
            'edit_category_id' => 'required|exists:categories,id',
            'edit_eng_name' => 'required|string|max:255',
            'edit_mm_name' => 'required|string|max:255',
            'edit_price' => 'required|integer|min:0',
            'edit_eng_description' => 'nullable|string',
            'edit_mm_description' => 'nullable|string',
            'edit_image_path' => 'nullable|image|max:5120', // max 2MB, adjust as per requirements
            'edit_is_available' => 'sometimes|boolean',
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
            'edit_category_id.required' => 'Category is required.',
            'edit_category_id.exists' => 'The selected category is invalid.',
            'edit_eng_name.required' => 'The English name is required.',
            'edit_mm_name.required' => 'The Myanmar name is required.',
            'edit_price.integer' => 'The price must be an integer.',
            'edit_price.min' => 'The price must be at least 0.',
            'edit_image_path.image' => 'The file must be an image.',
            'edit_image_path.max' => 'Image size must not exceed 2MB.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'edit_is_available' => $this->boolean('edit_is_available'),
        ]);
    }
}
