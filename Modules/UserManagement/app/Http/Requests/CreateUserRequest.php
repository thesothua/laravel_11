<?php

namespace Modules\UserManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(Request $request): array
    {


        //  // Validate request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            "roles" => 'required|array',

            // Optional profile fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'profile_image' => 'nullable|image|max:2048', // Example for profile image
            // Optional address fields
            'addresses' => 'array',
            'addresses.*.address_line_1' => 'required_with:addresses|string|max:255',
            'addresses.*.address_line_2' => 'nullable|string|max:255',
            'addresses.*.city' => 'required_with:addresses|string|max:100',
            'addresses.*.state' => 'required_with:addresses|string|max:100',
            'addresses.*.country' => 'required_with:addresses|string|max:100',
            'addresses.*.postal_code' => 'required_with:addresses|string|max:20',
            'addresses.*.type' => 'required_with:addresses|in:home,work,billing,shipping',
        ]);

        return $validated;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
