<?php

namespace Modules\Products\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'cost',
        'sku',
        'barcode',
        'quantity',
        'low_stock_threshold',
        'track_inventory',
        'stock_status',
        'weight',
        'length',
        'width',
        'height',
        'status',
        'is_featured',
        'is_visible',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'attributes',
        'view_count',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'track_inventory' => 'boolean',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
        'view_count' => 'integer',
        'attributes' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Get the product images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the categories for this product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product')
            ->withTimestamps();
    }

    /**
     * Scope to get only active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_visible', true);
    }

    /**
     * Scope to get featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get in-stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock')
            ->where(function ($q) {
                $q->where('track_inventory', false)
                    ->orWhere('quantity', '>', 0);
            });
    }

    /**
     * Scope to filter by price range.
     */
    public function scopePriceBetween($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Check if product is on sale.
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    /**
     * Calculate discount percentage.
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->is_on_sale) {
            return null;
        }

        return round((($this->compare_price - $this->price) / $this->compare_price) * 100, 2);
    }

    /**
     * Check if product is low on stock.
     */
    public function getIsLowStockAttribute(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }

        return $this->quantity > 0 && $this->quantity <= $this->low_stock_threshold;
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Update stock quantity.
     */
    public function updateStock(int $quantity): void
    {
        if ($this->track_inventory) {
            $this->quantity += $quantity;
            
            if ($this->quantity <= 0) {
                $this->stock_status = 'out_of_stock';
                $this->quantity = 0;
            } else {
                $this->stock_status = 'in_stock';
            }
            
            $this->save();
        }
    }
}
