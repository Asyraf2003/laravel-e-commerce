<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\ReviewVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewVoteAdminController extends Controller
{
    public function index(ProductReview $review)
    {
        $this->authorize('update', $review);

        $votes = $review->votes()->with('user:id,name')->orderByDesc('id')->paginate(20);

        return view('admin.reviews.votes', [
            'review' => $review->load(['product:id,name', 'user:id,name']),
            'votes'  => $votes,
        ]);
    }

    public function destroy(ProductReview $review, ReviewVote $vote)
    {
        $this->authorize('update', $review);

        if ($vote->review_id !== $review->id) {
            abort(404);
        }

        DB::transaction(function () use ($vote) {
            $vote->delete(); // model event akan sync helpful_count
        });

        return back()->with('status', 'Vote deleted');
    }
}
