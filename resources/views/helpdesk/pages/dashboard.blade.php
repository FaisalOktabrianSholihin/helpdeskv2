<!DOCTYPE html>
<section class="page active" id="page-dashboard">
    <div class="card"
        style="background:linear-gradient(120deg, var(--primary-50), #fff 60%); padding:20px 24px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px;">
        <div>
            <h1 style="margin:0 0 4px; font-size:19px; font-weight:800;">Selamat datang kembali, Muhammad Andre 👋
            </h1>
            <p style="margin:0; font-size:13px; color:var(--text-mute); font-weight:500;">Berikut ringkasan aktivitas
                helpdesk hari ini.</p>
        </div>
        <!-- <div
              style="background:#fff; border:1px solid var(--border); border-radius:12px; padding:10px 18px; text-align:right;">
              <div style="font-size:10.5px; color:var(--text-mute); font-weight:700;">SALDO CREDIT</div>
              <div style="font-size:16px; font-weight:800;">Rp 0</div>
            </div> -->
    </div>
    <div class="grid kpi-grid">
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:var(--primary-100); color:var(--primary);"><svg class="icon"
                        data-lucide="ticket"></svg></div>
                <div class="kpi-trend" style="background:var(--teal-100); color:var(--teal);"><svg class="icon"
                        data-lucide="arrow-up-right" style="width:12px;height:12px;"></svg>12%</div>
            </div>
            <div class="kpi-value"><span class="count-up" data-target="128" id="kpi-total">0</span></div>
            <div class="kpi-label">Total Tiket · dari bulan lalu</div>
        </div>
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:var(--teal-100); color:var(--teal);"><svg class="icon"
                        data-lucide="circle-check-big"></svg></div>
                <div class="kpi-trend" style="background:var(--teal-100); color:var(--teal);"><svg class="icon"
                        data-lucide="arrow-up-right" style="width:12px;height:12px;"></svg>8%</div>
            </div>
            <div class="kpi-value"><span class="count-up" data-target="24" id="kpi-selesai">0</span></div>
            <div class="kpi-label">Tiket Selesai · dari bulan lalu</div>
        </div>
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:var(--amber-100); color:var(--amber);"><svg class="icon"
                        data-lucide="clock"></svg></div>
                <div class="kpi-trend" style="background:var(--rose-100); color:var(--rose);"><svg class="icon"
                        data-lucide="arrow-down-right" style="width:12px;height:12px;"></svg>6%</div>
            </div>
            <div class="kpi-value"><span class="count-up" data-target="8" id="kpi-menunggu">0</span></div>
            <div class="kpi-label">Tiket Menunggu · dari bulan lalu</div>
        </div>
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:var(--rose-100); color:var(--rose);"><svg class="icon"
                        data-lucide="circle-alert"></svg></div>
                <span class="badge b-rendah" style="font-size:10.5px;">Butuh aksi</span>
            </div>
            <div class="kpi-value"><span class="count-up" data-target="4" id="kpi-terbuka">0</span></div>
            <div class="kpi-label">Tiket Terbuka</div>
        </div>
    </div>
    <div class="grid grid-dash-a">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Tiket Per Bulan</h3>
                    <div class="sub">Volume tiket masuk sepanjang tahun</div>
                </div>
                <div class="select-wrap"><select class="filter-select">
                        <option>Tahun ini</option>
                        <option>Tahun lalu</option>
                    </select><svg data-lucide="chevron-down"></svg></div>
            </div>
            <div class="card-body chart-box" style="height:230px;"><canvas id="chartMonthly"></canvas></div>
        </div>
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Tiket Berdasarkan Status</h3>
                    <div class="sub">Distribusi status saat ini</div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-box" style="height:150px; display:flex; align-items:center; justify-content:center;">
                    <canvas id="chartStatus"></canvas>
                </div>
                <div id="status-legend" style="margin-top:6px;"></div>
            </div>
        </div>
    </div>
    <div class="grid grid-dash-b">
        <div class="card">
            <div class="card-head">
                <h3>Tiket Terbaru</h3><button class="link-more" data-page="tiket-list">Lihat semua <svg class="icon"
                        data-lucide="arrow-right" style="width:13px;height:13px;"></svg></button>
            </div>
            <div class="card-body table-wrap" style="padding-top:6px;">
                <table>
                    <thead>
                        <tr>
                            <th>ID Tiket</th>
                            <th>Judul</th>
                            <th>Agen</th>
                            <th>Status</th>
                            <th>Prioritas</th>
                            <th>Dibuat</th>
                        </tr>
                    </thead>
                    <tbody id="dash-recent-body"></tbody>
                </table>
            </div>
        </div>
        <div>
            <div class="card" style="margin-bottom:18px;">
                <div class="card-head">
                    <h3>Tiket Menunggu</h3><button class="link-more" data-page="tiket-list">Lihat semua</button>
                </div>
                <div class="card-body" id="dash-waiting-list" style="padding-top:8px;"></div>
            </div>
            <div class="card">
                <div class="card-head">
                    <h3>Pengumuman</h3><button class="link-more">Lihat semua</button>
                </div>
                <div class="announce" style="margin-top:14px;">
                    <div class="announce-title"><svg class="icon" data-lucide="megaphone"
                            style="width:15px;height:15px;color:var(--primary);"></svg>Maintenance Sistem</div>
                    <p>Layanan helpdesk akan diperbarui pada 17 Mei 2025 pukul 23:00–02:00 WIB. Mohon simpan pekerjaan
                        Anda sebelum jadwal tersebut.</p>
                </div>
            </div>
        </div>
    </div>
</section>
