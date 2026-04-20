<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingAgendaSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'title',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function memos(): HasMany
    {
        return $this->hasMany(MeetingAgendaMemo::class, 'section_id')->orderBy('order');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /**
     * Total memos requiring a vote in this section.
     */
    public function requiredVoteCount(): int
    {
        return $this->memos()->where('vote_required', 'required')->count();
    }
}
