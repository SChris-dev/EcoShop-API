<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReviewController extends Controller
{
    /**
     * Display reviews for a specific product.
     */
    public function index(Product $product)
    {
        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->get();

        return response()->json([
            'reviews' => $reviews
        ]);
    }

    /**
     * Store a newly created review.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user has purchased this product
        $hasPurchased = $request->user()
            ->orders()
            ->whereHas('orderItems', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'message' => 'You can only review products you have purchased.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this product.'
            ], Response::HTTP_CONFLICT);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $review->load('user');

        return response()->json([
            'message' => 'Review created successfully',
            'review' => $review
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified review.
     */
    public function show(Review $review)
    {
        $review->load(['user', 'product']);

        return response()->json([
            'review' => $review
        ]);
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, Review $review)
    {
        // Users can only update their own reviews
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Access denied. You can only update your own reviews.'
            ], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'sometimes|nullable|string|max:1000',
        ]);

        $review->update($request->only(['rating', 'comment']));

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => $review
        ]);
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Request $request, Review $review)
    {
        // Users can only delete their own reviews, admins can delete any
        if (!$request->user()->isAdmin() && $review->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Access denied. You can only delete your own reviews.'
            ], Response::HTTP_FORBIDDEN);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }
}
