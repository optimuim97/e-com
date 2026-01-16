<?php

namespace Modules\Products\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class CreateCategoryDTO extends ValidatedDTO
{
    public string $name;
    public string $slug;
    public ?string $description;
    public ?int $parent_id;
    public int $sort_order;
    public bool $is_active;
    public ?string $image;
    public ?string $meta_title;
    public ?string $meta_description;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    protected function defaults(): array
    {
        return [
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la catégorie est requis.',
            'slug.required' => 'Le slug est requis.',
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
        ];
    }
}
