<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    /**
     * Data tiket hasil transkripsi dari tiket asli yang diberikan.
     *
     * ID sengaja mengikuti nomor tiket sumber (#847, #879 ... #906),
     * termasuk gap #848-#878 dan #890 karena nomor tersebut memang tidak
     * terdapat pada kumpulan dokumen sumber.
     */
    public function run(): void
    {
        $andre = DB::table('users')->where('email', 'andre@mitratani27.co.id')->value('id');
        $agent = DB::table('users')->where('email', 'agent@mitratani27.co.id')->value('id');
        $creator = $andre ?: $agent;

        $rows = [
            [847, 'Komputer', 'komputer admin grading terkena air, tolong dicek terlebih dahulu', 'Umi Hoironi', 'selesai', 'rendah', 'Problem', 'Telepon', '2025-03-15 12:01:00', 'Muhammad Andre Sahrindra'],
            [879, 'Hardisk', 'HDD admin RND tidak bisa terbaca, tolong dicek', 'Pungky', 'baru', 'rendah', 'Problem', 'Telepon', '2026-01-05 08:17:00', 'Muhammad Andre Sahrindra'],
            [880, 'Komputer', 'komputer kepala departemen admin domestik tidak bisa menyala, tolong segera dicek', 'Heri Dwi Prasetyo', 'baru', 'rendah', 'Problem', 'Telepon', '2026-01-12 10:22:00', 'Muhammad Andre Sahrindra'],
            [881, 'Printer', 'printer saya tidak bisa dibuat mengcopy file, tolong dicek', 'Trio Jerry', 'baru', 'rendah', 'Problem', 'Telepon', '2026-01-19 11:03:00', 'Muhammad Andre Sahrindra'],
            [882, 'Printer', 'printer SPI tidak bisa keluar kertasnya, tolong dicek', 'Imanda', 'baru', 'rendah', 'Problem', 'Telepon', '2026-01-21 09:00:00', 'Muhammad Andre Sahrindra'],
            [883, 'UPS', 'UPS tim ekspor sebanyak 6 unit tidak dapat menyimpan daya listrik, tolong dicek', 'Abdul Wahid', 'baru', 'rendah', 'Problem', 'Telepon', '2026-02-04 15:40:00', 'Muhammad Andre Sahrindra'],
            [884, 'Printer', 'printer saya untuk mencetak tidak maksimal dan ada njelebretnya, tolong dicek ya', 'Vina', 'baru', 'rendah', 'Problem', 'Telepon', '2026-02-11 09:23:00', 'Muhammad Andre Sahrindra'],
            [885, 'Komputer', 'tolong buatkan rekomendasi perangkat komputer untuk karyawan baru, terima kasih', 'Bagus Tantowi', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-02-11 09:23:00', 'Muhammad Andre Sahrindra'],
            [886, 'Faceprint', 'mesin faceprint pengolahan untuk absensi kemarin terjadi error, tolong buatkan rekomendasi untuk back up mesin ya', 'Vina', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-02-13 10:25:00', 'Muhammad Andre Sahrindra'],
            [887, 'Printer', 'printer saya tidak bisa digunakan untuk mencetak, tolong dicek', 'Vina', 'baru', 'rendah', 'Problem', 'Telepon', '2026-02-19 07:56:00', 'Muhammad Andre Sahrindra'],
            [888, 'Printer', 'printer BDY bermasalah, tolong dicek', 'Basecamp 5 (Wafa)', 'baru', 'rendah', 'Problem', 'Telepon', '2026-02-19 09:18:00', 'Muhammad Andre Sahrindra'],
            [889, 'Printer', 'tolong buatkan rekomendasi printer untuk kebutuhan QC', 'Alfin Ari Wibowo', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-03-26 13:56:00', 'Muhammad Andre Sahrindra'],
            [891, 'Monitor', 'tolong buatkan rekomendasi monitor untuk kebutuhan ekspor', 'Bagus Tantowi', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-04-13 15:51:00', 'Muhammad Andre Sahrindra'],
            [892, 'UPS', 'UPS umum bermasalah, tolong dicek', 'M. Sholeh', 'baru', 'rendah', 'Problem', 'Telepon', '2026-04-15 15:10:00', 'Muhammad Andre Sahrindra'],
            [893, 'Printer', 'printer PENG error, tolong dicek', 'Syaiful Rizal Haq', 'baru', 'rendah', 'Problem', 'Telepon', '2026-04-17 09:44:00', 'Muhammad Andre Sahrindra'],
            [894, 'Printer', 'printer SDM bermasalah, tolong dicek', 'Hanapi', 'baru', 'rendah', 'Problem', 'Telepon', '2026-04-28 14:44:00', 'Muhammad Andre Sahrindra'],
            [895, 'Komputer', 'tolong berikan rekomendasi komputer untuk kebutuhan BDY', 'Tiara Maharani C', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-04-29 13:52:00', 'Muhammad Andre Sahrindra'],
            [896, 'Laptop', 'tolong berikan rekomendasi laptop untuk kebutuhan SEKPER', 'Erison Caesar', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-04-29 15:29:00', 'Muhammad Andre Sahrindra'],
            [897, 'Printer', 'printer saya tintanya bocor, tolong dicek', 'Dhinta', 'baru', 'rendah', 'Problem', 'Telepon', '2026-05-18 15:29:00', 'Muhammad Andre Sahrindra'],
            [898, 'Printer', 'tolong rekomendasikan pemakaian aset IPAD dan TAB Sekper', 'M. Erison CS', 'baru', 'rendah', 'Problem', 'Telepon', '2026-06-18 15:29:00', 'Muhammad Andre Sahrindra'],
            [899, 'UPS', '2 UPS QC tidak berfungsi, tolong dicek ya', 'Sevty', 'baru', 'rendah', 'Problem', 'Telepon', '2026-07-01 09:10:00', 'Muhammad Andre Sahrindra'],
            [900, 'Printer', 'printer L5190 pengolahan error, tolong dicek', 'Syaiful Rizal Haq', 'baru', 'rendah', 'Problem', 'Telepon', '2026-04-03 09:44:00', 'Muhammad Andre Sahrindra'],
            [901, 'Komputer', 'tolong rekomendasikan 3 komputer untuk kebutuhan staf keuangan dan PIC cold storage', 'Ika Sri Wahyuni', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-07-29 10:12:00', 'Muhammad Andre Sahrindra'],
            [902, 'Laptop', 'tolong berikan rekomendasi laptop untuk menunjang kinerja divisi pemasaran ekspor', 'Bagus Tantowi', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-07-30 14:29:00', 'Muhammad Andre Sahrindra'],
            [903, 'Komputer', 'tolong berikan rekomendasi komputer untuk menunjang kinerja divisi pengadaan', 'Selamet Riyadi', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-08-31 08:29:00', 'Muhammad Andre Sahrindra'],
            [904, 'Router', 'tolong berikan rekomendasi router untuk ruangan direksi, IKBI, dan ruang rapat edamame', 'R. Ribi Arifin', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-08-31 10:29:00', 'Muhammad Andre Sahrindra'],
            [905, 'Printer', 'tolong cekkan printer saya, karena ndak bisa digunakan untuk print', 'Heri Dwi Prasetyo', 'baru', 'tinggi', 'Problem', 'Product Management', '2026-09-10 07:16:00', 'Andre'],
            [906, 'Printer', 'printer kami tintanya meluber dan hasil cetaknya kurang jelas, tolong dicek ya', 'Bagus Tantowi', 'baru', 'sedang', 'Problem', 'Product Management', '2026-09-16 08:29:00', 'Andre'],
        ];

        // Hapus hanya data demo awal yang dibuat oleh seeder lama.
        DB::table('tickets')->where('id', '<=', 8)->delete();

        foreach ($rows as [$id, $judul, $deskripsi, $kontak, $status, $prioritas, $tipe, $sumber, $tgl, $agenNama]) {
            $createdAt = Carbon::parse($tgl);
            $agenId = strcasecmp($agenNama, 'Andre') === 0 ? ($andre ?: $agent) : $andre;

            $payload = [
                'judul' => $judul,
                'deskripsi' => $deskripsi,
                'kontak_nama' => $kontak,
                'karyawan_id' => null,
                'divisi_id' => null,
                'telepon' => null,
                'status' => $status,
                'prioritas' => $prioritas,
                'tipe' => $tipe,
                'sumber' => $sumber,
                'agen_id' => $agenId,
                'created_by' => $creator,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'closed_at' => $status === 'selesai' ? $createdAt : null,
            ];

            DB::table('tickets')->updateOrInsert(['id' => $id], $payload);
        }
    }
}
