<?php

namespace App\Http\Controllers;

use App\Models\Indukk\Divisi;
use Illuminate\Http\Request;

/**
 * Read-only: data Divisi milik db_indukk. Tidak ada store/update/destroy
 * di sini secara sengaja — manajemen data divisi dilakukan di sistem induk.
 */
class DivisiController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('divisi', 'view'), 403);

        $divisi = Divisi::withCount('karyawans')
            ->orderBy('nama_divisi')
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'nama' => $d->nama_divisi,
                'jumlahKaryawan' => $d->karyawans_count,
            ]);

        return response()->json(['ok' => true, 'divisi' => $divisi]);
    }
}
