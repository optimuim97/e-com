<?php

namespace Modules\Orders\DTOs;

use WendelAdriel\ValidatedDTO\ValidatedDTO;

class UpdateOrderStatusDTO extends ValidatedDTO
{
    public int $order_id;
    public string $status;
    public ?string $tracking_number;
    public ?string $admin_notes;

    protected function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'status' => ['required', 'string', 'in:pending,confirmed,processing,shipped,delivered,cancelled,refunded'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'tracking_number' => null,
            'admin_notes' => null,
        ];
    }

    protected function messages(): array
    {
        return [
            'order_id.required' => 'La commande est obligatoire',
            'order_id.exists' => 'La commande n\'existe pas',
            'status.required' => 'Le statut est obligatoire',
            'status.in' => 'Statut invalide',
        ];
    }
}
