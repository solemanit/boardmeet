<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'agenda_id',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'document_type',
        'description',
        'uploaded_by',
    ];

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function getFileIconClass(): string
    {
        return match (strtolower($this->file_type)) {
            'pdf'                              => 'text-danger',
            'doc', 'docx'                      => 'text-primary',
            'xls', 'xlsx'                      => 'text-success',
            'ppt', 'pptx'                      => 'text-warning',
            'jpg', 'jpeg', 'png',
            'gif', 'bmp', 'svg', 'webp'        => 'text-info',
            default                            => 'text-secondary',
        };
    }
}
