<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' =>
            [
                'nullable',
                'email',
                'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                Rule::unique('companies')->ignore($this->company),
            ],
            'image' => 'nullable|mimes:jpeg,png,jpg|dimensions:min_width=100,min_height=100|max:2048',
            'website' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            
            'email.email' => 'The email must be a valid email address.',
            'email.regex' => 'The email format is invalid.',
            'email.unique' => 'The email has already been taken.',
            
            'image.image' => 'The image must be a valid image file.',
            'image.mimes' => 'The image must be in JPEG, PNG, or JPG format.',
            'image.dimensions' => 'The image dimensions must be at least 100x100 pixels.',
            'image.max' => 'The image size must not exceed 2048 KB.',
            
            'website.string' => 'The website must be a string.',
        ];
    }
}
