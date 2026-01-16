<?php

namespace Modules\Products\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Products\DTOs\CreateCategoryDTO;
use Modules\Products\DTOs\UpdateCategoryDTO;
use Modules\Products\Services\CategoryService;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Get all categories.
     */
    public function index(Request $request)
    {
        $activeOnly = $request->boolean('active_only', false);
        $categories = $this->categoryService->getAllCategories($activeOnly);

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Get category tree (hierarchical).
     */
    public function tree(Request $request)
    {
        $activeOnly = $request->boolean('active_only', false);
        $tree = $this->categoryService->getCategoryTree($activeOnly);

        return response()->json([
            'tree' => $tree,
        ]);
    }

    /**
     * Get root categories only.
     */
    public function roots(Request $request)
    {
        $activeOnly = $request->boolean('active_only', false);
        $categories = $this->categoryService->getRootCategories($activeOnly);

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Get a single category by ID.
     */
    public function show(int $id)
    {
        $category = $this->categoryService->getCategoryById($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    /**
     * Get a single category by slug.
     */
    public function showBySlug(string $slug)
    {
        $category = $this->categoryService->getCategoryBySlug($slug);

        return response()->json([
            'category' => $category,
        ]);
    }

    /**
     * Create a new category.
     */
    public function store(Request $request)
    {
        $dto = CreateCategoryDTO::fromRequest($request);
        $category = $this->categoryService->createCategory($dto);

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'category' => $category,
        ], 201);
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, int $id)
    {
        $data = array_merge($request->all(), ['category_id' => $id]);
        $dto = UpdateCategoryDTO::fromArray($data);
        $category = $this->categoryService->updateCategory($id, $dto);

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès',
            'category' => $category,
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroy(int $id)
    {
        $this->categoryService->deleteCategory($id);

        return response()->json([
            'message' => 'Catégorie supprimée avec succès',
        ]);
    }

    /**
     * Get categories with product count.
     */
    public function withProductCount()
    {
        $categories = $this->categoryService->getCategoriesWithProductCount();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:categories,id',
            'orders.*.sort_order' => 'required|integer|min:0',
        ]);

        $this->categoryService->reorderCategories($request->input('orders'));

        return response()->json([
            'message' => 'Catégories réordonnées avec succès',
        ]);
    }
}
