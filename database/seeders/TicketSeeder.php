<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TicketSeeder extends Seeder
{
    /**
     * Data tiket sesuai dokumen asli (folder "helpdesk data", 28 PDF).
     *
     * ID mengikuti nomor tiket sumber (#847, #879 ... #906); nomor #848-#878
     * dan #890 memang tidak ada di dokumen sumber. Setiap tiket membawa
     * komentar/balasan asli dari PDF (tabel ticket_replies).
     *
     * Aman dijalankan berulang:  php artisan db:seed --class=TicketSeeder
     */
    public function run(): void
    {
        $andre = DB::table('users')->where('email', 'andre@mitratani27.co.id')->value('id');
        $fallback = DB::table('users')->where('email', 'agent@mitratani27.co.id')->value('id');
        $andre = $andre ?: $fallback;

        // Agen tiket #897 pada dokumen sumber adalah "R. Ribi Arifin".
        // Dibuat sebagai akun NONAKTIF (tidak bisa login) agar nama agen tampil benar.
        $ribi = User::firstOrCreate(
            ['email' => 'ribi.arifin@mitratani27.co.id'],
            [
                'name' => 'R. Ribi Arifin',
                'password' => Hash::make(Str::random(32)),
                'role_id' => Role::where('nama', 'Agent')->value('id'),
                'divisi_id' => null,
                'active' => false,
            ]
        )->id;

        $agents = ['andre' => $andre, 'ribi' => $ribi];

        // Format: [id, judul, deskripsi, kontak, status, prioritas, tipe, sumber, dibuat, agen, [[waktu, channel, isi], ...]]
        $rows = [
            [847, 'Komputer', 'komputer admin grading terkena air, tolong dicek terlebih dahulu', 'Umi Hoironi', 'selesai', 'rendah', 'Problem', 'Telepon', '2025-03-15 12:01:00', 'andre', [
                ['2025-03-17 09:23', 'Email keluar', 'Hi Umi Hoironi,

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi, komputer mengalami kerusakan pada mainboardnya yang disebabkan oleh terkenanya air yang mengenai perangkat. Kami menyarankan penggantian mainboard beserta isinya, yakni prosessor, SSD, dan RAM pada komputer (1 paket) dengan spesifikasi sebagai berikut :
1. Prosessor core i3 gen 12
2. Mobo Asus/MSI
3. RAM 4 GB
4. SSD 240GB kingston
Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
                ['2026-04-29 15:06', 'Email keluar', 'Komponen tambahan yakni :
1. Monitor 19inch (minimal)
2. UPS
3. PSU Deepcool 500W
Disarankan untuk motherboard tersedia port untuk VGA serta HDMI bawaan.'],
            ]],
            [879, 'Hardisk', 'HDD admin RND tidak bisa terbaca, tolong dicek', 'Pungky', 'baru', 'rendah', 'Problem', 'Telepon', '2026-01-05 08:17:00', 'andre', [
                ['2026-01-05 10:24', 'Email keluar', 'Hi Pungky,

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi, hardisk sudah memasuki presentasi kesehatan yakni di 40% yang dimana nilai tersebut sudah memasuki indikasi bad sector dalam hardisk. Kami menyarankan penggantian perangkat hardisk dengan kapasitas 1 TB. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [880, 'Komputer', 'komputer kepala departemen admin domestik tidak bisa menyala, tolong segera dicek', 'Heri Dwi Prasetyo', 'baru', 'rendah', 'Problem', 'Telepon', '2026-01-12 10:22:00', 'andre', [
                ['2026-01-12 10:29', 'Email keluar', 'Hi Heri Dwi Prasetyo,

Setelah dilakukan pengecekan oleh bagian teknologi informasi, komputer mengalami kerusakan pada hardisk sehingga tidak dapat masuk ke dalam windows dan UPS mengalami kerusakan pada baterai. Kami menyarankan penggantian UPS dan penggantian hardisk menjadi SSD, demikian laporan yang dapat kami sampaikan atas perhatiannya disampaikan terima kasih.'],
            ]],
            [881, 'Printer', 'printer saya tidak bisa dibuat mengcopy file, tolong dicek', 'Trio Jerry', 'baru', 'rendah', 'Problem', 'Telepon', '2026-01-19 11:03:00', 'andre', [
                ['2026-01-20 11:02', 'Email keluar', 'Hi Trio Jerry,

Setelah dilakukan pengecekan oleh bagian teknolodi informasi, indikasi printer yakni lampu power dan copy berkedip bersamaan. permasalahan terjadi pada ic transistornya, kami menyarankan perbaikan pada perangkat tersebut supaya printer dapat berfungsi sebagaimana mestinya. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [882, 'Printer', 'printer SPI tidak bisa keluar kertasnya
tolong dicek', 'Imanda', 'baru', 'rendah', 'Problem', 'Telepon', '2026-01-21 09:00:00', 'andre', [
                ['2026-01-23 18:43', 'Email keluar', 'Hi Imanda,

Setelah dilakukan oleh bagian teknologi informasi, printer mengalami kerusakan pada roller penarik kertas. kami menyarankan penggantian perangkat tersebut. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [883, 'UPS', 'UPS tim ekspor sebanyak 6 unit tidak dapat menyimpan daya listrik, tolong dicek', 'Abdul Wahid', 'baru', 'rendah', 'Problem', 'Telepon', '2026-02-04 15:40:00', 'andre', [
                ['2026-02-04 16:02', 'Email keluar', 'Hi Abdul Wahid,

Setlah dilakukan pengecekan oleh bagian teknologi informasi, UPS mengalami kerusakan pada baterai sehingga tidak dapat menyimpan daya listrik. Kami menyarankan pengadaan baru UPS dikarenakan harga perbaikan hampir sama dengan pembelian baru. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [884, 'Printer', 'printer saya untuk mencetak tidak maskimal dan ada njelebretnya, tolong dicek ya', 'Vina', 'baru', 'rendah', 'Problem', 'Telepon', '2026-02-11 09:23:00', 'andre', [
                ['2026-02-11 11:19', 'Email keluar', 'Hi Vina,

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi, printer mengalami kerusakan pada head dan perlu dilakukan perbaikan. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [885, 'Komputer', 'tolong buatkan rekomendasi perangkat komputer untuk karyawan baru, terima kasih.', 'Bagus Tantowi', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-02-11 09:23:00', 'andre', [
                ['2026-02-11 14:58', 'Email keluar', 'Hi Bagus Tantowi,

Setelah dilakukan riset lebih lanjut, kami merekomendasikan spesifikasi perangkat komputer sebagai berikut :
1. Motherboard ASUS/MSI
2. RAM 8GB DDR4 (2 keping)
3. PSU Deepcool 500 W
4. Prosessor Core i3 gen 12
5. SSD NVME 256GB
Demikian rekomendasi yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [886, 'Faceprint', 'mesin faceprint pengolahan untuk absensi kemarin terjadi error, tolong buatkan rekomendasi untuk back up mesin ya', 'Vina', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-02-13 10:25:00', 'andre', [
                ['2026-02-13 10:46', 'Email keluar', 'Hi Vina,

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi, mesin mengalami error dikarenakan terkena air hujan yang merembes dari atap. Kami menyarankan penambahan mesih cadangan untuk backup apabila mesin terjadi kerusakan, Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [887, 'UPS', 'keempat UPS SDM tidak bisa menyimpan daya, tolong dicek', 'Vina', 'baru', 'rendah', 'Problem', 'Telepon', '2026-02-19 07:56:00', 'andre', [
                ['2026-02-19 08:46', 'Email keluar', 'Hi Vina,

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi, UPS mengalami kerusakan pada penyimpanan (baterai). Kami menyarankan pengadaan baru UPS dikarenakan biaya perbaikan hampir mendekati harga pembelian baru UPS. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [888, 'Printer', 'printer kami tidak dapat mengeluarkan tinta biru, tolong dicek ya', 'Basecamp 5 (Wafa)', 'baru', 'rendah', 'Problem', 'Telepon', '2026-02-19 09:18:00', 'andre', [
                ['2026-02-20 15:00', 'Email keluar', 'Hi Basecamp 5 (Wafa),

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi printer mengalami kerusakan pada bagian catridge warna biru. Kami menyarankan perbaikan printer supaya printer dapat digunakan sebagaimana mestinya. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [889, 'Printer', 'printer kami warna hitamnya tidak keluar', 'Alfin Ari Wibowo', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-03-26 13:56:00', 'andre', [
                ['2026-03-26 14:09', 'Email keluar', 'Hi Alfin Ari Wibowo,

Setelah dilakukan pengecekan oleh bagian teknologi informasi, printer mengalami kerusakan pada penarik tinta. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [891, 'Monitor', 'tolong rekomendasikan monitor komputer karyawan baru', 'Bagus Tantowi', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-04-13 15:51:00', 'andre', [
                ['2026-04-13 16:01', 'Email keluar', 'Hi Bagus Tantowi,

Kami merekomendasikan monitor dengan lebar 19 inch dengan merk LG. Demikian rekomendasi yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [892, 'UPS', 'UPS kepunyaan admin umum tiba-tiba mati, tolong dicek ya', 'M. Sholeh', 'baru', 'rendah', 'Problem', 'Telepon', '2026-04-15 15:10:00', 'andre', [
                ['2026-04-15 16:30', 'Email keluar', 'Hi M. Sholeh,

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi, UPS mengalami kerusakan pada power dan baterai yang tidak bisa menyimpan daya listrik. Kami menyarankan pengadaan UPS baru dikarenakan biaya perbaikan mendekati dengan harga pembelian UPS baru. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [893, 'Printer', 'printer laserjet pengolahan error, tolong dicek', 'Syaiful Rizal Haq', 'baru', 'rendah', 'Problem', 'Telepon', '2026-04-17 09:44:00', 'andre', [
                ['2026-04-20 08:15', 'Email keluar', 'Hi Syaiful Rizal Haq,

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi, printer mengalami kerusakan pada roller penarik kertasnya. Kami menyarankan penggantian perangkat yang rusak supaya pinter dapat digunakan sebagaimana mestinya. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [894, 'Printer', 'tolong cekkan printer saya, ada suara kencang di dalamnya', 'Hanapi', 'baru', 'rendah', 'Problem', 'Telepon', '2026-04-28 14:44:00', 'andre', [
                ['2026-04-29 08:32', 'Email keluar', 'Hi Hanapi,

Setelah dilakukan pengecekan oleh Bagian Teknologi Informasi, printer mengalami kerusakan pada roller dan sensor penarik kertas. Kami menyarankan perbaikan pada perangkat yang rusak supaya printer dapat digunakan sebagaimana mestinya. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [895, 'Komputer', 'tolong rekomendasikan komputer untuk proses administrasi di divisi budidaya', 'Tiara Maharani C', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-04-29 13:52:00', 'andre', [
                ['2026-04-29 15:19', 'Email keluar', 'Hi Tiara Maharani C,

Kami menyarankan pengadaan yakni prosessor, SSD, dan RAM pada komputer (1 paket) dengan spesifikasi sebagai berikut :
1. Prosessor core i3 gen 12
2. Mobo Asus/MSI
3. RAM 4 GB
4. SSD 240GB kingston
5. PSU Deepcooll 500W
6. Monitor 19inc (minimal)
7. UPS
Disarankan untuk motherboard tersedia port untuk VGA serta HDMI bawaan. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [896, 'Laptop', 'tolong berikan rekomendasi laptop untuk menunjang kinerja sekper', 'Erison Caesar', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-04-29 15:29:00', 'andre', [
                ['2026-04-30 15:49', 'Email keluar', 'Hi Erison Caesar,

Berdasarkan data yang sudah bagian teknologi informasi collect, kami menyarankan spesifikasi dan merk laptop sebagai berikut :
1. Merk Laptop Asus/Hp (disarankan lebih baik HP)
2. Prosesor Core Inter i5/Ryzen 5
3. Kapasitas SSD 512GB
4. Connector Type C to all
Demikian rekomendasi yang dapat kami usulkan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [897, 'Printer', 'printer saya tintanya bocor, tolong dicek', 'Dhinta', 'baru', 'rendah', 'Problem', 'Telepon', '2026-05-18 15:29:00', 'ribi', [
                ['2026-05-18 16:42', 'Email keluar', 'Hi Dhinta,

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi, printer mengalami kebocoran pada tanki tintanya kami menyarankan perbaikan supaya printer dapat digunakan sebagaimana mestinya. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [898, 'Printer', 'tolong rekomendasikan pemakaian aset IPAD dan TAB Sekper', 'M. Erison CS', 'baru', 'rendah', 'Problem', 'Telepon', '2026-06-18 15:29:00', 'andre', [
                ['2026-06-18 16:42', 'Email keluar', 'Hi M. Erison CS,

Setelah dilakukan kajian ulang, kami menyarankan penggunaan perangkat IPAD dan TAB untuk ruang lingkup kerja yang ada di SEKPER dengan rincian User sebagai berikut :
TAB Samsung S9 : digunakan oleh Kepala Sekper (menunjang pekerjaan SEKPER)
IPAD Gen 10 : digunakan oleh Kepala Departemen Sekper (notulensi, meninjau laporan spredsheet dan pekerjaan lainnya)
Demikian rekomendasi ini kami buat, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [899, 'UPS', '2 UPS QC tidak berfungsi, tolong dicek ya', 'Sevty', 'baru', 'rendah', 'Problem', 'Telepon', '2026-07-01 09:10:00', 'andre', [
                ['2026-07-01 10:30', 'Email keluar', 'Hi Sevty,

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi, 2 UPS mengalami kerusakan pada power dan baterai yang tidak bisa menyimpan daya listrik. Kami menyarankan pengadaan UPS baru dikarenakan biaya perbaikan mendekati dengan harga pembelian UPS baru. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [900, 'Printer', 'printer l5190 pengolahan error, tolong dicek', 'Syaiful Rizal Haq', 'baru', 'rendah', 'Problem', 'Telepon', '2026-04-03 09:44:00', 'andre', [
                ['2026-04-06 08:15', 'Email keluar', 'Hi Syaiful Rizal Haq,

Setelah dilakukan pengecekan oleh bagian Teknologi Informasi, printer mengalami kerusakan pada roller penarik kertasnya. Kami menyarankan penggantian perangkat yang rusak supaya pinter dapat digunakan sebagaimana mestinya. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [901, 'Komputer', 'tolong rekomendasikan 3 komputer untuk kebutuhan staf keuangan dan pic cold storage', 'Ika Sri Wahyuni', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-07-29 10:12:00', 'andre', [
                ['2026-07-29 11:19', 'Email keluar', 'Hi Ika Sri Wahyuni,

Kami menyarankan pengadaan yakni prosessor, SSD, dan RAM pada komputer (3 paket) dengan spesifikasi sebagai berikut :
1. Prosessor core i3 gen 12
2. Mobo Asus/MSI
3. RAM 4 GB
4. SSD 120GB kingston (atau ssd merk lexar)
5. HDD 500GB
6. PSU Deepcooll 500W
7. Monitor 19inc LG (minimal)
8. Keymouse ( Keyboard & Mouse) Logitech
9. UPS
Disarankan untuk motherboard tersedia port untuk VGA serta HDMI bawaan. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [902, 'Laptop', 'tolong berikan rekomendasi laptop untuk menunjang kinerja divisi pemasaran ekspor', 'Bagus Tantowi', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-07-30 14:29:00', 'andre', [
                ['2026-07-30 15:49', 'Email keluar', 'Hi Bagus Tantowi,

Berdasarkan data yang sudah bagian teknologi informasi kumpulkam, kami menyarankan spesifikasi dan merk laptop sebagai berikut :
1. Merk Laptop Asus/Hp (disarankan lebih baik HP)
2. Prosesor Core Intel i3 (gen 12) atau yang setara
3. Kapasitas SSD 512GB
4. Connector Type C to all
Demikian rekomendasi yang dapat kami usulkan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [903, 'Komputer', 'tolong berikan rekomendasi komputer untuk menunjang kinerja divisi pengadaan', 'Selamet Riyadi', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-08-31 08:29:00', 'andre', [
                ['2026-08-31 13:09', 'Email keluar', 'Hi Selamet Riyadi,

Berdasarkan data yang sudah bagian teknologi informasi kumpulkan, kami menyarankan spesifikasi komputer sebagai berikut :
1. PSU 500W Deepcooll (minimal)
2. Prosesor Core Intel i3 (gen 12) atau yang setara
3. Motherboard ASUS/MSI (ada port vga dan hdmi)
4. SSD 256GB NVME
5. RAM 8GB
Demikian rekomendasi yang dapat kami usulkan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [904, 'Router', 'tolong berikan rekomendasi router untuk ruangan direksi, ikbi, dan ruang rapat edamame', 'R. Ribi Arifin', 'baru', 'rendah', 'Feature Request', 'Telepon', '2026-08-31 10:29:00', 'andre', [
                ['2026-08-31 14:09', 'Email keluar', 'Hi R. Ribi Arifin,

Berdasarkan data yang sudah bagian teknologi informasi kumpulkam, kami menyarankan spesifikasi dan merk router ruiji RG-EW6000GX REYEE sebanyak 3 buah.
Demikian rekomendasi yang dapat kami usulkan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [905, 'Printer', 'tolong cekkan printer saya, karena ndak bisa digunakan untuk print', 'Heri Dwi Prasetyo', 'baru', 'tinggi', 'Problem', 'Product Management', '2026-09-10 07:16:00', 'andre', [
                ['2026-09-10 07:18', 'Balasan baru', 'Setelah dilakukan pengecekan oleh bagian TI, printer mengalami kerusakan pada sensor penarik kertasnya. Kami meyarankan penggantian sensor kertas pada printer supaya printer dapat menarik kertas dengan sempurna. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
            [906, 'Printer', 'printer kami tintanya meluber dan hasil cetaknya kurang jelas, tolong dicek ya', 'Bagus Tantowi', 'baru', 'sedang', 'Problem', 'Product Management', '2026-09-16 08:29:00', 'andre', [
                ['2026-09-16 09:04', 'Balasan baru', 'Setelah dilakukan pengecekan oleh bagian teknologi informasi, printer mengalami indikasi kerusakan pada catridge atau head printer. Maka dari itu kami menyarankan penggantian perangkat tersebut. Demikian laporan yang dapat kami sampaikan, atas perhatiannya disampaikan terima kasih.'],
            ]],
        ];

        // Hapus hanya data demo awal yang dibuat oleh seeder lama.
        DB::table('tickets')->where('id', '<=', 8)->delete();

        foreach ($rows as [$id, $judul, $deskripsi, $kontak, $status, $prioritas, $tipe, $sumber, $tgl, $agen, $replies]) {
            $createdAt = Carbon::parse($tgl);
            $replyTimes = array_map(fn ($r) => Carbon::parse($r[0]), $replies);
            $lastActivity = $replyTimes ? max($replyTimes) : $createdAt;

            DB::table('tickets')->updateOrInsert(['id' => $id], [
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
                'agen_id' => $agents[$agen] ?? $andre,
                'created_by' => $andre,
                'created_at' => $createdAt,
                'updated_at' => $lastActivity,
                'closed_at' => $status === 'selesai' ? ($replyTimes ? min($replyTimes) : $createdAt) : null,
            ]);

            // Ganti komentar lama tiket ini dengan komentar dari dokumen sumber.
            DB::table('ticket_replies')->where('ticket_id', $id)->delete();
            foreach ($replies as [$waktu, $channel, $isi]) {
                DB::table('ticket_replies')->insert([
                    'ticket_id' => $id,
                    'user_id' => $andre,
                    'body' => $isi,
                    'channel' => $channel,
                    'created_at' => Carbon::parse($waktu),
                    'updated_at' => Carbon::parse($waktu),
                ]);
            }
        }
    }
}
