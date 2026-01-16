<?php

namespace Modules\Products\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class UpdateCategoryDTO extends ValidatedDTO
{
    public ?string $name;
    public ?string $slug;
    public ?string $description;
    public ?int $parent_id;
    public ?int $sort_order;
    public ?bool $is_active;
    public ?string $image;
    public ?string $meta_title;
    public ?string $meta_description;
    public int $category_id;

    protected function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $this->category_id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'image' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'parent_id.exists' => 'La catégorie parente n\'existe pas.',
        ];
    }

    public function casts(): array
    {
        return [
            'parent_id' => 'int',
            'sort_order' => 'int',
            'is_active' => 'bool',
            'category_id' => 'int',
        ];
    }
}
