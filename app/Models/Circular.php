<?php
// app/Models/Circular.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Circular extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'reference_number', 'body', 'type', 'status',
        'issued_date', 'published_at', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'issued_date'  => 'date',
        'published_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────
    public function attachments()
    {
        return $this->hasMany(CircularAttachment::class);
    }

    public function reminderLogs()
    {
        return $this->hasMany(CircularReminderLog::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────────
    public function scopePublished($q) { return $q->where('status', 'published'); }
    public function scopeDraft($q)     { return $q->where('status', 'draft'); }

    // ── Helpers ───────────────────────────────────────────────────────────
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'notice'   => 'warning',
            'circular' => 'primary',
            'memo'     => 'info',
            default    => 'secondary',
        };
    }

    public function getLastReminderSentAtAttribute()
    {
        return $this->reminderLogs()->latest('sent_at')->value('sent_at');
    }
}
