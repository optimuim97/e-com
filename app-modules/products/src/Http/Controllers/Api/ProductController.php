<?php

namespace Modules\Products\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Products\DTOs\CreateProductDTO;
use Modules\Products\DTOs\ProductFilterDTO;
use Modules\Products\DTOs\UpdateProductDTO;
use Modules\Products\Services\ProductService;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get paginated list of products with filters.
     */
    public function index(Request $request)
    {
        $filters = ProductFilterDTO::fromRequest($request);
        $products = $this->productService->getProducts($filters);

        return response()->json($products);
    }

    /**
     * Get a single product by ID.
     */
    public function show(int $id)
    {
        $product = $this->productService->getProductById($id);

        // Increment view count
        $this->productService->incrementViewCount($id);

        return response()->json([
            'product' => $product,
        ]);
    }

    /**
     * Get a single product by slug.
     */
    public function showBySlug(string $slug)
    {
        $product = $this->productService->getProductBySlug($slug);

        // Increment view count
        $this->productService->incrementViewCount($product->id);

        return response()->json([
            'product' => $product,
        ]);
    }

    /**
     * Create a new product.
     */
    public function store(Request $request)
    {
        $dto = CreateProductDTO::fromRequest($request);
        $product = $this->productService->createProduct($dto);

        return response()->json([
            'message' => 'Produit créé avec succès',
            'product' => $product,
        ], 201);
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, int $id)
    {
        $data = array_merge($request->all(), ['product_id' => $id]);
        $dto = UpdateProductDTO::fromArray($data);
        $product = $this->productService->updateProduct($id, $dto);

        return response()->json([
            'message' => 'Produit mis à jour avec succès',
            'product' => $product,
        ]);
    }

    /**
     * Delete a product.
     */
    public function destroy(int $id)
    {
        $this->productService->deleteProduct($id);

        return response()->json([
            'message' => 'Produit supprimé avec succès',
        ]);
    }

    /**
     * Get featured products.
     */
    public function featured(Request $request)
    {
        $limit = $request->input('limit', 10);
        $products = $this->productService->getFeaturedProducts($limit);

        return response()->json([
            'products' => $products,
        ]);
    }

    /**
     * Get related products.
     */
    public function related(int $id, Request $request)
    {
        $limit = $request->input('limit', 5);
        $products = $this->productService->getRelatedProducts($id, $limit);

        return response()->json([
            'products' => $products,
        ]);
    }

    /**
     * Update product stock.
     */
    public function updateStock(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer',
        ]);

        $product = $this->productService->updateStock($id, $request->input('quantity'));

        return response()->json([
            'message' => 'Stock mis à jour avec succès',
            'product' => $product,
        ]);
    }

    /**
     * Get low stock products.
     */
    public function lowStock()
    {
        $products = $this->productService->getLowStockProducts();

        return response()->json([
            'products' => $products,
        ]);
    }

    /**
     * Get out of stock products.
     */
    public function outOfStock()
    {
        $products = $this->productService->getOutOfStockProducts();

        return response()->json([
            'products' => $products,
        ]);
    }

    /**
     * Bulk update product status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'status' => 'required|in:draft,active,archived',
        ]);

        $count = $this->productService->bulkUpdateStatus(
            $request->input('product_ids'),
            $request->input('status')
        );

        return response()->json([
            'message' => "$count produits mis à jour avec succès",
        ]);
    }

    /**
     * Duplicate a product.
     */
    public function duplicate(int $id)
    {
        $product = $this->productService->duplicateProduct($id);

        return response()->json([
            'message' => 'Produit dupliqué avec succès',
            'product' => $product,
        ], 201);
    }
}
