<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['name','slug','type','subject','body','variables','is_active'];
    protected $casts = ['variables'=>'array','is_active'=>'boolean'];

    public static function typeOptions(): array
    {
        return [
            'welcome'           => 'Welcome',
            'invoice_sent'      => 'Invoice Sent',
            'invoice_reminder'  => 'Invoice Reminder',
            'ticket_created'    => 'Ticket Created',
            'ticket_reply'      => 'Ticket Reply',
            'leave_approval'    => 'Leave Approval',
            'leave_rejection'   => 'Leave Rejection',
            'password_reset'    => 'Password Reset',
            'announcement'      => 'Announcement',
            'contract_sent'     => 'Contract Sent',
            'quote_sent'        => 'Quote Sent',
            'general'           => 'General',
        ];
    }

    public static function forType(string $type): ?self
    {
        return static::where('type', $type)->where('is_active', true)->first();
    }

    public function render(array $variables): array
    {
        $subject = $this->subject;
        $body    = $this->body;
        foreach ($variables as $key => $value) {
            $subject = str_replace('{{'.$key.'}}', $value, $subject);
            $body    = str_replace('{{'.$key.'}}', $value, $body);
        }
        return ['subject' => $subject, 'body' => $body];
    }
}
