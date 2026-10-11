<?php

namespace App\Http\Controllers;

use App\Models\Indukk\Karyawan;
use Illuminate\Http\Request;

/**
 * Read-only: data Karyawan milik db_indukk, dan hanya kolom "seperlunya"
 * (lihat Karyawan::SEPERLUNYA_COLUMNS) yang ditarik dari database induk —
 * bukan seluruh profil HR (alamat, KTP, tanggal lahir, dst).
 */
class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('karyawan', 'view'), 403);

        $karyawan = Karyawan::with('jabatan:id,nama_jabatan', 'divisi:id,nama_divisi')
            ->select(Karyawan::SEPERLUNYA_COLUMNS)
            ->orderBy('nama')
            ->get()
            ->map(fn ($k) => [
                'id' => $k->id,
                'nik' => $k->nik,
                'nama' => $k->nama,
                'email' => $k->email,
                'tlp' => $k->no_hp,
                'divisiId' => $k->divisi_id,
                'divisiNama' => $k->divisi_nama,
                'jabatan' => $k->jabatan_nama,
                'statusAktif' => $k->status_aktif,
            ]);

        return response()->json(['ok' => true, 'karyawan' => $karyawan]);
    }
}
