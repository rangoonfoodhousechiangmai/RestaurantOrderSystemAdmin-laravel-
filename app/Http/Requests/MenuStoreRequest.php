<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuStoreRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'eng_name' => 'required|string|max:255',
            'mm_name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'eng_description' => 'nullable|string',
            'mm_description' => 'nullable|string',
            'image_path' => 'nullable|image|max:5120', // max 2MB, adjust as per requirements
            'is_available' => 'sometimes|boolean',
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
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'eng_name.required' => 'The English name is required.',
            'mm_name.required' => 'The Myanmar name is required.',
            'price.integer' => 'The price must be an integer.',
            'price.min' => 'The price must be at least 0.',
            'image_path.image' => 'The file must be an image.',
            'image_path.max' => 'Image size must not exceed 2MB.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_available' => $this->boolean('is_available'),
        ]);
    }
}
