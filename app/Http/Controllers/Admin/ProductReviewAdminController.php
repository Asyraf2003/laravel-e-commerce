<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProductReviewAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ProductReview::class);

        $q           = (string) $request->string('q');
        $status      = (string) $request->string('status');
        $productId   = $request->integer('product_id') ?: null;
        $userId      = $request->integer('user_id') ?: null;
        $rating      = $request->integer('rating') ?: null;
        $verified    = $request->has('verified') ? (bool) $request->boolean('verified') : null;

        $reviews = ProductReview::query()
            ->with(['product:id,name', 'user:id,name'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                       ->orWhere('body', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn($q) => $q->where('status', $status))
            ->when($productId !== null, fn($q) => $q->where('product_id', $productId))
            ->when($userId !== null, fn($q) => $q->where('user_id', $userId))
            ->when($rating !== null, fn($q) => $q->where('rating', $rating))
            ->when($verified !== null, fn($q) => $q->where('is_verified_purchase', $verified))
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews'   => $reviews,
            'q'         => $q,
            'status'    => $status,
            'productId' => $productId,
            'userId'    => $userId,
            'rating'    => $rating,
            'verified'  => $verified,
        ]);
    }

    public function edit(ProductReview $review)
    {
        $this->authorize('update', $review);

        return view('admin.reviews.edit', [
            'review' => $review->load(['product:id,name', 'user:id,name']),
        ]);
    }

    public function update(Request $request, ProductReview $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title'  => ['nullable', 'string', 'max:150'],
            'body'   => ['nullable', 'string'],
            'status' => ['nullable', Rule::in([
                ProductReview::STATUS_PENDING,
                ProductReview::STATUS_APPROVED,
                ProductReview::STATUS_REJECTED,
            ])],
            'rejected_reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($review, $validated) {
            $review->rating = $validated['rating'];
            $review->title  = $validated['title'] ?? null;
            $review->body   = $validated['body'] ?? null;

            // Status handling + timestamps
            if (!empty($validated['status'])) {
                if ($validated['status'] === ProductReview::STATUS_APPROVED) {
                    $review->markApproved();
                } elseif ($validated['status'] === ProductReview::STATUS_REJECTED) {
                    $review->markRejected($validated['rejected_reason'] ?? null);
                } else {
                    $review->status         = ProductReview::STATUS_PENDING;
                    $review->approved_at    = null;
                    $review->rejected_at    = null;
                    $review->rejected_reason= null;
                    $review->save();
                }
            } else {
                $review->save();
            }

            // Verifikasi pembelian (kalau memenuhi)
            $review->ensureVerifiedPurchase();

            // Recalc aggregate di product
            $this->recalcProductReviewStats($review->product);
        });

        return redirect()
            ->route('admin.reviews.edit', $review)
            ->with('status', 'Review updated');
    }

    public function approve(ProductReview $review)
    {
        $this->authorize('update', $review);

        DB::transaction(function () use ($review) {
            $review->markApproved();
            $review->ensureVerifiedPurchase();
            $this->recalcProductReviewStats($review->product);
        });

        return back()->with('status', 'Review approved');
    }

    public function reject(Request $request, ProductReview $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($review, $validated) {
            $review->markRejected($validated['reason'] ?? null);
            $this->recalcProductReviewStats($review->product);
        });

        return back()->with('status', 'Review rejected');
    }

    public function destroy(ProductReview $review)
    {
        $this->authorize('delete', $review);

        DB::transaction(function () use ($review) {
            $product = $review->product;
            $wasApproved = $review->status === ProductReview::STATUS_APPROVED;
            $review->delete();

            if ($wasApproved) {
                $this->recalcProductReviewStats($product);
            }
        });

        return redirect()
            ->route('admin.reviews.index')
            ->with('status', 'Review deleted');
    }

    /** ---------- Helpers ---------- */

    private function recalcProductReviewStats(Product $product): void
    {
        $agg = $product->reviews()
            ->approved()
            ->selectRaw('COUNT(*) as c, COALESCE(AVG(rating),0) as a')
            ->first();

        $product->reviews_count = (int) ($agg->c ?? 0);
        $product->reviews_avg   = round((float) ($agg->a ?? 0), 2);
        $product->save();
    }
}
