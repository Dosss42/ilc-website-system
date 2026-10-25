<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'status', 'ip_address',
    ];

    /**
     * Everything the Contact Messages inbox needs (admin/sections/messages.blade.php), the same for
     * Admin and Super Admin: the latest 200 messages (paged 10 per page in the browser) and the counts.
     * Counts are separate COUNT queries, not taken from the capped list, so they stay right past 200.
     */
    public static function inboxData(): array
    {
        return [
            'contactMessages'      => static::orderByDesc('created_at')->limit(200)->get(),
            'unreadMessagesCount'  => static::where('status', 'unread')->count(),
            'totalMessagesCount'   => static::count(),
            'readMessagesCount'    => static::where('status', 'read')->count(),
            'repliedMessagesCount' => static::where('status', 'replied')->count(),
        ];
    }
}
