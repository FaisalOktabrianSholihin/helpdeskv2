<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'ticket_id', 'icon', 'title', 'text', 'read_at'])]
class Notification extends Model
{
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Notifikasi milik user. Notifikasi umum (user_id null) hanya ikut untuk
     * staf; pelapor hanya melihat notifikasi yang ditujukan kepadanya.
     */
    public function scopeForUser($query, ?int $userId, bool $withBroadcast = true)
    {
        return $query->where(function ($q) use ($userId, $withBroadcast) {
            if ($withBroadcast) {
                $q->whereNull('user_id');
            }
            if ($userId) {
                $withBroadcast ? $q->orWhere('user_id', $userId) : $q->where('user_id', $userId);
            }
        });
    }

    /** Kirim notifikasi ke banyak user sekaligus. */
    public static function kirim(iterable $userIds, ?Ticket $ticket, string $icon, string $title, ?string $text = null): void
    {
        foreach (collect($userIds)->unique() as $userId) {
            static::create([
                'user_id' => $userId,
                'ticket_id' => $ticket?->id,
                'icon' => $icon,
                'title' => $title,
                'text' => $text ? mb_strimwidth($text, 0, 250, '...') : null,
            ]);
        }
    }

    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'icon' => $this->icon,
            'title' => $this->title,
            'text' => $this->text,
            'ticket' => $this->ticket_id ? '#' . $this->ticket_id : null,
            'time' => $this->created_at?->diffForHumans(),
            'unread' => is_null($this->read_at),
        ];
    }
}
