<?php

namespace Modules\Auth\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class RegisterDTO extends ValidatedDTO
{
    public string $name;
    public string $email;
    public string $password;
    public string $password_confirmation;

    /**
     * Define the validation rules for the DTO.
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Define default values for the DTO properties.
     */
    protected function defaults(): array
    {
        return [];
    }

    /**
     * Define custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'The password field is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    public function casts(): array
    {
        return [];
    }
}
