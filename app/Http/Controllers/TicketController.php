<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user->hasPermission('tiket', 'view'), 403);

        $tickets = Ticket::visibleTo($user)->orderByDesc('id')->get()->map->toFrontendArray();

        return response()->json(['ok' => true, 'tickets' => $tickets]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user->hasPermission('tiket', 'create'), 403);

        if ($user->isRequester()) {
            // Pelapor hanya mengisi judul & deskripsi. Nama kontak, telepon,
            // divisi diisi otomatis dari akun/data karyawan; prioritas, status,
            // tipe, dan agen ditentukan oleh agen — bukan oleh pelapor.
            $input = $request->validate([
                'judul' => ['required', 'string', 'max:255'],
                'deskripsi' => ['nullable', 'string'],
            ]);
            $karyawan = $user->karyawanRecord();

            $data = [
                'judul' => $input['judul'],
                'deskripsi' => $input['deskripsi'] ?? null,
                'kontak_nama' => $karyawan?->nama ?? $user->name,
                'karyawan_id' => $karyawan?->id,
                'divisi_id' => $karyawan?->divisi_id ?? $user->divisi_id,
                'telepon' => $karyawan?->no_hp ?: '-',
                'status' => 'baru',
                'prioritas' => 'sedang',
                'tipe' => 'Incident',
                'sumber' => 'Web',
                'agen_id' => null,
            ];
        } else {
            $data = $request->validate([
                'judul' => ['required', 'string', 'max:255'],
                'deskripsi' => ['nullable', 'string'],
                'kontak_nama' => ['required', 'string', 'max:255'],
                'karyawan_id' => ['nullable', 'integer'],
                'divisi_id' => ['nullable', 'integer'],
                'telepon' => ['nullable', 'string', 'max:50'],
                'status' => ['nullable', 'in:baru,menunggu,proses,selesai'],
                'prioritas' => ['required', 'in:rendah,sedang,tinggi'],
                'tipe' => ['required', 'string', 'max:100'],
                'sumber' => ['required', 'string', 'max:100'],
                'agen_id' => ['nullable', 'integer', 'exists:users,id'],
            ]);
            $data['status'] = $data['status'] ?? 'baru';
        }
        $data['created_by'] = $user->id;

        $ticket = Ticket::create($data);

        if ($user->isRequester()) {
            // Beritahu semua agen lewat lonceng di header.
            Notification::kirim(
                User::agents([$user->id])->pluck('id'),
                $ticket,
                'ticket',
                "Tiket baru {$ticket->kode} dari {$ticket->kontak_nama}",
                $ticket->judul
            );
        } elseif ($ticket->agen_id) {
            $this->announceAssignment($ticket, $user);
        }

        return response()->json(['ok' => true, 'ticket' => $ticket->toFrontendArray()], 201);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        abort_unless($user->hasPermission('tiket', 'edit') && ! $user->isRequester(), 403);

        $data = $request->validate([
            'judul' => ['sometimes', 'required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'kontak_nama' => ['sometimes', 'required', 'string', 'max:255'],
            'karyawan_id' => ['nullable', 'integer'],
            'divisi_id' => ['nullable', 'integer'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'in:baru,menunggu,proses,selesai'],
            'prioritas' => ['sometimes', 'required', 'in:rendah,sedang,tinggi'],
            'tipe' => ['sometimes', 'required', 'string', 'max:100'],
            'sumber' => ['sometimes', 'required', 'string', 'max:100'],
            'agen_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $oldStatus = $ticket->status;
        $oldAgen = $ticket->agen_id;

        // Simpan waktu pertama kali tiket ditutup agar histori backlog per
        // bulan tidak berubah hanya karena tiket diedit setelah selesai.
        if (($data['status'] ?? null) === 'selesai' && $ticket->status !== 'selesai' && ! $ticket->closed_at) {
            $data['closed_at'] = now();
        } elseif (isset($data['status']) && $data['status'] !== 'selesai' && $ticket->status === 'selesai') {
            // Tiket yang sudah selesai dibuka lagi -> dihitung "dibuka kembali".
            $data['closed_at'] = null;
            $data['reopened_count'] = ((int) $ticket->reopened_count) + 1;
            $data['rating'] = null;
            $data['rated_at'] = null;
        }

        $ticket->update($data);
        $ticket = $ticket->fresh();

        if (! empty($data['agen_id']) && (int) $data['agen_id'] !== (int) $oldAgen) {
            $this->announceAssignment($ticket, $user);
        }
        if ($ticket->status !== $oldStatus) {
            $this->notifyCreator($ticket, $user, 'refresh-cw', "Status tiket {$ticket->kode} diperbarui", 'Status sekarang: ' . ucfirst($ticket->status));
        }

        return response()->json(['ok' => true, 'ticket' => $ticket->toFrontendArray()]);
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        // Tiket yang sudah terkirim tidak boleh dihapus oleh pelapor.
        abort_if($user->isRequester(), 403, 'Tiket yang sudah terkirim tidak dapat dihapus.');
        abort_unless($user->hasPermission('tiket', 'delete'), 403);

        $ticket->delete();

        return response()->json(['ok' => true]);
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        // Pelapor tidak boleh membalas komentar tiketnya sendiri.
        abort_if($user->isRequester(), 403, 'Pelapor tidak dapat membalas tiket.');
        abort_unless($user->hasPermission('tiket', 'reply'), 403);

        $data = $request->validate([
            'body' => ['required', 'string'],
            'channel' => ['nullable', 'string', 'max:100'],
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => $data['body'],
            'channel' => $data['channel'] ?? 'Balasan baru',
        ]);

        // Tiket belum ada yang pegang -> yang pertama membalas otomatis jadi penanggung jawab.
        if (! $ticket->agen_id && $ticket->status !== 'selesai') {
            $ticket->agen_id = $user->id;
            $ticket->save();
            $this->announceAssignment($ticket, $user);
        }
        if ($ticket->status === 'baru') {
            $ticket->status = 'proses';
            $ticket->save();
        }
        $ticket->touch();

        $this->notifyCreator($ticket, $user, 'message-circle', "Balasan baru pada tiket {$ticket->kode}", "{$user->name}: {$data['body']}");

        return response()->json(['ok' => true, 'ticket' => $ticket->fresh()->toFrontendArray()]);
    }

    /** Agen mengambil tiket yang belum punya penanggung jawab. */
    public function take(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        abort_if($user->isRequester(), 403);
        abort_unless($user->hasPermission('tiket', 'view'), 403);

        if ($ticket->agen_id && $ticket->agen_id !== $user->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Tiket ini sudah diambil oleh ' . ($ticket->agen?->name ?? 'agen lain') . '.',
            ], 409);
        }
        if ($ticket->status === 'selesai') {
            return response()->json(['ok' => false, 'message' => 'Tiket sudah selesai.'], 422);
        }

        if ($ticket->agen_id !== $user->id) {
            $ticket->agen_id = $user->id;
            if (in_array($ticket->status, ['baru', 'menunggu'], true)) {
                $ticket->status = 'proses';
            }
            $ticket->save();
            $this->announceAssignment($ticket, $user);
        }

        return response()->json(['ok' => true, 'ticket' => $ticket->fresh()->toFrontendArray()]);
    }

    public function close(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        abort_unless($user->hasPermission('tiket', 'close') && ! $user->isRequester(), 403);

        if ($ticket->status !== 'selesai') {
            $ticket->loadMissing('creator.role');
            $needsConfirm = $ticket->creator && $ticket->creator->isRequester() && ! $ticket->rating;

            if ($needsConfirm) {
                // Tiket pelapor: agen menandai sudah ditangani, tinggal menunggu
                // konfirmasi + rating dari pelapor (status baru "selesai" setelah itu).
                $ticket->update(['status' => 'menunggu']);
                $this->notifyCreator($ticket, $user, 'circle-check', "Tiket {$ticket->kode} sudah ditangani", 'Mohon konfirmasi dan beri rating pada tiket Anda.');
            } else {
                $ticket->update(['status' => 'selesai', 'closed_at' => $ticket->closed_at ?: now()]);
            }
        }

        return response()->json(['ok' => true, 'ticket' => $ticket->fresh()->toFrontendArray()]);
    }

    /** Pelapor mengonfirmasi tiket selesai + rating wajib (1-5). */
    public function confirm(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        abort_unless($ticket->created_by === $user->id, 403);

        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5']], [
            'rating.required' => 'Rating wajib dipilih.',
            'rating.between' => 'Rating harus antara 1 sampai 5.',
        ]);

        if ($ticket->rating) {
            return response()->json(['ok' => false, 'message' => 'Tiket ini sudah diberi rating.'], 422);
        }

        $handled = in_array($ticket->status, ['menunggu', 'selesai'], true)
            || $ticket->replies()->where('user_id', '!=', $user->id)->exists();
        if (! $handled) {
            return response()->json(['ok' => false, 'message' => 'Tiket belum ditangani agen.'], 422);
        }

        $ticket->update([
            'status' => 'selesai',
            'closed_at' => $ticket->closed_at ?: now(),
            'rating' => $data['rating'],
            'rated_at' => now(),
        ]);

        $recipients = $ticket->agen_id ? [$ticket->agen_id] : User::agents()->pluck('id');
        Notification::kirim(
            $recipients,
            $ticket,
            'star',
            "Tiket {$ticket->kode} dikonfirmasi selesai",
            "Rating dari {$ticket->kontak_nama}: {$data['rating']}/5"
        );

        return response()->json(['ok' => true, 'ticket' => $ticket->fresh()->toFrontendArray()]);
    }

    /** Kabari agen lain + pelapor siapa yang mengerjakan tiket ini. */
    private function announceAssignment(Ticket $ticket, User $actor): void
    {
        $ticket->loadMissing('agen');
        $agen = $ticket->agen?->name ?? 'seorang agen';

        Notification::kirim(
            User::agents([$actor->id])->pluck('id'),
            $ticket,
            'user-check',
            "Tiket {$ticket->kode} dikerjakan oleh {$agen}",
            $ticket->judul
        );
        $this->notifyCreator($ticket, $actor, 'user-check', "Tiket {$ticket->kode} sedang ditangani", "Agen penanggung jawab: {$agen}");
    }

    private function notifyCreator(Ticket $ticket, User $actor, string $icon, string $title, ?string $text = null): void
    {
        if ($ticket->created_by && $ticket->created_by !== $actor->id) {
            Notification::kirim([$ticket->created_by], $ticket, $icon, $title, $text);
        }
    }
}
