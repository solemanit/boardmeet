<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'agenda_id',
        'user_id',
        'action',
        'comment',
        'version',
    ];

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionBadgeClass(): string
    {
        return match ($this->action) {
            'submitted'          => 'badge-label-info',
            'approved'           => 'badge-label-success',
            'rejected'           => 'badge-label-danger',
            'revision_requested' => 'badge-label-warning',
            'revised'            => 'badge-label-secondary',
            default              => 'badge-label-secondary',
        };
    }

    public function getActionLabel(): string
    {
        return match ($this->action) {
            'submitted'          => 'Submitted for Review',
            'approved'           => 'Approved',
            'rejected'           => 'Rejected',
            'revision_requested' => 'Revision Requested',
            'revised'            => 'Revised & Resubmitted',
            default              => ucfirst($this->action),
        };
    }
}
