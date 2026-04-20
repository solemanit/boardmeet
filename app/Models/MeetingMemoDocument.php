<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MeetingMemoDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'memo_id',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function memo(): BelongsTo
    {
        return $this->belongsTo(MeetingAgendaMemo::class, 'memo_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getDownloadUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $b = $this->file_size;
        if ($b >= 1048576) return round($b / 1048576, 2) . ' MB';
        if ($b >= 1024)    return round($b / 1024, 1)    . ' KB';
        return $b . ' B';
    }

    public function getIconEmojiAttribute(): string
    {
        return match (strtolower($this->file_type)) {
            'pdf'                              => '📄',
            'doc', 'docx'                      => '📝',
            'xls', 'xlsx'                      => '📊',
            'ppt', 'pptx'                      => '📋',
            'jpg','jpeg','png','gif','bmp',
            'svg','webp'                       => '🖼',
            default                            => '📎',
        };
    }
}
