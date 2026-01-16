<?php

namespace Modules\Orders\DTOs;

use WendelAdriel\ValidatedDTO\ValidatedDTO;

class CreateOrderDTO extends ValidatedDTO
{
    public string $payment_method;
    public array $shipping_address;
    public ?float $initial_payment_amount;
    public ?string $customer_notes;

    protected function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:cash_on_delivery,online,partial'],
            'shipping_address' => ['required', 'array'],
            'shipping_address.full_name' => ['required', 'string', 'max:255'],
            'shipping_address.email' => ['required', 'email', 'max:255'],
            'shipping_address.phone' => ['required', 'string', 'max:20'],
            'shipping_address.company' => ['nullable', 'string', 'max:255'],
            'shipping_address.address_line1' => ['required', 'string', 'max:255'],
            'shipping_address.address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.state' => ['nullable', 'string', 'max:100'],
            'shipping_address.postal_code' => ['required', 'string', 'max:20'],
            'shipping_address.country' => ['required', 'string', 'max:2'],
            'shipping_address.delivery_instructions' => ['nullable', 'string', 'max:500'],
            'shipping_address.address_type' => ['nullable', 'string', 'in:home,office,other'],
            'initial_payment_amount' => ['nullable', 'numeric', 'min:0'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'shipping_address.country' => 'FR',
            'shipping_address.address_type' => 'home',
            'initial_payment_amount' => null,
            'customer_notes' => null,
        ];
    }

    protected function messages(): array
    {
        return [
            'payment_method.required' => 'La méthode de paiement est obligatoire',
            'payment_method.in' => 'Méthode de paiement invalide',
            'shipping_address.required' => 'L\'adresse de livraison est obligatoire',
            'shipping_address.full_name.required' => 'Le nom complet est obligatoire',
            'shipping_address.email.required' => 'L\'email est obligatoire',
            'shipping_address.email.email' => 'L\'email doit être valide',
            'shipping_address.phone.required' => 'Le téléphone est obligatoire',
            'shipping_address.address_line1.required' => 'L\'adresse est obligatoire',
            'shipping_address.city.required' => 'La ville est obligatoire',
            'shipping_address.postal_code.required' => 'Le code postal est obligatoire',
            'shipping_address.country.required' => 'Le pays est obligatoire',
            'initial_payment_amount.min' => 'Le montant du paiement doit être positif',
        ];
    }
}
