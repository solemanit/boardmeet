<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAgendaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'title',
        'details',
        'order',
        'minutes_note',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // ── Relationships ───────────────────────────────────────────────

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}
