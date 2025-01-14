<?php

namespace Modules\UserManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */

    public function rules(Request $request)
    {

        
    }

    // public function messages(): array
    // {

    //     return [
    //         'email.required' => 'The email field is required.',
    //         'email.email' => 'The email must be a valid email address.',
    //         'email.max' => 'The email may not be greater than 255 characters.',
    //         'email.unique' => 'The email has already been taken.', // Custom message for unique
    //         // Add other custom messages here
    //     ];
    // }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
