<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_id', 'user_id', 'filename', 'original_filename',
        'mime_type', 'file_size', 'path',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' Mo';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' Ko';
        return $bytes . ' octets';
    }
}
