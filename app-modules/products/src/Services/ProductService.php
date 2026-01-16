<?php

namespace Modules\Products\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Products\DTOs\CreateProductDTO;
use Modules\Products\DTOs\ProductFilterDTO;
use Modules\Products\DTOs\UpdateProductDTO;
use Modules\Products\Models\Product;

class ProductService
{
    /**
     * Get paginated products with filters.
     */
    public function getProducts(ProductFilterDTO $filters): LengthAwarePaginator
    {
        $query = Product::with(['categories', 'primaryImage']);

        // Search
        if ($filters->search) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters->search . '%')
                    ->orWhere('description', 'like', '%' . $filters->search . '%')
                    ->orWhere('sku', 'like', '%' . $filters->search . '%');
            });
        }

        // Filter by categories
        if ($filters->category_ids && count($filters->category_ids) > 0) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->whereIn('categories.id', $filters->category_ids);
            });
        }

        // Filter by price range
        if ($filters->min_price !== null) {
            $query->where('price', '>=', $filters->min_price);
        }
        if ($filters->max_price !== null) {
            $query->where('price', '<=', $filters->max_price);
        }

        // Filter by status
        if ($filters->status) {
            $query->where('status', $filters->status);
        }

        // Filter by featured
        if ($filters->is_featured !== null) {
            $query->where('is_featured', $filters->is_featured);
        }

        // Filter by stock status
        if ($filters->stock_status) {
            $query->where('stock_status', $filters->stock_status);
        }

        // Sorting
        $query->orderBy($filters->sort_by, $filters->sort_order);

        return $query->paginate($filters->per_page, ['*'], 'page', $filters->page);
    }

    /**
     * Get a single product by ID.
     */
    public function getProductById(int $id): ?Product
    {
        return Product::with(['categories', 'images'])
            ->findOrFail($id);
    }

    /**
     * Get a single product by slug.
     */
    public function getProductBySlug(string $slug): ?Product
    {
        return Product::with(['categories', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Create a new product.
     */
    public function createProduct(CreateProductDTO $dto): Product
    {
        return DB::transaction(function () use ($dto) {
            // Create product
            $productData = $dto->toArray();
            $categoryIds = $productData['category_ids'] ?? null;
            unset($productData['category_ids']);

            $product = Product::create($productData);

            // Attach categories
            if ($categoryIds) {
                $product->categories()->attach($categoryIds);
            }

            return $product->load(['categories', 'images']);
        });
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(int $id, UpdateProductDTO $dto): Product
    {
        return DB::transaction(function () use ($id, $dto) {
            $product = Product::findOrFail($id);

            $updateData = array_filter($dto->toArray(), function ($value) {
                return $value !== null;
            });

            $categoryIds = $updateData['category_ids'] ?? null;
            unset($updateData['category_ids']);
            unset($updateData['product_id']);

            // Update product
            $product->update($updateData);

            // Sync categories if provided
            if ($categoryIds !== null) {
                $product->categories()->sync($categoryIds);
            }

            return $product->fresh(['categories', 'images']);
        });
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(int $id): bool
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }

    /**
     * Get featured products.
     */
    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return Product::with(['primaryImage', 'categories'])
            ->active()
            ->featured()
            ->limit($limit)
            ->get();
    }

    /**
     * Get related products based on categories.
     */
    public function getRelatedProducts(int $productId, int $limit = 5): Collection
    {
        $product = Product::findOrFail($productId);
        $categoryIds = $product->categories->pluck('id')->toArray();

        return Product::with(['primaryImage', 'categories'])
            ->active()
            ->where('id', '!=', $productId)
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Increment product view count.
     */
    public function incrementViewCount(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->incrementViewCount();
    }

    /**
     * Update product stock.
     */
    public function updateStock(int $id, int $quantity): Product
    {
        $product = Product::findOrFail($id);
        $product->updateStock($quantity);
        return $product->fresh();
    }

    /**
     * Get low stock products.
     */
    public function getLowStockProducts(): Collection
    {
        return Product::where('track_inventory', true)
            ->where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'low_stock_threshold')
            ->with(['primaryImage'])
            ->get();
    }

    /**
     * Get out of stock products.
     */
    public function getOutOfStockProducts(): Collection
    {
        return Product::where('stock_status', 'out_of_stock')
            ->orWhere(function ($q) {
                $q->where('track_inventory', true)
                    ->where('quantity', '<=', 0);
            })
            ->with(['primaryImage'])
            ->get();
    }

    /**
     * Bulk update product status.
     */
    public function bulkUpdateStatus(array $productIds, string $status): int
    {
        return Product::whereIn('id', $productIds)
            ->update(['status' => $status]);
    }

    /**
     * Duplicate a product.
     */
    public function duplicateProduct(int $id): Product
    {
        return DB::transaction(function () use ($id) {
            $originalProduct = Product::with(['categories', 'images'])->findOrFail($id);

            // Clone product
            $newProduct = $originalProduct->replicate();
            $newProduct->name = $originalProduct->name . ' (Copy)';
            $newProduct->slug = $originalProduct->slug . '-copy-' . time();
            $newProduct->sku = $originalProduct->sku ? $originalProduct->sku . '-copy' : null;
            $newProduct->barcode = null; // Don't duplicate barcode
            $newProduct->save();

            // Clone categories
            $newProduct->categories()->attach($originalProduct->categories->pluck('id'));

            // Clone images
            foreach ($originalProduct->images as $image) {
                $newProduct->images()->create([
                    'path' => $image->path,
                    'alt_text' => $image->alt_text,
                    'sort_order' => $image->sort_order,
                    'is_primary' => $image->is_primary,
                ]);
            }

            return $newProduct->fresh(['categories', 'images']);
        });
    }
}
