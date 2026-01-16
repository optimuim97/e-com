<?php

namespace Modules\Cart\DTOs;

use WendelAdriel\ValidatedDTO\ValidatedDTO;

class ApplyCouponDTO extends ValidatedDTO
{
    public string $coupon_code;

    protected function rules(): array
    {
        return [
            'coupon_code' => ['required', 'string', 'max:50'],
        ];
    }

    protected function messages(): array
    {
        return [
            'coupon_code.required' => 'Le code promo est obligatoire',
            'coupon_code.max' => 'Le code promo ne peut pas dépasser 50 caractères',
        ];
    }
}
