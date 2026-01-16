<?php

namespace Modules\Cart\DTOs;

use WendelAdriel\ValidatedDTO\ValidatedDTO;

class AddToCartDTO extends ValidatedDTO
{
    public int $product_id;
    public int $quantity;
    public ?array $options;

    protected function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'options' => ['nullable', 'array'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'quantity' => 1,
            'options' => null,
        ];
    }

    protected function messages(): array
    {
        return [
            'product_id.required' => 'Le produit est obligatoire',
            'product_id.exists' => 'Le produit n\'existe pas',
            'quantity.required' => 'La quantité est obligatoire',
            'quantity.min' => 'La quantité doit être au moins 1',
        ];
    }
}
