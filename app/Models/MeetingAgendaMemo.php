<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingAgendaMemo extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'section_id',
        'title',
        'description',
        'vote_required',
        'order',
        'minutes_note',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(MeetingAgendaSection::class, 'section_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MeetingMemoDocument::class, 'memo_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(MeetingMemoVote::class, 'memo_id');
    }

    // ── Vote helpers ─────────────────────────────────────────────────

    public function voteRequiresAction(): bool
    {
        return in_array($this->vote_required, ['required', 'optional']);
    }

    public function userHasVoted(int $userId): bool
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function userVote(int $userId): ?MeetingMemoVote
    {
        return $this->votes()->where('user_id', $userId)->first();
    }

    /**
     * Returns tally: ['approve' => N, 'reject' => N, 'abstain' => N, 'total' => N]
     */
    public function voteTally(): array
    {
        $counts = $this->votes()
            ->selectRaw('vote, COUNT(*) as cnt')
            ->groupBy('vote')
            ->pluck('cnt', 'vote')
            ->toArray();

        return [
            'approve' => (int) ($counts['approve'] ?? 0),
            'reject'  => (int) ($counts['reject']  ?? 0),
            'abstain' => (int) ($counts['abstain'] ?? 0),
            'total'   => array_sum($counts),
        ];
    }

    /**
     * Outcome based on majority: 'passed' | 'rejected' | 'tied' | 'pending'
     */
    public function voteOutcome(): string
    {
        $t = $this->voteTally();
        if ($t['total'] === 0) return 'pending';
        if ($t['approve'] > $t['reject']) return 'passed';
        if ($t['reject']  > $t['approve']) return 'rejected';
        return 'tied';
    }

    // ── Badge helpers ─────────────────────────────────────────────────

    public function getVoteBadgeClass(): string
    {
        return match ($this->vote_required) {
            'required' => 'badge-label-warning',
            'optional' => 'badge-label-info',
            default    => 'badge-label-secondary',
        };
    }

    public function getOutcomeBadgeClass(): string
    {
        return match ($this->voteOutcome()) {
            'passed'   => 'badge-label-success',
            'rejected' => 'badge-label-danger',
            'tied'     => 'badge-label-warning',
            default    => 'badge-label-secondary',
        };
    }
}
