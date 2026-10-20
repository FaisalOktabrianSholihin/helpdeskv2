<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'judul',
    'deskripsi',
    'kontak_nama',
    'karyawan_id',
    'divisi_id',
    'telepon',
    'status',
    'prioritas',
    'tipe',
    'sumber',
    'agen_id',
    'created_by',
    'closed_at',
    'reopened_count',
    'rating',
    'rated_at',
])]
class Ticket extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
            'rated_at' => 'datetime',
        ];
    }

    /** Pelapor hanya boleh melihat tiket miliknya sendiri; staf melihat semua. */
    public function scopeVisibleTo($query, User $user)
    {
        return $user->isRequester() ? $query->where('created_by', $user->id) : $query;
    }

    public function agen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agen_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    /** Kode tampilan seperti pada front-end lama, mis. "#887". */
    public function getKodeAttribute(): string
    {
        return '#' . $this->id;
    }

    /** Nama karyawan (dari db_indukk) jika kontak tertaut ke data induk. */
    public function karyawanNama(): ?string
    {
        if (! $this->karyawan_id) {
            return null;
        }

        return \App\Models\Indukk\Karyawan::find($this->karyawan_id)?->nama;
    }

    /**
     * Bentuk JSON yang dikonsumsi front-end (assets/js/app.js), dibuat mirip
     * mungkin dengan struktur data demo lama supaya render*() tidak perlu
     * ditulis ulang total.
     */
    public function toFrontendArray(): array
    {
        $this->loadMissing('agen', 'replies.user');

        return [
            'id' => $this->kode,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'kontak' => $this->karyawanNama() ?? $this->kontak_nama,
            'agen' => $this->agen?->name ?? 'Belum ditugaskan',
            'status' => $this->status,
            'prioritas' => $this->prioritas,
            'tipe' => $this->tipe,
            'sumber' => $this->sumber,
            'telepon' => $this->telepon,
            'karyawan_id' => $this->karyawan_id,
            'divisi_id' => $this->divisi_id,
            'dibuat' => \App\Support\IndoDate::format($this->created_at),
            'updated' => \App\Support\IndoDate::format($this->updated_at),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'closed_at' => optional($this->closed_at)->toIso8601String(),
            'reopened_count' => (int) ($this->reopened_count ?? 0),
            'agen_id' => $this->agen_id,
            'created_by' => $this->created_by,
            'rating' => $this->rating ? (int) $this->rating : null,
            // Sudah ada balasan dari pihak lain (agen)? -> pelapor boleh konfirmasi.
            'staff_replied' => $this->replies->contains(fn($r) => $r->user_id && $r->user_id !== $this->created_by),
            'replies' => $this->replies->map(fn($r) => [
                'id' => $r->id,
                'nama' => $r->user?->name ?? 'Sistem',
                'body' => $r->body,
                'channel' => $r->channel,
                'waktu' => \App\Support\IndoDate::format($r->created_at),
            ])->values(),
        ];
    }
}
