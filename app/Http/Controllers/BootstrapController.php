<?php

namespace App\Http\Controllers;

use App\Models\Indukk\Divisi;
use App\Models\Indukk\Karyawan;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Dipanggil sekali setelah login (dan saat reload halaman ketika sesi masih
 * aktif) untuk mengisi semua array yang dulu hardcode di app.js: tickets,
 * divisiList, karyawanList, rolesList, usersList, settingsState, notifications.
 * Setiap section hanya disertakan kalau user punya permission "view"-nya,
 * supaya tidak membocorkan data yang memang tidak boleh dia akses.
 */
class BootstrapController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $can = fn (string $resource) => $user->hasPermission($resource, 'view');

        return response()->json([
            'ok' => true,
            'user' => app(AuthController::class)->userPayload($user),
            'tickets' => $can('tiket') ? Ticket::orderByDesc('id')->get()->map->toFrontendArray() : [],
            'divisi' => $can('divisi') ? Divisi::withCount('karyawans')->orderBy('nama_divisi')->get()->map(fn ($d) => [
                'id' => $d->id, 'nama' => $d->nama_divisi, 'jumlahKaryawan' => $d->karyawans_count,
            ]) : [],
            'karyawan' => $can('karyawan') ? Karyawan::with('jabatan:id,nama_jabatan', 'divisi:id,nama_divisi')
                ->select(Karyawan::SEPERLUNYA_COLUMNS)->orderBy('nama')->get()->map(fn ($k) => [
                    'id' => $k->id, 'nik' => $k->nik, 'nama' => $k->nama, 'email' => $k->email, 'tlp' => $k->no_hp,
                    'divisiId' => $k->divisi_id, 'divisiNama' => $k->divisi_nama, 'jabatan' => $k->jabatan_nama,
                    'statusAktif' => $k->status_aktif,
                ]) : [],
            'roles' => $can('roles') ? Role::withCount('users')->get()->map(fn ($r) => [
                'id' => $r->id, 'nama' => $r->nama, 'permissions' => $r->permissions ?? [], 'jml' => $r->users_count,
            ]) : [],
            'users' => $can('users') ? User::with('role')->get()->map(fn ($u) => [
                'id' => $u->id, 'nama' => $u->name, 'email' => $u->email, 'roleId' => $u->role_id,
                'divisiId' => $u->divisi_id, 'divisiNama' => $u->divisiNama(), 'active' => (bool) $u->active,
            ]) : [],
            'settings' => $can('setting') ? (function () {
                $s = Setting::current();

                return [
                    'company' => $s->company, 'email' => $s->email, 'categories' => $s->categories,
                    'statuses' => $s->statuses, 'backup' => $s->backup, 'general' => $s->general,
                ];
            })() : null,
            'notifications' => Notification::forUser($user->id)->orderByDesc('created_at')->limit(20)->get()->map(fn ($n) => [
                'id' => $n->id, 'icon' => $n->icon, 'title' => $n->title, 'text' => $n->text,
                'time' => $n->created_at?->diffForHumans(), 'unread' => is_null($n->read_at),
            ]),
        ]);
    }
}
