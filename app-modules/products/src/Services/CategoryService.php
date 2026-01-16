<?php

namespace Modules\Products\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Products\DTOs\CreateCategoryDTO;
use Modules\Products\DTOs\UpdateCategoryDTO;
use Modules\Products\Models\Category;

class CategoryService
{
    /**
     * Get all categories.
     */
    public function getAllCategories(bool $activeOnly = false): Collection
    {
        $query = Category::with(['parent', 'children'])
            ->orderBy('sort_order');

        if ($activeOnly) {
            $query->active();
        }

        return $query->get();
    }

    /**
     * Get root categories (no parent).
     */
    public function getRootCategories(bool $activeOnly = false): Collection
    {
        $query = Category::with(['children'])
            ->root()
            ->orderBy('sort_order');

        if ($activeOnly) {
            $query->active();
        }

        return $query->get();
    }

    /**
     * Get category tree (hierarchical structure).
     */
    public function getCategoryTree(bool $activeOnly = false): Collection
    {
        $categories = $this->getRootCategories($activeOnly);

        return $categories->map(function ($category) use ($activeOnly) {
            return $this->buildTree($category, $activeOnly);
        });
    }

    /**
     * Build category tree recursively.
     */
    private function buildTree(Category $category, bool $activeOnly = false): array
    {
        $children = $activeOnly 
            ? $category->children()->active()->orderBy('sort_order')->get()
            : $category->children;

        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'image' => $category->image,
            'is_active' => $category->is_active,
            'sort_order' => $category->sort_order,
            'children' => $children->map(function ($child) use ($activeOnly) {
                return $this->buildTree($child, $activeOnly);
            })->toArray(),
        ];
    }

    /**
     * Get a single category by ID.
     */
    public function getCategoryById(int $id): ?Category
    {
        return Category::with(['parent', 'children', 'products'])
            ->findOrFail($id);
    }

    /**
     * Get a single category by slug.
     */
    public function getCategoryBySlug(string $slug): ?Category
    {
        return Category::with(['parent', 'children', 'products'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Create a new category.
     */
    public function createCategory(CreateCategoryDTO $dto): Category
    {
        return Category::create($dto->toArray());
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(int $id, UpdateCategoryDTO $dto): Category
    {
        $category = Category::findOrFail($id);

        $updateData = array_filter($dto->toArray(), function ($value, $key) {
            return $value !== null && $key !== 'category_id';
        }, ARRAY_FILTER_USE_BOTH);

        $category->update($updateData);

        return $category->fresh(['parent', 'children']);
    }

    /**
     * Delete a category.
     */
    public function deleteCategory(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $category = Category::findOrFail($id);

            // Move children to parent category or make them root
            if ($category->children()->count() > 0) {
                $category->children()->update([
                    'parent_id' => $category->parent_id,
                ]);
            }

            // Detach products
            $category->products()->detach();

            return $category->delete();
        });
    }

    /**
     * Get categories with product count.
     */
    public function getCategoriesWithProductCount(): Collection
    {
        return Category::withCount('products')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Reorder categories.
     */
    public function reorderCategories(array $categoryOrders): bool
    {
        return DB::transaction(function () use ($categoryOrders) {
            foreach ($categoryOrders as $order) {
                Category::where('id', $order['id'])
                    ->update(['sort_order' => $order['sort_order']]);
            }
            return true;
        });
    }
}
