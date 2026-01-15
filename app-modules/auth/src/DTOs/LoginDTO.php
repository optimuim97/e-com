<?php

namespace Modules\Auth\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class LoginDTO extends ValidatedDTO
{
    public string $email;
    public string $password;

    /**
     * Define the validation rules for the DTO.
     */
    protected function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
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
            'password.required' => 'The password field is required.',
        ];
    }

    public function casts(): array
    {
        return [];
    }
}
