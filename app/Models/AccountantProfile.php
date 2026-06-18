<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountantProfile extends Model
{
    protected $fillable = ['user_id', 'accounting_specialization', 'certifications', 'bio'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function specializationOptions(): array
    {
        return ['general' => 'General', 'tax' => 'Tax', 'payroll' => 'Payroll', 'audit' => 'Audit'];
    }
}
