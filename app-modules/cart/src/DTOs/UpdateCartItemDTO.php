<?php

namespace Modules\Cart\DTOs;

use WendelAdriel\ValidatedDTO\ValidatedDTO;

class UpdateCartItemDTO extends ValidatedDTO
{
    public int $cart_item_id;
    public int $quantity;

    protected function rules(): array
    {
        return [
            'cart_item_id' => ['required', 'integer', 'exists:cart_items,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    protected function messages(): array
    {
        return [
            'cart_item_id.required' => 'L\'article est obligatoire',
            'cart_item_id.exists' => 'L\'article n\'existe pas',
            'quantity.required' => 'La quantité est obligatoire',
            'quantity.min' => 'La quantité doit être au moins 1',
            'quantity.max' => 'La quantité ne peut pas dépasser 999',
        ];
    }
}
