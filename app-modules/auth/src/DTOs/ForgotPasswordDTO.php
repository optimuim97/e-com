<?php

namespace Modules\Auth\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ForgotPasswordDTO extends ValidatedDTO
{
    public string $email;

    /**
     * Define the validation rules for the DTO.
     */
    protected function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users,email',
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
        ];
    }

    public function casts(): array
    {
        return [];
    }
}
