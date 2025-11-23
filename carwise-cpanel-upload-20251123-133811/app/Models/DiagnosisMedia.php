<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosisMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'diagnosis_session_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'metadata',
        'ai_analysis',
        'ai_tags'
    ];

    protected $casts = [
        'metadata' => 'array',
        'ai_tags' => 'array'
    ];

    /**
     * Get the diagnosis session that owns the media.
     */
    public function diagnosisSession(): BelongsTo
    {
        return $this->belongsTo(DiagnosisSession::class);
    }

    /**
     * Get the full URL for the media file.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Check if the media is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if the media is a video.
     */
    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    /**
     * Check if the media is an audio file.
     */
    public function isAudio(): bool
    {
        return str_starts_with($this->mime_type, 'audio/');
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
