<!DOCTYPE html>
<section class="page" id="page-analitik">
    <div class="page-head">
        <div>
            <div class="breadcrumb"><a data-page="dashboard">Beranda</a> <svg class="icon" data-lucide="chevron-right"
                    style="width:12px;height:12px;"></svg> Analitik</div>
            <h1>Analitik</h1>
            <p>Analisa data tiket helpdesk</p>
        </div>
        <div class="select-wrap"><select class="filter-select">
                <option>01 Jan 2026 – 31 Des 2026</option>
                <option>Bulan ini</option>
            </select><svg data-lucide="chevron-down"></svg></div>
    </div>
    <div class="grid kpi-grid">
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:var(--primary-100); color:var(--primary);"><svg class="icon"
                        data-lucide="ticket"></svg></div>
            </div>
            <div class="kpi-value"><span class="count-up" data-target="0">0</span></div>
            <div class="kpi-label">Total Tiket</div>
        </div>
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:var(--teal-100); color:var(--teal);"><svg class="icon"
                        data-lucide="circle-check-big"></svg></div>
            </div>
            <div class="kpi-value"><span class="count-up" data-target="0">0</span></div>
            <div class="kpi-label">Tiket Selesai</div>
        </div>
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:var(--violet-100); color:var(--violet);"><svg class="icon"
                        data-lucide="timer"></svg></div>
            </div>
            <div class="kpi-value">2j 45m</div>
            <div class="kpi-label">Rata-rata Respon</div>
        </div>
        <div class="card kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:var(--amber-100); color:var(--amber);"><svg class="icon"
                        data-lucide="star"></svg></div>
            </div>
            <div class="kpi-value">4.6 <span style="font-size:14px; color:var(--text-mute); font-weight:700;">/
                    5</span></div>
            <div class="kpi-label">Kepuasan</div>
        </div>
    </div>
    <div class="grid grid-analytics">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Volume Tiket per Bulan</h3>
                    <div class="sub" id="volume-sub">Jumlah tiket masuk setiap bulan</div>
                </div>
                <div class="select-wrap"><select class="filter-select" id="volume-year" aria-label="Pilih tahun"></select><svg
                        data-lucide="chevron-down"></svg></div>
            </div>
            <div class="card-body chart-box" style="height:250px;"><canvas id="chartPeriode"></canvas></div>
        </div>
        <div class="card">
            <div class="card-head">
                <h3>Tiket Berdasarkan Tipe</h3>
            </div>
            <div class="card-body">
                <div class="chart-box" style="height:160px; display:flex; align-items:center; justify-content:center;">
                    <canvas id="chartTipe"></canvas>
                </div>
                <div id="type-legend" style="margin-top:8px;"></div>
            </div>
        </div>
    </div>
</section>
