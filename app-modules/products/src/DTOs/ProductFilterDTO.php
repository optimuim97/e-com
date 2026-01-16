<?php

namespace Modules\Products\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ProductFilterDTO extends ValidatedDTO
{
    public ?string $search;
    public ?array $category_ids;
    public ?float $min_price;
    public ?float $max_price;
    public ?string $status;
    public ?bool $is_featured;
    public ?string $stock_status;
    public ?string $sort_by;
    public ?string $sort_order;
    public int $per_page;
    public int $page;

    protected function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'status' => 'nullable|in:draft,active,archived',
            'is_featured' => 'nullable|boolean',
            'stock_status' => 'nullable|in:in_stock,out_of_stock,on_backorder',
            'sort_by' => 'nullable|in:name,price,created_at,view_count,quantity',
            'sort_order' => 'nullable|in:asc,desc',
            'per_page' => 'integer|min:1|max:100',
            'page' => 'integer|min:1',
        ];
    }

    protected function defaults(): array
    {
        return [
            'sort_by' => 'created_at',
            'sort_order' => 'desc',
            'per_page' => 15,
            'page' => 1,
        ];
    }

    public function messages(): array
    {
        return [
            'category_ids.*.exists' => 'Une ou plusieurs catégories sélectionnées n\'existent pas.',
            'max_price.gte' => 'Le prix maximum doit être supérieur ou égal au prix minimum.',
            'sort_by.in' => 'Le tri doit être par name, price, created_at, view_count ou quantity.',
            'sort_order.in' => 'L\'ordre de tri doit être asc ou desc.',
        ];
    }

    public function casts(): array
    {
        return [
            'min_price' => 'float',
            'max_price' => 'float',
            'is_featured' => 'bool',
            'per_page' => 'int',
            'page' => 'int',
        ];
    }
}
