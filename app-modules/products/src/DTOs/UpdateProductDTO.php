<?php

namespace Modules\Products\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class UpdateProductDTO extends ValidatedDTO
{
    public ?string $name;
    public ?string $slug;
    public ?string $description;
    public ?string $short_description;
    public ?float $price;
    public ?float $compare_price;
    public ?float $cost;
    public ?string $sku;
    public ?string $barcode;
    public ?int $quantity;
    public ?int $low_stock_threshold;
    public ?bool $track_inventory;
    public ?string $stock_status;
    public ?float $weight;
    public ?float $length;
    public ?float $width;
    public ?float $height;
    public ?string $status;
    public ?bool $is_featured;
    public ?bool $is_visible;
    public ?string $meta_title;
    public ?string $meta_description;
    public ?string $meta_keywords;
    public ?array $attributes;
    public ?array $category_ids;
    public ?string $published_at;
    public int $product_id;

    protected function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:products,slug,' . $this->product_id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'sometimes|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $this->product_id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $this->product_id,
            'quantity' => 'sometimes|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'track_inventory' => 'sometimes|boolean',
            'stock_status' => 'sometimes|in:in_stock,out_of_stock,on_backorder',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:draft,active,archived',
            'is_featured' => 'sometimes|boolean',
            'is_visible' => 'sometimes|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'attributes' => 'nullable|array',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'published_at' => 'nullable|date',
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Le nom du produit doit être une chaîne de caractères.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'price.min' => 'Le prix doit être supérieur ou égal à 0.',
            'sku.unique' => 'Ce SKU est déjà utilisé.',
            'barcode.unique' => 'Ce code-barres est déjà utilisé.',
            'status.in' => 'Le statut doit être draft, active ou archived.',
            'stock_status.in' => 'Le statut du stock doit être in_stock, out_of_stock ou on_backorder.',
            'category_ids.*.exists' => 'Une ou plusieurs catégories sélectionnées n\'existent pas.',
        ];
    }

    public function casts(): array
    {
        return [
            'price' => 'float',
            'compare_price' => 'float',
            'cost' => 'float',
            'quantity' => 'int',
            'low_stock_threshold' => 'int',
            'track_inventory' => 'bool',
            'is_featured' => 'bool',
            'is_visible' => 'bool',
            'weight' => 'float',
            'length' => 'float',
            'width' => 'float',
            'height' => 'float',
            'product_id' => 'int',
        ];
    }
}
