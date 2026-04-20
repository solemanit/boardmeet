<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meeting_number',
        'title',
        'type',
        'status',
        'date',
        'start_time',
        'end_time',
        'venue',
        'venue_type',
        'meeting_link',
        'description',
        'chairperson_id',
        'secretary_id',
        'created_by',
        'previous_meeting_id',
        'rescheduled_from_id',
        'rescheduled_at',
        'cancelled_at',
        'cancellation_reason',
        'notification_sent_at',
    ];

    protected $casts = [
        'date'                 => 'date',
        'rescheduled_at'       => 'datetime',
        'cancelled_at'         => 'datetime',
        'notification_sent_at' => 'datetime',
    ];

    // ── Core relationships ─────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chairperson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chairperson_id');
    }

    public function secretary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function previousMeeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'previous_meeting_id');
    }

    public function rescheduledFrom(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'rescheduled_from_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MeetingDocument::class);
    }

    // ── Legacy flat agenda items (original system) ─────────────────
    // Used by show.blade.php, minutes.blade.php, MeetingWebController

    public function agendaItems(): HasMany
    {
        return $this->hasMany(MeetingAgendaItem::class)->orderBy('order');
    }

    // ── New structured agenda builder ──────────────────────────────

    public function agendaSections(): HasMany
    {
        return $this->hasMany(MeetingAgendaSection::class)->orderBy('order');
    }

    public function agendaMemos(): HasMany
    {
        return $this->hasMany(MeetingAgendaMemo::class)->orderBy('order');
    }

    public function memoVotes(): HasMany
    {
        return $this->hasMany(MeetingMemoVote::class);
    }

    // ── Status helpers ─────────────────────────────────────────────

    public function isScheduled(): bool { return $this->status === 'scheduled'; }
    public function isOngoing(): bool   { return $this->status === 'ongoing'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }
    public function isPostponed(): bool { return $this->status === 'postponed'; }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['scheduled', 'postponed']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['scheduled', 'postponed']);
    }

    public function canBeRescheduled(): bool
    {
        return in_array($this->status, ['scheduled', 'postponed']);
    }

    // ── Badge helpers ──────────────────────────────────────────────

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'scheduled' => 'badge-label-primary',
            'ongoing'   => 'badge-label-warning',
            'completed' => 'badge-label-success',
            'cancelled' => 'badge-label-danger',
            'postponed' => 'badge-label-secondary',
            default     => 'badge-label-secondary',
        };
    }

    public function getTypeBadgeClass(): string
    {
        return match ($this->type) {
            'board'     => 'badge-label-primary',
            'agm'       => 'badge-label-success',
            'egm'       => 'badge-label-danger',
            'committee' => 'badge-label-info',
            'executive' => 'badge-label-warning',
            default     => 'badge-label-secondary',
        };
    }

    // ── Vote progress summary ──────────────────────────────────────

    public function voteProgress(): array
    {
        $memos         = $this->agendaMemos()->whereIn('vote_required', ['required', 'optional'])->withCount('votes')->get();
        $attendeeCount = $this->attendees()->count();
        $totalMemos    = $memos->count();
        $votesCast     = $memos->sum('votes_count');
        $maxPossible   = $totalMemos * max($attendeeCount, 1);

        return [
            'total_memos'  => $totalMemos,
            'votes_cast'   => $votesCast,
            'max_possible' => $maxPossible,
            'pct'          => $maxPossible > 0 ? round($votesCast / $maxPossible * 100) : 0,
        ];
    }

    // ── Auto-generate meeting number ───────────────────────────────

    public static function generateMeetingNumber(): string
    {
        $year   = now()->format('Y');
        $prefix = "MTG-{$year}-";

        $last = self::withTrashed()
            ->where('meeting_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        $next = $last
            ? ((int) substr($last->meeting_number, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
