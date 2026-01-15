<?php

namespace Modules\Auth\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ResetPasswordDTO extends ValidatedDTO
{
    public string $email;
    public string $token;
    public string $password;
    public string $password_confirmation;

    /**
     * Define the validation rules for the DTO.
     */
    protected function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users,email',
            'token' => 'required|string',
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
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.exists' => 'We could not find a user with that email address.',
            'token.required' => 'The reset token is required.',
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
