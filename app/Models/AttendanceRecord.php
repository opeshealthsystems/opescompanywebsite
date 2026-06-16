<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = ['user_id','date','check_in','check_out','status','hours_worked','notes'];
    protected $casts = ['date'=>'date'];

    public function employee(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    public static function statusOptions(): array
    {
        return [
            'present' => 'Present',
            'absent'  => 'Absent',
            'late'    => 'Late',
            'half_day'=> 'Half Day',
            'on_leave'=> 'On Leave',
            'remote'  => 'Remote',
            'holiday' => 'Holiday',
        ];
    }

    public function getFormattedHoursAttribute(): string
    {
        if (!$this->check_in || !$this->check_out) return '—';
        $checkin  = \Carbon\Carbon::parse($this->check_in);
        $checkout = \Carbon\Carbon::parse($this->check_out);
        if ($checkout->lt($checkin)) return '—';
        $diff = $checkin->diff($checkout);
        return $diff->h . 'h ' . $diff->i . 'm';
    }
}
