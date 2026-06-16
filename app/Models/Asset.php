<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $fillable = ['reference','name','category','serial_number','brand','model','purchase_date','purchase_price','current_value','currency','location','assigned_to','status','warranty_expires','notes'];
    protected $casts = ['purchase_date'=>'date','warranty_expires'=>'date','purchase_price'=>'decimal:2','current_value'=>'decimal:2'];

    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }

    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::where('reference', 'like', "AST-{$year}-%")->orderByDesc('reference')->value('reference');
        $next = $last ? (int) preg_replace('/.*-/', '', $last) + 1 : 1;
        return "AST-{$year}-" . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public static function categoryOptions(): array
    {
        return ['laptop'=>'Laptop','desktop'=>'Desktop','mobile'=>'Mobile Device','server'=>'Server','furniture'=>'Furniture','vehicle'=>'Vehicle','software_license'=>'Software License','other'=>'Other'];
    }

    public static function statusOptions(): array
    {
        return ['active'=>'Active','in_repair'=>'In Repair','retired'=>'Retired','disposed'=>'Disposed'];
    }
}
