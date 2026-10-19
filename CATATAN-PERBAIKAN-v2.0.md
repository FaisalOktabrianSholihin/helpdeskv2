# Perbaikan Helpdesk v2.0
Salin file-file di zip ini ke project (timpa file lama, struktur folder sama), lalu jalankan:

    php artisan migrate
    php artisan db:seed --class=TicketSeeder

Setelah itu refresh browser dengan Ctrl+F5.

Revisi 8 (dashboard) sudah termasuk. Jalankan juga: php artisan migrate (menambah kolom reopened_count untuk "Tiket dibuka kembali").
