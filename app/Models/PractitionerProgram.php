<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PractitionerProgram extends Model
{
    protected $fillable = [
        'product_slug','product_name','title','title_fr','description',
        'description_fr','type','compensation','max_participants','status',
        'starts_at','ends_at',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at'   => 'date',
    ];

    public function applications()
    {
        return $this->hasMany(PractitionerApplication::class, 'program_id');
    }

    public static function typeOptions(): array
    {
        return ['volunteer' => 'Volunteer', 'paid' => 'Paid'];
    }

    public static function statusOptions(): array
    {
        return [
            'draft'    => 'Draft',
            'open'     => 'Open',
            'closed'   => 'Closed',
            'archived' => 'Archived',
        ];
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isFull(): bool
    {
        return $this->max_participants !== null &&
            $this->applications()->where('status', 'approved')->count() >= $this->max_participants;
    }
}
