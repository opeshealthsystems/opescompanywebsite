<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlaRule extends Model
{
    protected $fillable = ['name','ticket_type','ticket_priority','response_time_hours','resolution_time_hours','is_active'];
    protected $casts = ['is_active'=>'boolean'];

    public function tickets(): HasMany { return $this->hasMany(Ticket::class); }

    public static function priorityOptions(): array
    {
        return ['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'];
    }

    public static function forTicket(string $type, string $priority): ?self
    {
        return static::where('is_active', true)
            ->where('ticket_priority', $priority)
            ->where(fn ($q) => $q->where('ticket_type', $type)->orWhereNull('ticket_type'))
            ->orderByRaw("ticket_type IS NULL ASC")
            ->first();
    }
}
