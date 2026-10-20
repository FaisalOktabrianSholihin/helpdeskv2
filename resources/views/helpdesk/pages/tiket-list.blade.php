<!DOCTYPE html>
<section class="page" id="page-tiket-list">
    <div class="page-head">
        <div>
            <div class="breadcrumb"><a data-page="dashboard">Beranda</a> <svg class="icon" data-lucide="chevron-right"
                    style="width:12px;height:12px;"></svg> Tiket</div>
            <h1>Tiket</h1>
            <p>Kelola semua tiket helpdesk</p>
        </div>
        <button class="btn btn-blue" id="new-ticket-btn" data-action-permission="tiket.create"><svg class="icon"
                data-lucide="plus" style="width:15px;height:15px;"></svg>Buat Tiket</button>
    </div>
    <div class="filter-bar">
        <div class="search-chip"><svg class="icon" data-lucide="search"
                style="width:14px;height:14px;color:var(--text-mute);"></svg><input id="ticket-search"
                placeholder="Cari tiket..." /></div>
        <div class="select-wrap">
            <select class="filter-select" id="filter-status">
                <option value="">Semua Status</option>
                <option value="baru">Baru</option>
                <option value="menunggu">Menunggu</option>
                <option value="proses">Proses</option>
                <option value="selesai">Selesai</option>
            </select><svg data-lucide="chevron-down"></svg>
        </div>
        <div class="select-wrap">
            <select class="filter-select" id="filter-prioritas">
                <option value="">Semua Prioritas</option>
                <option value="rendah">Rendah</option>
                <option value="sedang">Sedang</option>
                <option value="tinggi">Tinggi</option>
            </select><svg data-lucide="chevron-down"></svg>
        </div>
        <div class="select-wrap">
            <select class="filter-select" id="filter-tipe">
                <option value="">Semua Tipe</option>
                <option value="Incident">Incident</option>
                <option value="Request">Request</option>
                <option value="Problem">Problem</option>
                <option value="Feature Request">Feature Request</option>
            </select><svg data-lucide="chevron-down"></svg>
        </div>
    </div>
    <div class="quick-chip" id="ticket-quick-chip" hidden>
        <svg class="icon" data-lucide="filter" style="width:13px;height:13px;"></svg>
        <span id="ticket-quick-label"></span>
        <button type="button" id="ticket-quick-clear" aria-label="Hapus filter">&times;</button>
    </div>
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID Tiket</th>
                        <th>Judul</th>
                        <th>Agen</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th>Dibuat</th>
                        <th style="width:90px;" id="ticket-aksi-th">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ticket-list-body"></tbody>
            </table>
        </div>
        <div class="pagination">
            <span id="ticket-count-label">Menampilkan 8 dari 8 tiket</span>
            <div class="page-nums">
                <div class="page-num"><svg class="icon" data-lucide="chevron-left"
                        style="width:13px;height:13px;"></svg></div>
                <div class="page-num active">1</div>
                <div class="page-num"><svg class="icon" data-lucide="chevron-right"
                        style="width:13px;height:13px;"></svg></div>
            </div>
        </div>
    </div>
</section>
