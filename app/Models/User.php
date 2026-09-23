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
}