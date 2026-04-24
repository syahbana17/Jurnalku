<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = ['user_id', 'judul', 'topik', 'kelas', 'catatan', 'link_url', 'link_type', 'file_pdf'];

    public function user() { return $this->belongsTo(User::class); }

    // Deteksi otomatis tipe link dari URL
    public static function detectLinkType(string $url): string
    {
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) return 'youtube';
        if (str_contains($url, 'canva.com'))  return 'canva';
        if (str_contains($url, 'drive.google.com') || str_contains($url, 'docs.google.com')) return 'drive';
        if (str_ends_with(strtolower(parse_url($url, PHP_URL_PATH) ?? ''), '.pdf')) return 'pdf';
        return 'other';
    }

    // Label & icon per tipe
    public function mediaIcon(): string
    {
        return match($this->link_type) {
            'youtube' => '▶️',
            'canva'   => '🎨',
            'drive'   => '📁',
            'pdf'     => '📄',
            default   => '🔗',
        };
    }

    public function mediaLabel(): string
    {
        return match($this->link_type) {
            'youtube' => 'YouTube',
            'canva'   => 'Canva',
            'drive'   => 'Google Drive',
            'pdf'     => 'PDF',
            default   => 'Link',
        };
    }

    public function mediaBadgeClass(): string
    {
        return match($this->link_type) {
            'youtube' => 'media-yt',
            'canva'   => 'media-canva',
            'drive'   => 'media-drive',
            'pdf'     => 'media-pdf',
            default   => 'media-other',
        };
    }
}
