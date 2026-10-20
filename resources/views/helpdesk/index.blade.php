<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#087f5b">
    <meta name="description" content="Helpdesk internal PT. Mitratani Dua Tujuh">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dua Tujuh Helpdesk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/tickets.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/master.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/analytics.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/settings.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/revisi.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body>
    <div id="login-screen">
        <div class="login-left">
            <div class="brand-row">
                <div class="logo-mark">27</div>
                <div>
                    <div class="brand-name">PT. Mitratani Dua Tujuh</div>
                    <div class="brand-sub">Helpdesk Ticketing System</div>
                </div>
            </div>
            <h1 class="login-heading">Masuk ke akun Anda</h1>
            <p class="login-sub">Kelola tiket dukungan pelanggan dengan lebih cepat dan rapi.</p>
            <form id="login-form" autocomplete="off">
                <div class="field">
                    <label for="email">Email</label>
                    <div class="field-input">
                        <svg class="icon" data-lucide="mail"></svg>
                        <input id="email" name="login_email" placeholder="Masukkan email" required=""
                            type="email" value="" autocomplete="off" autocapitalize="off" spellcheck="false" />
                    </div>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="field-input">
                        <svg class="icon" data-lucide="lock"></svg>
                        <input id="password" name="login_password" placeholder="Masukkan password" required=""
                            type="password" value="" autocomplete="new-password" />
                        <button id="toggle-pass" type="button"><svg class="icon" data-lucide="eye"></svg></button>
                    </div>
                </div>
                <div class="login-row">
                    <label class="checkbox-row"><input checked="" type="checkbox" /> Ingat saya</label>
                    <a href="#" id="forgot-link" aria-expanded="false" aria-controls="forgot-box">Lupa
                        password?</a>
                </div>
                <button class="btn-primary" id="login-btn" type="submit">
                    <span id="login-btn-text">Masuk</span>
                    <svg class="icon spin" data-lucide="loader-2" id="login-spinner"></svg>
                </button>
                <div class="login-error" id="login-error" role="alert"></div>
                <div class="forgot-box" id="forgot-box" hidden>
                    <svg class="icon" data-lucide="life-buoy"></svg>
                    <div>
                        <strong>Lupa password?</strong>
                        <p>Silakan hubungi <b>TIM IT</b> (Teknologi Informasi) untuk reset password akun Anda. Sebutkan
                            nama dan email akun saat menghubungi.</p>
                    </div>
                </div>
            </form>
            <div class="login-footer">© 2025 PT. Mitratani Dua Tujuh.<br />All rights reserved.<div
                    class="login-version">Helpdesk Ticketing System · Versi 2.0</div>
            </div>
        </div>
        <div class="login-right">
            <div class="helpdesk-scene" aria-label="Ilustrasi alur helpdesk">
                <div class="scene-grid"></div>
                <div class="scene-glow glow-one"></div>
                <div class="scene-glow glow-two"></div>

                <div class="ticket-float ticket-a">
                    <div class="ticket-icon"><svg class="icon" data-lucide="ticket-check"></svg></div>
                    <div><strong>#0887</strong><span>Tiket baru</span></div>
                    <b class="status-pill new">BARU</b>
                </div>

                <div class="support-card">
                    <div class="support-top">
                        <div class="support-avatar"><svg class="icon" data-lucide="headphones"></svg></div>
                        <div><strong>IT Helpdesk</strong><span>Support Center</span></div>
                        <span class="online-dot"></span>
                    </div>
                    <div class="support-screen">
                        <div class="screen-header"><span>Ticket Queue</span><span class="live-label">● LIVE</span>
                        </div>
                        <div class="ticket-line"><i></i><span>Komputer tidak menyala</span><b>Proses</b></div>
                        <div class="ticket-line"><i></i><span>Akses email</span><b>Menunggu</b></div>
                        <div class="ticket-line done"><i></i><span>Printer HR</span><b>Selesai</b></div>
                    </div>
                    <div class="support-footer"><span><svg class="icon" data-lucide="clock-3"></svg> Respons
                            cepat</span><span><svg class="icon" data-lucide="shield-check"></svg> Terpantau</span>
                    </div>
                </div>

                <div class="chat-bubble bubble-one"><svg class="icon"
                        data-lucide="message-circle"></svg><span>Halo, ada kendala?</span></div>
                <div class="chat-bubble bubble-two"><span>Teknisi sedang menangani...</span><svg class="icon"
                        data-lucide="send"></svg></div>

                <div class="flow-node node-one"><span>1</span><strong>Lapor</strong><small>Buat tiket</small></div>
                <div class="flow-node node-two"><span>2</span><strong>Tangani</strong><small>Tim IT</small></div>
                <div class="flow-node node-three"><span>3</span><strong>Selesai</strong><small>Tiket ditutup</small>
                </div>
                <svg class="flow-lines" viewBox="0 0 900 620" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M145 440 C220 370 245 315 320 270" />
                    <path d="M575 250 C650 300 685 360 750 405" />
                </svg>
                <div class="scene-caption"><span class="caption-dot"></span><span>Semua permintaan IT, dalam satu
                        tempat.</span></div>
            </div>
        </div>
    </div>
    <div id="app">
        <div id="sidebar-overlay"></div>
        <!-- SIDEBAR -->
        <aside id="sidebar">
            <div class="sb-brand">
                <div class="logo-mark">27</div>
                <div class="sb-text">
                    <div class="brand-name">PT. Mitratani Dua Tujuh</div>
                    <div class="brand-sub">Helpdesk</div>
                </div>
                <button class="collapse-btn" id="sb-collapse-btn" type="button" title="Kecilkan / besarkan sidebar"
                    aria-label="Kecilkan sidebar"><svg class="icon" data-lucide="chevrons-left"
                        style="width:15px;height:15px;"></svg></button>
                <button class="sb-close-btn" id="sb-close-btn" type="button" aria-label="Tutup menu"><svg
                        class="icon" data-lucide="x" style="width:15px;height:15px;"></svg></button>
            </div>
            <nav class="sb-scroll">
                <div class="sb-label">MENU</div>
                <div class="nav-item active" data-page="dashboard" title="Dashboard">
                    <svg class="icon" data-lucide="layout-dashboard"></svg><span class="sb-text">Dashboard</span>
                </div>
                <div class="nav-item" data-page="tiket-list" title="Tiket">
                    <svg class="icon" data-lucide="ticket"></svg><span class="sb-text">Tiket</span>
                </div>
                <div class="nav-item" id="kontak-toggle" title="Kontak">
                    <svg class="icon" data-lucide="users"></svg><span class="sb-text">Kontak</span>
                    <svg class="icon chev" data-lucide="chevron-right"></svg>
                </div>
                <div class="nav-sub" id="kontak-sub">
                    <div class="nav-item" data-page="divisi" title="Divisi"><svg class="icon"
                            data-lucide="building-2"></svg><span class="sb-text">Divisi</span></div>
                    <div class="nav-item" data-page="karyawan" title="Karyawan"><svg class="icon"
                            data-lucide="contact"></svg><span class="sb-text">Karyawan</span></div>
                </div>
                <div class="nav-item" data-page="analitik" title="Analitik">
                    <svg class="icon" data-lucide="bar-chart-3"></svg><span class="sb-text">Analitik</span>
                </div>
                <div class="nav-item" data-page="users" title="Akun User">
                    <svg class="icon" data-lucide="user-cog"></svg><span class="sb-text">Akun User</span>
                </div>
                <div class="sb-label">LAINNYA</div>
                <div class="nav-item" data-page="setting" title="Setting">
                    <svg class="icon" data-lucide="settings"></svg><span class="sb-text">Setting</span>
                </div>
                <div class="nav-item" data-page="roles" title="Roles">
                    <svg class="icon" data-lucide="shield"></svg><span class="sb-text">Roles</span>
                </div>
            </nav>
        </aside>
        <!-- MAIN COLUMN -->
        <div id="main-col">
            <header id="topbar">

                <button class="icon-btn hamburger-btn" id="hamburger-btn" type="button" aria-label="Buka menu"><svg
                        class="icon" data-lucide="menu"></svg></button>
                <div class="page-title" id="topbar-title">Dashboard</div>
                <div class="topbar-actions">
                    <button class="icon-btn" id="notification-btn" title="Notifikasi"><svg class="icon"
                            data-lucide="bell"></svg><span class="notif-dot"></span></button>
                    <button class="icon-btn"><svg class="icon" data-lucide="mail"></svg></button>
                    <div class="topbar-user" id="topbar-user">
                        <div class="avatar" style="width:30px;height:30px;font-size:11px;">MA</div>
                        <div class="topbar-user-text">
                            <div class="topbar-user-name">Muhammad Andre</div>
                            <div class="topbar-user-role">Admin</div>
                        </div>
                    </div>
                    <button class="logout-btn" id="logout-btn" type="button" title="Logout"
                        aria-label="Logout"><svg class="icon" data-lucide="log-out"
                            style="width:16px;height:16px;"></svg><span>Logout</span></button>
                </div>
            </header>
            <div id="notification-panel" class="notification-panel" aria-live="polite"></div>
            <div id="content">
                <!-- ================= DASHBOARD ================= -->
                @include('helpdesk.pages.dashboard')
                <!-- ================= TIKET LIST ================= -->
                @include('helpdesk.pages.tiket-list')
                <!-- ================= TIKET DETAIL ================= -->
                @include('helpdesk.pages.tiket-detail')
                <!-- ================= CETAK TIKET ================= -->
                @include('helpdesk.pages.cetak-tiket')
                <!-- ================= DIVISI ================= -->
                @include('helpdesk.pages.divisi')
                <!-- ================= KARYAWAN ================= -->
                @include('helpdesk.pages.karyawan')
                <!-- ================= ANALITIK ================= -->
                @include('helpdesk.pages.analitik')
                <!-- ================= SETTING ================= -->
                @include('helpdesk.pages.setting')
                <!-- ================= ROLES ================= -->
                @include('helpdesk.pages.roles')
                <!-- ================= USERS ================= -->
                @include('helpdesk.pages.users')
            </div>
        </div>
    </div>
    <div id="toast"><span class="toast-ic"><svg class="icon" data-lucide="check"
                style="width:14px;height:14px;color:#fff;"></svg></span><span id="toast-text">Berhasil disimpan</span>
    </div>
    <div id="modal-overlay">
        <div class="modal" id="modal-box">
            <div class="modal-head">
                <h3 id="modal-title">Judul</h3>
                <button class="modal-close" id="modal-close-btn"><svg class="icon" data-lucide="x"
                        style="width:15px;height:15px;"></svg></button>
            </div>
            <div class="modal-body" id="modal-body"></div>
            <div class="modal-foot" id="modal-foot"></div>
        </div>
    </div>
    <div id="page-loader" aria-hidden="true">
        <div class="loader-orbit"><span></span><span></span><span></span></div>
    </div>
    <script>
        /* Diisi server (Blade) supaya app.js tahu, tanpa perlu tebak-tebakan,
         apakah sesi login Laravel masih aktif saat halaman ini pertama dimuat. */
        window.__HELPDESK_BOOT = {
            authenticated: @json(auth()->check())
        };
    </script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/modules/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/modules/tickets.js') }}"></script>
    <script src="{{ asset('assets/js/modules/master.js') }}"></script>
    <script src="{{ asset('assets/js/modules/analytics.js') }}"></script>
    <script src="{{ asset('assets/js/modules/settings.js') }}"></script>
</body>

</html>
