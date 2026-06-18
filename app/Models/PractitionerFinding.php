<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PractitionerFinding extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id','practitioner_id','wait_time_rating',
        'data_integrity_rating','usability_rating','overall_rating',
        'findings_text','video_url','screenshot_path','is_published',
    ];

    protected $casts = [
        'practitioner_id' => 'integer',
        'is_published'    => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(PractitionerApplication::class);
    }

    public function practitioner()
    {
        return $this->belongsTo(User::class, 'practitioner_id');
    }

    /**
     * Convert a YouTube or Vimeo watch URL into its embeddable form.
     * Returns null if the URL is empty or not a recognised provider.
     */
    public function embedUrl(): ?string
    {
        $url = trim((string) $this->video_url);
        if ($url === '') {
            return null;
        }

        // YouTube: youtu.be/<id> or *youtube.com/watch?v=<id>
        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // Vimeo: vimeo.com/<id> or player.vimeo.com/video/<id>
        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return null;
    }

    public function averageRating(): float
    {
        $ratings = array_filter([
            $this->wait_time_rating,
            $this->data_integrity_rating,
            $this->usability_rating,
            $this->overall_rating,
        ]);
        return count($ratings) ? array_sum($ratings) / count($ratings) : 0.0;
    }
}
