<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isBanned(): bool
    {
        return $this->banned;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_user', 'user_id', 'game_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function positionTickets(): HasMany
    {
        return $this->hasMany(PositionTicket::class);
    }

    public function friendsOfThisUser(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps();
    }

    public function thisUserFriendOf(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps();
    }

    // Combined collection of all accepted friends
    public function getFriendsAttribute()
    {
        return $this->friendsOfThisUser->merge($this->thisUserFriendOf);
    }

    // Sent requests pending approval
    public function pendingFriendsSent(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', 'pending')
            ->withTimestamps();
    }

    // Received requests pending my approval
    public function pendingFriendsReceived(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', 'pending')
            ->withTimestamps();
    }

    // Status Check Helpers
    public function isFriendsWith(User $user): bool
    {
        return $this->friends->contains('id', $user->id);
    }

    public function hasPendingRequestTo(User $user): bool
    {
        return $this->pendingFriendsSent->contains('id', $user->id);
    }

    public function hasPendingRequestFrom(User $user): bool
    {
        return $this->pendingFriendsReceived->contains('id', $user->id);
    }

    public function projectInvitations(): HasMany
    {
        return $this->hasMany(ProjectInvitation::class)->latest();
    }

    public function pendingProjectInvitations(): HasMany
    {
        return $this->hasMany(ProjectInvitation::class)->where('status', 'pending')->latest();
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Get all unique users this user has messaged with
    public function conversations()
    {
        $sentIds = $this->sentMessages()->pluck('receiver_id');
        $receivedIds = $this->receivedMessages()->pluck('sender_id');

        return User::whereIn('id', $sentIds->merge($receivedIds)->unique())
            ->where('id', '!=', $this->id)
            ->get();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_read', false)->latest();
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            });
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Get active plan name ('plus', 'enterprise', or null for free).
     */
    public function subscriptionPlan(): ?string
    {
        return $this->activeSubscription?->plan_name;
    }

    public function hasPlusPlan(): bool
    {
        return in_array($this->subscriptionPlan(), ['plus', 'enterprise']);
    }

    public function hasEnterprisePlan(): bool
    {
        return $this->subscriptionPlan() === 'enterprise';
    }

    /**
     * Projects owned by this user.
     */
    public function ownedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_owners')->withTimestamps();
    }

    /**
     * Count how many unapproved forked projects the user currently owns.
     */
    public function unapprovedForkedProjectsCount(): int
    {
        return $this->ownedProjects()
            ->whereNotNull('parent_project_id')
            ->where('is_approved', false)
            ->count();
    }

    /**
     * Check if user already owns any forked project.
     */
    public function ownsForkedProject(): bool
    {
        return $this->ownedProjects()
            ->whereNotNull('parent_project_id')
            ->exists();
    }

    /**
     * Forking Guard Logic:
     * - Free Tier: Max 1 forked project total.
     * - Plus Tier: Unlimited total forks, max 3 UNAPPROVED forks at once (anti-spam).
     * - Enterprise Tier / Admin: Unlimited forks.
     */
    public function canForkProject(): bool
    {
        if ($this->isAdmin() || $this->hasEnterprisePlan()) {
            return true;
        }

        if ($this->hasPlusPlan()) {
            // Plus subscribers can fork unlimited projects, max 3 pending approval
            return $this->unapprovedForkedProjectsCount() < 3;
        }

        // Free subscribers can only own 1 forked project
        return !$this->ownsForkedProject();
    }

}
