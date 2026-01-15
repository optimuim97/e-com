<?php

namespace Modules\Auth\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class BasicAuthLoginDTO extends ValidatedDTO
{
    public string $username;
    public string $password;

    /**
     * Define the validation rules for the DTO.
     */
    protected function rules(): array
    {
        return [
            'username' => 'required|string',
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
            'username.required' => 'The username field is required.',
            'password.required' => 'The password field is required.',
        ];
    }

    /**
     * Define the type casting for the DTO properties.
     */
    protected function casts(): array
    {
        return [];
    }
}
