<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingVoteLink extends Model
{
    protected $fillable = [
        'meeting_id',
        'token',
        'expires_at',
        'max_uses',
        'use_count',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(MeetingMemoVote::class, 'vote_link_id');
    }

    // ── Status helpers ───────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExhausted(): bool
    {
        return $this->max_uses !== null && $this->use_count >= $this->max_uses;
    }

    public function isUsable(): bool
    {
        return $this->is_active && !$this->isExpired() && !$this->isExhausted();
    }

    public function getPublicUrl(): string
    {
        return route('vote.show', $this->token);
    }

    public function incrementUse(): void
    {
        $this->increment('use_count');
    }
}
