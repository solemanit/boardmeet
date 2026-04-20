<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingMemoVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'memo_id',
        'meeting_id',
        'user_id',
        'vote',
        'comment',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function memo(): BelongsTo
    {
        return $this->belongsTo(MeetingAgendaMemo::class, 'memo_id');
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function getBadgeClass(): string
    {
        return match ($this->vote) {
            'approve' => 'badge-label-success',
            'reject'  => 'badge-label-danger',
            'abstain' => 'badge-label-secondary',
            default   => 'badge-label-secondary',
        };
    }

    public function getLabel(): string
    {
        return match ($this->vote) {
            'approve' => 'Approved',
            'reject'  => 'Rejected',
            'abstain' => 'Abstained',
            default   => ucfirst($this->vote),
        };
    }
}
