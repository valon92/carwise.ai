<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewHelpfulness extends Model
{
    use HasFactory;

    protected $table = 'review_helpfulness';

    protected $fillable = [
        'review_id',
        'user_id',
        'is_helpful'
    ];

    protected $casts = [
        'is_helpful' => 'boolean'
    ];

    /**
     * Get the review that this helpfulness vote belongs to.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(MechanicReview::class, 'review_id');
    }

    /**
     * Get the user who voted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for helpful votes.
     */
    public function scopeHelpful($query)
    {
        return $query->where('is_helpful', true);
    }

    /**
     * Scope for not helpful votes.
     */
    public function scopeNotHelpful($query)
    {
        return $query->where('is_helpful', false);
    }

    /**
     * Check if user has already voted on a review.
     */
    public static function hasUserVoted($userId, $reviewId): bool
    {
        return static::where('user_id', $userId)
            ->where('review_id', $reviewId)
            ->exists();
    }

    /**
     * Get user's vote on a review.
     */
    public static function getUserVote($userId, $reviewId): ?bool
    {
        $vote = static::where('user_id', $userId)
            ->where('review_id', $reviewId)
            ->first();
        
        return $vote ? $vote->is_helpful : null;
    }

    /**
     * Update or create a vote.
     */
    public static function updateVote($userId, $reviewId, $isHelpful): self
    {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'review_id' => $reviewId
            ],
            [
                'is_helpful' => $isHelpful
            ]
        );
    }

    /**
     * Remove a user's vote.
     */
    public static function removeVote($userId, $reviewId): bool
    {
        return static::where('user_id', $userId)
            ->where('review_id', $reviewId)
            ->delete() > 0;
    }
}