<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agenda extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agenda_number',
        'title',
        'description',
        'status',
        'priority',
        'category',
        'created_by',
        'assigned_to',
        'approved_by',
        'approved_at',
        'submitted_at',
        'meeting_date',
        'version',
        'rejection_reason',
        'revision_notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'submitted_at' => 'datetime',
        'meeting_date' => 'date',
        'version' => 'integer',
    ];

    // ── Relationships ───────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AgendaDocument::class);
    }

    public function mainDocument()
    {
        return $this->hasOne(AgendaDocument::class)->where('document_type', 'main');
    }

    public function supportingDocuments(): HasMany
    {
        return $this->hasMany(AgendaDocument::class)->where('document_type', 'supporting');
    }

    public function approvalHistory(): HasMany
    {
        return $this->hasMany(AgendaApproval::class)->latest();
    }

    // ── Status helpers ──────────────────────────────────────────────

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPendingReview(): bool
    {
        return $this->status === 'pending_review';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isRevised(): bool
    {
        return $this->status === 'revised';
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'rejected', 'revised']);
    }

    public function canBeSubmitted(): bool
    {
        return in_array($this->status, ['draft', 'revised']);
    }

    // ── Status badge colors ─────────────────────────────────────────

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'draft'          => 'badge-label-secondary',
            'pending_review' => 'badge-label-warning',
            'approved'       => 'badge-label-success',
            'rejected'       => 'badge-label-danger',
            'revised'        => 'badge-label-info',
            default          => 'badge-label-secondary',
        };
    }

    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            'low'    => 'badge-label-secondary',
            'medium' => 'badge-label-info',
            'high'   => 'badge-label-warning',
            'urgent' => 'badge-label-danger',
            default  => 'badge-label-secondary',
        };
    }

    // ── Auto generate agenda number ─────────────────────────────────

    public static function generateAgendaNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "AGD-{$year}-";

        $lastAgenda = self::withTrashed()
            ->where('agenda_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if ($lastAgenda) {
            $lastNumber = (int) substr($lastAgenda->agenda_number, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
