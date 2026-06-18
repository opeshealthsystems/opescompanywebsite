<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerProfile extends Model
{
    protected $fillable = ['user_id', 'management_level', 'department_id', 'bio'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function levelOptions(): array
    {
        return ['team_lead' => 'Team Lead', 'senior_manager' => 'Senior Manager', 'director' => 'Director'];
    }
}
