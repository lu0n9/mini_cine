<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationCampaign extends Model
{
    use HasFactory;

    protected $table = 'notification_campaigns';

    protected $fillable = [
        'admin_id',
        'title',
        'message',
        'type',
        'target_type',
        'target_description',
        'action_text',
        'action_url',
        'recipient_count',
        'channel_email',
        'channel_web',
        'status',
        'error_message',
    ];

    protected $casts = [
        'channel_email'   => 'boolean',
        'channel_web'     => 'boolean',
        'recipient_count' => 'integer',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'new_movie'   => 'Phim mới',
            'promotion'   => 'Ưu đãi & Khuyến mãi',
            'maintenance' => 'Bảo trì hệ thống',
            'account'     => 'Tài khoản',
            default       => 'Thông báo hệ thống',
        };
    }
}

