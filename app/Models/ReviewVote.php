<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewVote extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'review_id','user_id','vote',
    ];

    // vote: 'helpful' | 'not_helpful'
    protected $casts = [
        'review_id' => 'integer',
        'user_id'   => 'integer',
    ];

    protected static function booted()
    {
        // Sinkron helpful_count di product_reviews
        static::created(function (ReviewVote $vote) {
            if ($vote->vote === 'helpful') {
                $vote->review()->increment('helpful_count');
            }
        });

        static::updating(function (ReviewVote $vote) {
            if ($vote->isDirty('vote')) {
                $original = $vote->getOriginal('vote');
                $new      = $vote->vote;
                if ($original === 'helpful' && $new !== 'helpful') {
                    $vote->review()->decrement('helpful_count');
                } elseif ($original !== 'helpful' && $new === 'helpful') {
                    $vote->review()->increment('helpful_count');
                }
            }
        });

        static::deleted(function (ReviewVote $vote) {
            if ($vote->vote === 'helpful') {
                $vote->review()->decrement('helpful_count');
            }
        });
    }

    // ===== Relationships =====
    public function review()
    {
        return $this->belongsTo(ProductReview::class, 'review_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
