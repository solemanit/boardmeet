<?php
// app/Models/CircularAttachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CircularAttachment extends Model
{
    protected $fillable = [
        'circular_id', 'original_name', 'path', 'mime_type', 'size',
    ];

    public function circular()
    {
        return $this->belongsTo(Circular::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function getIconClassAttribute(): string
    {
        return match (true) {
            str_contains($this->mime_type, 'pdf')   => 'text-danger',
            str_contains($this->mime_type, 'image') => 'text-success',
            str_contains($this->mime_type, 'word')  => 'text-primary',
            str_contains($this->mime_type, 'sheet') => 'text-success',
            default                                  => 'text-secondary',
        };
    }
}
