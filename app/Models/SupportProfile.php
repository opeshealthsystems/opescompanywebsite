<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportProfile extends Model
{
    protected $fillable = ['user_id', 'ticket_specialization', 'shift', 'languages', 'bio'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function specializationOptions(): array
    {
        return ['all' => 'All', 'technical' => 'Technical', 'billing' => 'Billing', 'general' => 'General'];
    }

    public static function shiftOptions(): array
    {
        return ['morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening'];
    }
}
