<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the average rating for the product.
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    /**
     * Check if product has sufficient stock.
     */
    public function hasSufficientStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Reduce product stock.
     */
    public function reduceStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
    }
}
