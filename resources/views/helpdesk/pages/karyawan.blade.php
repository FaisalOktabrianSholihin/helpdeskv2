<!DOCTYPE html>
<section class="page" id="page-karyawan">
<div class="page-head">
<div>
<div class="breadcrumb"><a data-page="dashboard">Beranda</a> <svg class="icon" data-lucide="chevron-right" style="width:12px;height:12px;"></svg> Kontak <svg class="icon" data-lucide="chevron-right" style="width:12px;height:12px;"></svg> Karyawan</div>
<h1>Karyawan</h1>
<p>Data karyawan (sumber: database induk / HR &mdash; read only)</p>
</div>
</div>
<div class="filter-bar">
<div class="search-chip"><svg class="icon" data-lucide="search" style="width:14px;height:14px;color:var(--text-mute);"></svg><input id="karyawan-search" placeholder="Cari karyawan..."/></div>
<div class="select-wrap"><select class="filter-select" id="karyawan-filter-divisi">
<option value="">Semua Divisi</option>
</select><svg data-lucide="chevron-down"></svg></div>
</div>
<div class="card">
<div class="table-wrap">
<table>
<thead>
<tr>
<th>Nama</th>
<th>Email</th>
<th>Divisi</th>
<th>Jabatan</th>
<th>No. Telepon</th>
</tr>
</thead>
<tbody id="karyawan-body"></tbody>
</table>
</div>
</div>
</section>
