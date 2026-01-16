<?php

namespace Modules\Orders\DTOs;

use WendelAdriel\ValidatedDTO\ValidatedDTO;

class ProcessPaymentDTO extends ValidatedDTO
{
    public int $order_id;
    public string $payment_method;
    public float $amount;
    public ?array $payment_details;

    protected function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'payment_method' => ['required', 'string', 'in:cash_on_delivery,stripe,paypal,bank_transfer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_details' => ['nullable', 'array'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'payment_details' => null,
        ];
    }

    protected function messages(): array
    {
        return [
            'order_id.required' => 'La commande est obligatoire',
            'order_id.exists' => 'La commande n\'existe pas',
            'payment_method.required' => 'La méthode de paiement est obligatoire',
            'payment_method.in' => 'Méthode de paiement invalide',
            'amount.required' => 'Le montant est obligatoire',
            'amount.min' => 'Le montant doit être supérieur à 0',
        ];
    }
}
