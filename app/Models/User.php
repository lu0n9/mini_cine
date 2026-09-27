<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'is_active',
        'ban_reason',
        'ban_expires_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'ban_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function isBanned(): bool
    {
        if ($this->is_active) {
            return false;
        }

        if (
            $this->ban_expires_at &&
            $this->ban_expires_at->isPast()
        ) {
            return false;
        }

        return true;
    }

    public function banHasExpired(): bool
    {
        return !$this->is_active
            && $this->ban_expires_at
            && $this->ban_expires_at->isPast();
    }

    public function activateFromBan(): void
    {
        $this->update([
            'is_active' => true,
            'ban_reason' => null,
            'ban_expires_at' => null,
        ]);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function watchHistories()
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function movieViews()
    {
        return $this->hasMany(MovieView::class);
    }

    public function searchHistories()
    {
        return $this->hasMany(SearchHistory::class);
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function commentLikes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function forumComments()
    {
        return $this->hasMany(ForumComment::class);
    }

    public function forumPostLikes()
    {
        return $this->hasMany(ForumPostLike::class);
    }

    public function forumCommentLikes()
    {
        return $this->hasMany(ForumCommentLike::class);
    }

    public function activePremiumSubscription(): ?PremiumSubscription
    {
        $owned = PremiumSubscription::query()
            ->with('plan')
            ->where('user_id', $this->id)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest('ends_at')
            ->first();

        if ($owned) {
            return $owned;
        }

        return PremiumShare::query()
            ->with('subscription.plan')
            ->where('shared_user_id', $this->id)
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'active')->where('ends_at', '>', now());
            })
            ->latest()
            ->first()?->subscription;
    }

    public function premiumSubscriptions()
    {
        return $this->hasMany(PremiumSubscription::class);
    }

    public function premiumCouponGrants()
    {
        return $this->hasMany(UserPremiumCoupon::class);
    }

    public function premiumShares()
    {
        return $this->hasMany(PremiumShare::class, 'shared_user_id');
    }

    public function hasPremiumAccess(): bool
    {
        return $this->activePremiumSubscription() !== null;
    }

    public function hasPremiumExtra(): bool
    {
        return $this->activePremiumSubscription()?->plan?->code === 'premium_extra';
    }
}
