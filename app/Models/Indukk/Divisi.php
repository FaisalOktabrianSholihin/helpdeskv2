<?php

namespace App\Models\Indukk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Data Divisi milik db_indukk (sistem HR pusat). Helpdesk hanya MEMBACA
 * tabel ini — tidak ada endpoint create/update/delete untuk model ini.
 */
class Divisi extends Model
{
    protected $connection = 'indukk';

    protected $table = 'divisis';

    // Read-only dari sisi helpdesk: cegah tidak sengaja ter-mass-assign/save.
    protected $guarded = ['*'];

    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'divisi_id');
    }
}
