<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('tiket', 'view'), 403);

        $tickets = Ticket::orderByDesc('id')->get()->map->toFrontendArray();

        return response()->json(['ok' => true, 'tickets' => $tickets]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('tiket', 'create'), 403);

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
        $data['created_by'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'baru';

        $ticket = Ticket::create($data);

        return response()->json(['ok' => true, 'ticket' => $ticket->toFrontendArray()], 201);
    }

    public function update(Request $request, Ticket $ticket)
    {
        abort_unless($request->user()->hasPermission('tiket', 'edit'), 403);

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

        // Simpan waktu pertama kali tiket ditutup agar histori backlog per
        // bulan tidak berubah hanya karena tiket diedit setelah selesai.
        if (($data['status'] ?? null) === 'selesai' && $ticket->status !== 'selesai' && ! $ticket->closed_at) {
            $data['closed_at'] = now();
        } elseif (($data['status'] ?? null) !== 'selesai' && $ticket->status === 'selesai') {
            $data['closed_at'] = null;
        }

        $ticket->update($data);

        return response()->json(['ok' => true, 'ticket' => $ticket->fresh()->toFrontendArray()]);
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        abort_unless($request->user()->hasPermission('tiket', 'delete'), 403);

        $ticket->delete();

        return response()->json(['ok' => true]);
    }

    public function reply(Request $request, Ticket $ticket)
    {
        abort_unless($request->user()->hasPermission('tiket', 'reply'), 403);

        $data = $request->validate([
            'body' => ['required', 'string'],
            'channel' => ['nullable', 'string', 'max:100'],
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
            'channel' => $data['channel'] ?? 'Balasan baru',
        ]);
        $ticket->touch();

        return response()->json(['ok' => true, 'ticket' => $ticket->fresh()->toFrontendArray()]);
    }

    public function close(Request $request, Ticket $ticket)
    {
        abort_unless($request->user()->hasPermission('tiket', 'close'), 403);

        if ($ticket->status !== 'selesai') {
            $ticket->update([
                'status' => 'selesai',
                'closed_at' => $ticket->closed_at ?: now(),
            ]);
        }

        return response()->json(['ok' => true, 'ticket' => $ticket->fresh()->toFrontendArray()]);
    }
}
