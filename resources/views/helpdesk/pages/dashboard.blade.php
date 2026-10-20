<!DOCTYPE html>
<section class="page active" id="page-dashboard">
    <div class="card"
        style="background:linear-gradient(120deg, var(--primary-50), #fff 60%); padding:20px 24px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px;">
        <div>
            <h1 style="margin:0 0 4px; font-size:19px; font-weight:800;">Selamat datang kembali, Muhammad Andre 👋
            </h1>
            <p id="dash-subtitle" style="margin:0; font-size:13px; color:var(--text-mute); font-weight:500;">Berikut
                ringkasan aktivitas
                helpdesk hari ini.</p>
        </div>
        <!-- <div
              style="background:#fff; border:1px solid var(--border); border-radius:12px; padding:10px 18px; text-align:right;">
              <div style="font-size:10.5px; color:var(--text-mute); font-weight:700;">SALDO CREDIT</div>
              <div style="font-size:16px; font-weight:800;">Rp 0</div>
            </div> -->
    </div>
    <div class="grid kpi-grid dash-kpi">
        <div class="card kpi-card dash-kpi-card kpi-link" data-kpi-filter="total" role="button" tabindex="0"
            title="Klik untuk melihat tiketnya">
            <div class="dash-kpi-label">Tiket dibuat</div>
            <div class="dash-kpi-value"><span class="count-up" data-target="0" id="kpi-total">0</span></div>
        </div>
        <div class="card kpi-card dash-kpi-card kpi-link" data-kpi-filter="selesai" role="button" tabindex="0"
            title="Klik untuk melihat tiketnya">
            <div class="dash-kpi-label">Tiket diselesaikan</div>
            <div class="dash-kpi-value"><span class="count-up" data-target="0" id="kpi-selesai">0</span></div>
        </div>
        <div class="card kpi-card dash-kpi-card kpi-link" data-kpi-filter="open" role="button" tabindex="0"
            title="Klik untuk melihat tiketnya">
            <div class="dash-kpi-label">Tiket tidak selesai</div>
            <div class="dash-kpi-value"><span class="count-up" data-target="0" id="kpi-menunggu">0</span></div>
        </div>
        <div class="card kpi-card dash-kpi-card kpi-link" data-kpi-filter="reopened" role="button" tabindex="0"
            title="Klik untuk melihat tiketnya">
            <div class="dash-kpi-label">Tiket dibuka kembali</div>
            <div class="dash-kpi-value"><span class="count-up" data-target="0" id="kpi-terbuka">0</span></div>
        </div>
    </div>
    <div class="grid grid-dash-a">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Tickets management overview</h3>
                    <div class="sub">Kelompokkan Menurut : Per Bulan</div>
                </div>
            </div>
            <div class="card-body chart-box" style="height:260px;"><canvas id="chartMonthly"></canvas></div>
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
            <div class="card">
                <div class="card-head">
                    <h3 id="dash-waiting-title">Tiket Menunggu</h3><button class="link-more"
                        data-page="tiket-list">Lihat semua</button>
                </div>
                <div class="card-body" id="dash-waiting-list" style="padding-top:8px;"></div>
            </div>
        </div>
    </div>
</section>
