<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingDocument extends Model
{
    protected $fillable = [
        'meeting_id', 'file_name', 'original_name', 'file_path',
        'file_type', 'file_size', 'document_type', 'uploaded_by',
    ];

    public function meeting(): BelongsTo  { return $this->belongsTo(Meeting::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
