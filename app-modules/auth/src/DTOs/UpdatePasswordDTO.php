<?php

namespace Modules\Auth\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class UpdatePasswordDTO extends ValidatedDTO
{
    public string $current_password;
    public string $password;
    public string $password_confirmation;

    /**
     * Define the validation rules for the DTO.
     */
    protected function rules(): array
    {
        return [
            'current_password' => 'required|string',
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
            'current_password.required' => 'The current password field is required.',
            'password.required' => 'The new password field is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    public function casts(): array
    {
        return [];
    }
}
