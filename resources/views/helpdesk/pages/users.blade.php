<!DOCTYPE html>
<section class="page" id="page-users">
<div class="page-head">
<div>
<div class="breadcrumb"><a data-page="dashboard">Beranda</a> <svg class="icon" data-lucide="chevron-right" style="width:12px;height:12px;"></svg> Akun User</div>
<h1>Akun User</h1>
<p>Kelola akun login, role, dan divisi pengguna helpdesk.</p>
</div>
<button class="btn btn-blue" id="new-user-btn"><svg class="icon" data-lucide="user-plus" style="width:15px;height:15px;"></svg>Tambah Akun</button>
</div>
<div class="card users-info-card">
<div class="users-info-icon"><svg class="icon" data-lucide="shield-check"></svg></div>
<div><strong>Akses berbasis Role</strong><p>Setiap akun hanya dapat melihat menu yang diizinkan oleh role-nya. Admin dapat dibatasi berdasarkan divisi.</p></div>
</div>
<div class="card">
<div class="table-wrap">
<table>
<thead><tr><th>Pengguna</th><th>Role</th><th>Divisi</th><th>Status</th><th style="width:110px">Aksi</th></tr></thead>
<tbody id="users-body"></tbody>
</table>
</div>
</div>
</section>
