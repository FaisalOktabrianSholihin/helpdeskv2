
/**
 * Dua Tujuh Helpdesk — application core
 * Page markup is intentionally split into /pages/*.html.
 */
function initLoginFlow() {
  const toggle = document.getElementById('toggle-pass');
  const form = document.getElementById('login-form');
  if (!toggle || !form || form.dataset.initialized === '1') return;
  form.dataset.initialized = '1';

  toggle.addEventListener('click', () => {
    const pw = document.getElementById('password');
    const icon = document.querySelector('#toggle-pass svg');
    if (pw.type === 'password') { pw.type = 'text'; icon?.setAttribute('data-lucide', 'eye-off'); }
    else { pw.type = 'password'; icon?.setAttribute('data-lucide', 'eye'); }
    if (window.lucide) lucide.createIcons();
  });

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('login-btn');
    const txt = document.getElementById('login-btn-text');
    const spin = document.getElementById('login-spinner');
    const error = document.getElementById('login-error');
    const email = document.getElementById('email')?.value.trim().toLowerCase();
    const password = document.getElementById('password')?.value;
    if (error) error.textContent = '';
    if (!email || !password) return;

    btn.disabled = true; txt.textContent = 'Memproses...'; spin.style.display = 'block';
    if (window.lucide) lucide.createIcons();

    try {
      await window.__helpdeskReady;
      const result = await window.__helpdeskLogin?.(email, password);
      if (!result?.ok) {
        if (error) error.textContent = result?.message || 'Email atau password tidak sesuai.';
        btn.disabled = false; txt.textContent = 'Masuk'; spin.style.display = 'none';
        if (window.lucide) lucide.createIcons();
        return;
      }

      const loginScreen = document.getElementById('login-screen');
      loginScreen.classList.add('success');
      document.body.classList.add('login-success');
      const burst = document.createElement('div');
      burst.className = 'login-burst';
      burst.innerHTML = Array.from({length: 18}, (_, i) => `<i style="--i:${i}"></i>`).join('');
      loginScreen.appendChild(burst);
      if (typeof window.__helpdeskShowPage === 'function') window.__helpdeskShowPage('dashboard');

      setTimeout(() => {
        loginScreen.classList.add('hide');
        document.getElementById('app').classList.add('show');
        window.__helpdeskRefreshIdentity?.();
        setTimeout(() => window.__helpdeskToast?.(`Login berhasil. Selamat datang, ${result.user.nama}!`, 'sparkles'), 250);
      }, 650);
    } catch (err) {
      console.error(err);
      if (error) error.textContent = 'Aplikasi belum siap. Silakan coba lagi.';
      btn.disabled = false; txt.textContent = 'Masuk'; spin.style.display = 'none';
      if (window.lucide) lucide.createIcons();
    }
  });
}

async function bootHelpdesk() {
  const pageLoader = document.getElementById('page-loader');
  // Catatan migrasi Laravel: dahulu di sini ada fetch('pages/*.html') untuk
  // menyusun #content secara dinamis (versi HTML statis). Di Laravel, setiap
  // halaman sudah dirender langsung oleh server lewat @include Blade
  // (lihat resources/views/helpdesk/index.blade.php), jadi tidak perlu fetch lagi.
  pageLoader?.classList.remove('show');

  lucide.createIcons();
  if (window.__HELPDESK_BOOT?.authenticated) {
    document.body.classList.add('login-success');
    document.getElementById('login-screen')?.classList.add('hide');
    document.getElementById('app')?.classList.add('show');
    showPage('dashboard');
  }

    /* ============ API HELPER ============
       Semua data sekarang berasal dari backend Laravel (2 database: db_helpdesk
       untuk tiket/roles/users/settings, db_indukk untuk divisi & karyawan —
       read-only). Tidak ada lagi localStorage sebagai sumber data.
    ============ */
    function csrfToken() { return document.querySelector('meta[name="csrf-token"]')?.content || ''; }
    async function apiFetch(url, options = {}) {
      const opts = Object.assign({ credentials: 'same-origin' }, options);
      opts.headers = Object.assign({ 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() }, options.headers || {});
      if (opts.body && typeof opts.body !== 'string') {
        opts.body = JSON.stringify(opts.body);
        opts.headers['Content-Type'] = 'application/json';
      }
      const res = await fetch(url, opts);
      let data = null;
      try { data = await res.json(); } catch (e) { /* respon kosong, mis. 204 */ }
      if (!res.ok) {
        const err = new Error(data?.message || 'Permintaan gagal diproses.');
        err.status = res.status; err.data = data;
        throw err;
      }
      return data;
    }

    /* ============ STATE ============
       Diisi async lewat loadBootstrapData() (dipanggil setelah login berhasil,
       atau langsung saat boot jika sesi Laravel masih aktif — lihat
       window.__HELPDESK_BOOT di bawah).
    ============ */
    let currentUser = null;
    let tickets = [];
    let divisiList = [];
    let karyawanList = [];

    /* ============ GRANULAR PERMISSIONS ============
       Format permission: resource.action
       Divisi & Karyawan sengaja hanya "view" — datanya ditarik read-only dari
       db_indukk, jadi tidak ada create/edit/delete untuk 2 resource ini.
    ============ */
    const permissionCatalog = [
      { resource:'dashboard', label:'Dashboard', actions:[['view','Lihat']] },
      { resource:'tiket', label:'Tiket', actions:[['view','Lihat'],['create','Buat'],['edit','Edit'],['delete','Hapus'],['reply','Balas'],['close','Tutup'],['print','Cetak']] },
      { resource:'divisi', label:'Divisi', actions:[['view','Lihat']] },
      { resource:'karyawan', label:'Karyawan', actions:[['view','Lihat']] },
      { resource:'analitik', label:'Analitik', actions:[['view','Lihat']] },
      { resource:'users', label:'Akun User', actions:[['view','Lihat'],['create','Tambah'],['edit','Edit'],['delete','Hapus']] },
      { resource:'setting', label:'Setting', actions:[['view','Lihat'],['edit','Edit']] },
      { resource:'roles', label:'Roles', actions:[['view','Lihat'],['create','Tambah'],['edit','Edit'],['delete','Hapus'],['permissions','Atur Hak Akses']] },
    ];
    const menuPermissions = permissionCatalog.map(p => [p.resource, p.label]);
    const allPermissionKeys = permissionCatalog.flatMap(p => p.actions.map(a => `${p.resource}.${a[0]}`));
    /* Struktur ini mengikuti persis urutan & pengelompokan menu di sidebar
       (label "MENU" / "LAINNYA", dan submenu "Kontak" berisi Divisi + Karyawan),
       supaya halaman Roles & Hak Akses terasa seperti "peta" dari sidebar. */
    const menuSections = [
      { label: 'MENU', entries: [
          { resource: 'dashboard' },
          { resource: 'tiket' },
          { group: 'Kontak', resources: ['divisi', 'karyawan'] },
          { resource: 'analitik' },
          { resource: 'users' },
        ] },
      { label: 'LAINNYA', entries: [
          { resource: 'setting' },
          { resource: 'roles' },
        ] },
    ];
    function permissionGroupHtml(resource, existingPerms, attr) {
      const group = permissionCatalog.find(p => p.resource === resource);
      if (!group) return '';
      return `<div class="permission-group"><div class="permission-group-head"><strong>${group.label}</strong><button type="button" class="link-btn" data-${attr}-all="${group.resource}">Pilih semua</button></div><div class="permission-actions">${group.actions.map(([action, label]) => { const key = `${group.resource}.${action}`; return `<label class="permission-action"><input type="checkbox" data-${attr}="${key}" ${existingPerms.includes(key) ? 'checked' : ''}><span>${label}</span><code>${key}</code></label>`; }).join('')}</div></div>`;
    }
    /* attr: 'perm' (modal Atur Hak Akses) atau 'newperm' (form Tambah/Edit Role) */
    function renderPermissionBuilder(existingPerms, attr) {
      return menuSections.map(section => {
        const body = section.entries.map(entry => {
          if (entry.group) {
            const inner = entry.resources.map(r => permissionGroupHtml(r, existingPerms, attr)).join('');
            return `<div class="permission-subsection"><div class="permission-subsection-label">${entry.group}</div>${inner}</div>`;
          }
          return permissionGroupHtml(entry.resource, existingPerms, attr);
        }).join('');
        return `<div class="permission-section"><div class="permission-section-label">${section.label}</div>${body}</div>`;
      }).join('');
    }
    const oldToNewPermission = {
      dashboard:'dashboard.view','tiket-list':'tiket.view','divisi':'divisi.view','karyawan':'karyawan.view',
      analitik:'analitik.view',users:'users.view',setting:'setting.view',roles:'roles.view'
    };
    function normalizePermissions(list){
      const out=[];
      (Array.isArray(list)?list:[]).forEach(key=>{
        const mapped=oldToNewPermission[key] || key;
        if(allPermissionKeys.includes(mapped)) out.push(mapped);
      });
      return [...new Set(out)];
    }
    const defaultSuperPermissions = [...allPermissionKeys];
    let rolesList = [];
    let usersList = [];
    let settingsState = {
      company:{name:'PT. Mitratani Dua Tujuh', email:'helpdesk@mitratani27.co.id', phone:'0331-xxxxxx', address:'Jember, Jawa Timur'},
      email:{host:'smtp.mitratani27.co.id', port:'587', sender:'helpdesk@mitratani27.co.id', enabled:true},
      categories:['Incident','Request','Problem','Feature Request'],
      statuses:['Baru','Menunggu','Proses','Selesai'],
      backup:{last:'Belum pernah', frequency:'Harian'},
      general:{timezone:'Asia/Jakarta', language:'Bahasa Indonesia', autoRefresh:true}
    };
    const notifications = [];

    let currentTicketId = null;

    const statusBadge = { baru: 'b-baru', menunggu: 'b-menunggu', proses: 'b-proses', selesai: 'b-selesai' };
    const statusLabel = { baru: 'Baru', menunggu: 'Menunggu', proses: 'Proses', selesai: 'Selesai' };
    const prioBadge = { rendah: 'b-rendah', sedang: 'b-sedang', tinggi: 'b-tinggi' };
    const prioLabel = { rendah: 'Rendah', sedang: 'Sedang', tinggi: 'Tinggi' };

    function esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }
    function ticketNumId(kode) { return String(kode).replace('#', ''); }
    function initials(name) { return (name || '?').split(' ').slice(0, 2).map(s => s[0]).join('').toUpperCase(); }
    function nowStr() {
      const d = new Date();
      const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      const pad = n => String(n).padStart(2, '0');
      return `${pad(d.getDate())} ${bulan[d.getMonth()]} ${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }
    function divisiName(id) { const d = divisiList.find(x => x.id === id); return d ? d.nama : '—'; }

    /* ============ RENDER: TICKETS ============ */
    function ticketRow(t, withActions) {
      return `<tr class="clickable" data-open="${t.id}">
    <td class="cell-id">${esc(t.id)}</td>
    <td class="cell-strong">${esc(t.judul)}</td>
    <td>${esc(t.agen)}</td>
    <td><span class="badge ${statusBadge[t.status]}"><span class="dot"></span>${statusLabel[t.status]}</span></td>
    <td><span class="badge ${prioBadge[t.prioritas]}">${prioLabel[t.prioritas]}</span></td>
    <td style="color:var(--text-mute); font-weight:600;">${esc(t.dibuat)}</td>
    ${withActions ? `<td><div class="row-actions">
      <button class="icon-action ia-edit" data-edit-ticket="${t.id}"><svg class="icon" style="width:14px;height:14px;" data-lucide="pencil"></svg></button>
      <button class="icon-action ia-del" data-del-ticket="${t.id}"><svg class="icon" style="width:14px;height:14px;" data-lucide="trash-2"></svg></button>
    </div></td>` : ''}
  </tr>`;
    }
    function renderDashboard() {
      document.getElementById('dash-recent-body').innerHTML = tickets.slice(0, 5).map(t => ticketRow(t, false)).join('') || `<tr class="empty-row"><td colspan="6">Belum ada tiket</td></tr>`;
      const waiting = tickets.filter(t => t.status === 'menunggu').slice(0, 4);
      document.getElementById('dash-waiting-list').innerHTML = waiting.length ? waiting.map((t, i) => `
    ${i > 0 ? '<div class="divider"></div>' : ''}
    <div>
      <div style="display:flex; align-items:center; gap:8px; font-weight:800; font-size:13px;">${esc(t.id)} <span class="badge b-menunggu" style="padding:2px 8px; font-size:10px;">Menunggu</span></div>
      <div style="font-size:12.5px; color:var(--text-soft); margin-top:2px;">${esc(t.judul)}</div>
      <div class="cell-sub">${esc(t.kontak)}</div>
    </div>`).join('') : `<div style="color:var(--text-mute); font-size:13px; padding:8px 0;">Tidak ada tiket menunggu.</div>`;

      const counts = { baru: 0, menunggu: 0, proses: 0, selesai: 0 };
      tickets.forEach(t => counts[t.status]++);
      const total = tickets.length || 1;
      const legendColors = { baru: 'var(--violet)', menunggu: '#B7791F', proses: 'var(--amber)', selesai: 'var(--teal)' };
      document.getElementById('status-legend').innerHTML = Object.keys(counts).map(k => `
    <div class="legend-row"><span class="legend-left"><span class="legend-sw" style="background:${legendColors[k]};"></span>${statusLabel[k]}</span><span class="legend-val">${counts[k]} (${Math.round(counts[k] / total * 100)}%)</span></div>
  `).join('');

      document.getElementById('kpi-menunggu').dataset.target = counts.menunggu;
      document.getElementById('kpi-selesai').dataset.target = counts.selesai;
      document.getElementById('kpi-terbuka').dataset.target = counts.baru;
      document.getElementById('kpi-total').dataset.target = tickets.length;
      lucide.createIcons();
    }
    function renderTicketList() {
      const search = (document.getElementById('ticket-search').value || '').toLowerCase();
      const fStatus = document.getElementById('filter-status').value;
      const fPrio = document.getElementById('filter-prioritas').value;
      const fTipe = document.getElementById('filter-tipe').value;
      const filtered = tickets.filter(t => {
        if (search && !(t.judul.toLowerCase().includes(search) || t.id.toLowerCase().includes(search) || t.kontak.toLowerCase().includes(search))) return false;
        if (fStatus && t.status !== fStatus) return false;
        if (fPrio && t.prioritas !== fPrio) return false;
        if (fTipe && t.tipe !== fTipe) return false;
        return true;
      });
      document.getElementById('ticket-list-body').innerHTML = filtered.length ? filtered.map(t => ticketRow(t, true)).join('')
        : `<tr class="empty-row"><td colspan="7">Tidak ada tiket yang cocok dengan pencarian/filter.</td></tr>`;
      document.getElementById('ticket-count-label').textContent = `Menampilkan ${filtered.length} dari ${tickets.length} tiket`;
      lucide.createIcons();
    }

    function renderTicketDetail(id) {
      const t = tickets.find(x => x.id === id);
      if (!t) return;
      currentTicketId = id;
      document.getElementById('detail-id').textContent = t.id;
      document.getElementById('detail-status-badge').className = 'badge ' + statusBadge[t.status];
      document.getElementById('detail-status-badge').innerHTML = `<span class="dot"></span>${statusLabel[t.status]}`;
      document.getElementById('detail-keadaan').textContent = statusLabel[t.status];
      document.getElementById('detail-prioritas-badge').className = 'badge ' + prioBadge[t.prioritas];
      document.getElementById('detail-prioritas-badge').textContent = prioLabel[t.prioritas];
      document.getElementById('detail-sumber').textContent = t.sumber;
      document.getElementById('detail-tipe').textContent = t.tipe;
      document.getElementById('detail-agen').textContent = t.agen;
      document.getElementById('detail-kontak').textContent = t.kontak;
      document.getElementById('detail-kontak').classList.add('profile-link');
      document.getElementById('detail-kontak').setAttribute('data-profile-contact', t.kontak);
      document.getElementById('detail-kontak').style.cursor='pointer';
      document.getElementById('detail-kontak-initial').textContent = initials(t.kontak);
      document.getElementById('detail-meta').textContent = `oleh ${t.kontak} pada ${t.dibuat} via ${t.sumber}`;
      document.getElementById('detail-desc').textContent = t.deskripsi;
      document.getElementById('info-id').textContent = t.id;
      document.getElementById('info-dibuat').textContent = t.dibuat;
      document.getElementById('info-updated').textContent = t.updated;
      document.getElementById('info-status-badge').className = 'badge ' + statusBadge[t.status];
      document.getElementById('info-status-badge').textContent = statusLabel[t.status];
      document.getElementById('info-prioritas-badge').className = 'badge ' + prioBadge[t.prioritas];
      document.getElementById('info-prioritas-badge').textContent = prioLabel[t.prioritas];
      document.getElementById('info-tipe').textContent = t.tipe;
      document.getElementById('info-telepon').textContent = t.telepon;

      document.getElementById('paper-id').textContent = t.id;
      document.getElementById('paper-keadaan').textContent = statusLabel[t.status];
      document.getElementById('paper-prioritas').textContent = prioLabel[t.prioritas];
      document.getElementById('paper-sumber').textContent = t.sumber;
      document.getElementById('paper-tipe').textContent = t.tipe;
      document.getElementById('paper-agen').textContent = t.agen;
      document.getElementById('paper-meta').textContent = `oleh ${t.kontak} pada ${t.dibuat} via ${t.sumber}`;
      document.getElementById('paper-judul-label').textContent = t.judul;
      document.getElementById('paper-desc').textContent = t.deskripsi;

      const printNow = new Date();
      const printDate = `${printNow.getMonth() + 1}/${printNow.getDate()}/${String(printNow.getFullYear()).slice(-2)}, ${printNow.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}`;
      document.getElementById('paper-print-date').textContent = printDate;
      document.getElementById('paper-print-url').textContent = `m27.helpdesk.com/helpdesk/tickets/${String(t.id).replace('#','')}/print`;

      const commentMeta = document.getElementById('paper-comment-meta');
      const commentBody = document.getElementById('paper-comment-body');
      if (commentMeta && commentBody) {
        commentMeta.textContent = `oleh ${currentUser?.nama || t.agen} pada ${t.updated} as Email keluar`;
        commentBody.innerHTML = `Hi ${esc(t.kontak)},<br><br>Setelah dilakukan pemeriksaan oleh bagian teknologi informasi, laporan tiket <strong>${esc(t.id)}</strong> mengenai <strong>${esc(t.judul.toLowerCase())}</strong> telah ditindaklanjuti. Demikian laporan yang dapat kami sampaikan, atas perhatiannya kami ucapkan terima kasih.`;
      }

      const commentsWrap = document.getElementById('detail-comments');
      if (commentsWrap) {
        const replies = t.replies || [];
        commentsWrap.innerHTML = replies.length ? replies.map(r => `
      <div class="comment">
        <div class="avatar-sm" style="width:30px;height:30px;font-size:12px;">${esc(initials(r.nama))}</div>
        <div class="comment-body"><div class="comment-name">${esc(r.nama)}<span class="comment-time">${esc(r.waktu)} · ${esc(r.channel)}</span></div>${esc(r.body)}</div>
      </div>`).join('') : `<p style="color:var(--text-mute);">Belum ada balasan untuk tiket ini.</p>`;
      }
    }

    /* ============ RENDER: DIVISI ============ */
    function renderDivisi() {
      document.getElementById('divisi-body').innerHTML = divisiList.length ? divisiList.map(d => `<tr>
      <td class="cell-strong">${esc(d.nama)}</td>
      <td>${d.jumlahKaryawan ?? 0}</td>
    </tr>`).join('') : `<tr class="empty-row"><td colspan="2">Belum ada data divisi dari database induk.</td></tr>`;
      lucide.createIcons();
      refreshDivisiSelects();
    }

    /* ============ RENDER: KARYAWAN ============ */
    function refreshDivisiSelects() {
      const opts = divisiList.map(d => `<option value="${d.id}">${esc(d.nama)}</option>`).join('');
      const filterSel = document.getElementById('karyawan-filter-divisi');
      const curVal = filterSel.value;
      filterSel.innerHTML = `<option value="">Semua Divisi</option>` + opts;
      filterSel.value = curVal;
    }
    function renderKaryawan() {
      const search = (document.getElementById('karyawan-search').value || '').toLowerCase();
      const fDivisi = document.getElementById('karyawan-filter-divisi').value;
      const filtered = karyawanList.filter(k => {
        if (search && !(k.nama.toLowerCase().includes(search) || (k.email||'').toLowerCase().includes(search))) return false;
        if (fDivisi && String(k.divisiId) !== String(fDivisi)) return false;
        return true;
      });
      document.getElementById('karyawan-body').innerHTML = filtered.length ? filtered.map(k => `
    <tr>
      <td><div class="avatar-row"><div class="avatar-sm">${initials(k.nama)}</div><span class="cell-strong">${esc(k.nama)}</span></div></td>
      <td style="color:var(--text-soft);">${esc(k.email || '-')}</td>
      <td><span class="badge b-baru">${esc(k.divisiNama || divisiName(k.divisiId))}</span></td>
      <td style="color:var(--text-soft);">${esc(k.jabatan || '-')}</td>
      <td style="color:var(--text-soft);">${esc(k.tlp || '-')}</td>
    </tr>`).join('') : `<tr class="empty-row"><td colspan="5">Tidak ada karyawan yang cocok.</td></tr>`;
      lucide.createIcons();
    }

    /* ============ RENDER: ROLES ============ */
    function renderRoles() {
      rolesList.forEach(r => r.jml = usersList.filter(u => u.roleId === r.id).length);
      const body = document.getElementById('roles-body');
      if (!body) return;
      body.innerHTML = rolesList.length ? rolesList.map(r => `
    <tr>
      <td><div class="cell-strong">${esc(r.nama)}</div><div class="role-perm-summary">${new Set((r.permissions||[]).map(p=>p.split('.')[0])).size} resource diizinkan</div></td>
      <td>${r.jml} pengguna</td>
      <td><div class="row-actions">
        <button class="icon-action" data-perm-role="${r.id}" title="Atur hak akses"><svg class="icon" style="width:14px;height:14px;" data-lucide="key-round"></svg></button>
        <button class="icon-action ia-edit" data-edit-role="${r.id}"><svg class="icon" style="width:14px;height:14px;" data-lucide="pencil"></svg></button>
        <button class="icon-action ia-del" data-del-role="${r.id}"><svg class="icon" style="width:14px;height:14px;" data-lucide="trash-2"></svg></button>
      </div></td>
    </tr>`).join('') : `<tr class="empty-row"><td colspan="3">Belum ada role.</td></tr>`;
      lucide.createIcons();
    }

    function renderUsers() {
      const body = document.getElementById('users-body'); if (!body) return;
      body.innerHTML = usersList.map(u => {
        const role = rolesList.find(r=>r.id===u.roleId);
        return `<tr><td><div class="avatar-row"><div class="avatar-sm">${initials(u.nama)}</div><div><div class="cell-strong">${esc(u.nama)}</div><div class="role-perm-summary">${esc(u.email)}</div></div></div></td><td>${esc(role?.nama||'—')}</td><td>${esc(divisiName(u.divisiId))}</td><td><span class="badge ${u.active?'b-selesai':'b-rendah'}">${u.active?'Aktif':'Nonaktif'}</span></td><td><div class="row-actions"><button class="icon-action ia-edit" data-edit-user="${u.id}"><svg class="icon" style="width:14px;height:14px;" data-lucide="pencil"></svg></button><button class="icon-action ia-del" data-del-user="${u.id}"><svg class="icon" style="width:14px;height:14px;" data-lucide="trash-2"></svg></button></div></td></tr>`;
      }).join('');
      lucide.createIcons();
    }

    /* ============ SETTING GRID ============ */
    const settingItems = [
      { key:'company', icon:'building-2', title:'Profil Perusahaan', sub:'Kelola informasi perusahaan Anda' },
      { key:'email', icon:'mail-check', title:'Pengaturan Email', sub:'Konfigurasi email notifikasi' },
      { key:'categories', icon:'tags', title:'Kategori Tiket', sub:'Kelola kategori dan prioritas tiket' },
      { key:'statuses', icon:'radio', title:'Status Tiket', sub:'Kelola status dan alur tiket' },
      { key:'backup', icon:'database-backup', title:'Backup & Restore', sub:'Kelola backup data sistem' },
      { key:'general', icon:'sliders-horizontal', title:'Umum', sub:'Pengaturan umum sistem' },
    ];
    document.getElementById('setting-grid').innerHTML = settingItems.map(s => `
  <button class="setting-card" type="button" data-setting="${s.key}">
    <div class="setting-icon"><svg class="icon" data-lucide="${s.icon}"></svg></div>
    <div><div class="setting-text-title">${s.title}</div><div class="setting-text-sub">${s.sub}</div></div>
    <svg class="icon setting-chev" data-lucide="chevron-right"></svg>
  </button>`).join('');

    lucide.createIcons();

    /* ============ BOOTSTRAP DARI BACKEND ============
       Dipanggil sekali setelah login sukses, atau langsung saat boot kalau
       sesi Laravel masih aktif (window.__HELPDESK_BOOT.authenticated).
       Menggantikan seluruh data hardcode/localStorage versi front-end lama.
    ============ */
    async function loadBootstrapData() {
      const data = await apiFetch('/api/bootstrap');
      currentUser = data.user;
      tickets = data.tickets || [];
      divisiList = data.divisi || [];
      karyawanList = data.karyawan || [];
      rolesList = data.roles || [];
      usersList = data.users || [];
      if (data.settings) settingsState = data.settings;
      notifications.length = 0;
      (data.notifications || []).forEach(n => notifications.push(n));

      renderDashboard(); renderTicketList(); renderDivisi(); renderKaryawan(); renderRoles(); renderUsers();
      renderNotificationDot();
      refreshIdentity();
      lucide.createIcons();
      return data;
    }

    function currentRole() { return { nama: currentUser?.roleNama, permissions: currentUser?.permissions || [] }; }
    const pageResource = {dashboard:'dashboard','tiket-list':'tiket','tiket-detail':'tiket','cetak-tiket':'tiket',divisi:'divisi',karyawan:'karyawan',analitik:'analitik',users:'users',setting:'setting',roles:'roles'};
    function can(resource, action='view') {
      const perms = currentUser?.permissions || [];
      return perms.includes(`${resource}.${action}`);
    }
    function canAccess(page) { return can(pageResource[page] || page, 'view'); }
    function requirePermission(resource, action='view', message='Anda tidak memiliki hak akses untuk tindakan ini.') {
      if (can(resource, action)) return true;
      showToast(message, 'shield-alert');
      return false;
    }
    function applyPermissions() {
      document.querySelectorAll('.nav-item[data-page]').forEach(n => { n.style.display = canAccess(n.dataset.page) ? '' : 'none'; });
      const sub = document.getElementById('kontak-sub');
      if (sub) sub.style.display = (canAccess('divisi') || canAccess('karyawan')) ? '' : 'none';
      document.querySelectorAll('[data-action-permission]').forEach(el => {
        const [resource, action] = el.dataset.actionPermission.split('.');
        el.style.display = can(resource, action) ? '' : 'none';
      });
    }
    function refreshIdentity() {
      if (!currentUser) return;
      const avatarText = initials(currentUser.nama);
      document.querySelectorAll('.sb-foot-name,.topbar-user-name').forEach(el => el.textContent = currentUser.nama);
      document.querySelectorAll('.sb-foot-role,.topbar-user-role').forEach(el => el.textContent = currentUser.roleNama || 'User');
      document.querySelectorAll('#sidebar .avatar, .topbar-user .avatar').forEach(el => el.textContent = avatarText);
      const welcome = document.querySelector('#page-dashboard h1');
      if (welcome) welcome.innerHTML = `Selamat datang kembali, ${esc(currentUser.nama)} 👋`;
      applyPermissions();
    }
    async function loginUser(email, password) {
      try {
        const res = await apiFetch('/login', { method: 'POST', body: { email, password } });
        await loadBootstrapData();
        return { ok: true, user: res.user };
      } catch (err) {
        return { ok: false, message: err.message };
      }
    }
    function logout() {
      apiFetch('/logout', { method: 'POST' }).catch(() => {});
      currentUser = null;
      tickets = []; divisiList = []; karyawanList = []; rolesList = []; usersList = [];
      notifications.length = 0;
      document.getElementById('app')?.classList.remove('show');
      const login = document.getElementById('login-screen');
      login?.classList.remove('hide','success');
      document.body.classList.remove('login-success');
      const form = document.getElementById('login-form'); form?.reset();
      document.getElementById('email').value = 'superadmin@mitratani27.co.id';
      document.getElementById('password').value = 'password';
      document.getElementById('login-btn').disabled = false;
      document.getElementById('login-btn-text').textContent = 'Masuk';
      document.getElementById('login-spinner').style.display = 'none';
      document.getElementById('login-error').textContent = '';
      showToast('Anda telah logout', 'log-out');
    }
    function showNotificationPanel() {
      let panel = document.getElementById('notification-panel');
      if (!panel) return;
      panel.classList.toggle('show');
      if (panel.classList.contains('show')) {
        panel.innerHTML = `<div class="notif-head"><strong>Notifikasi</strong><button id="mark-notif-read" class="link-btn">Tandai dibaca</button></div><div class="notif-list">${notifications.map(n=>`<div class="notif-item ${n.unread?'unread':''}" data-notif="${n.id}"><div class="notif-icon"><svg class="icon" data-lucide="${n.icon}"></svg></div><div><strong>${esc(n.title)}</strong><p>${esc(n.text)}</p><small>${esc(n.time)}</small></div></div>`).join('')}</div>`;
        lucide.createIcons();
        document.getElementById('mark-notif-read')?.addEventListener('click', () => {
          notifications.forEach(n => n.unread = false);
          renderNotificationDot(); showNotificationPanel();
          showToast('Semua notifikasi ditandai sudah dibaca', 'check');
          apiFetch('/api/notifications/read-all', { method: 'POST' }).catch(() => {});
        });
      }
    }
    function renderNotificationDot() { const dot=document.querySelector('.notif-dot'); if(dot) dot.style.display=notifications.some(n=>n.unread)?'block':'none'; }
    window.__helpdeskLogin = loginUser;
    window.__helpdeskLogout = logout;
    window.__helpdeskRefreshIdentity = refreshIdentity;
    window.__helpdeskToast = (msg, icon='check') => showToast(msg, icon);

    /* ============ NAVIGATION ============ */
    const titles = {
      'dashboard': 'Dashboard', 'tiket-list': 'Tiket', 'tiket-detail': 'Detail Tiket', 'cetak-tiket': 'Cetak Tiket',
      'divisi': 'Divisi', 'karyawan': 'Karyawan', 'analitik': 'Analitik', 'users': 'Akun User', 'setting': 'Setting', 'roles': 'Roles'
    };
    function showPage(id) {
      if (currentUser && !canAccess(id) && id !== 'tiket-detail' && id !== 'cetak-tiket') { showToast('Anda tidak memiliki akses ke menu ini', 'shield-alert'); return; }
      document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
      const target = document.getElementById('page-' + id);
      if (target) target.classList.add('active');
      document.getElementById('topbar-title').textContent = titles[id] || 'Dashboard';
      document.getElementById('content').scrollTo({ top: 0, behavior: 'smooth' });
      document.querySelectorAll('.nav-item[data-page]').forEach(n => n.classList.toggle('active', n.dataset.page === id));
      if (id === 'dashboard') { animateCounts(document.getElementById('page-dashboard')); renderCharts(); }
      if (id === 'analitik') { renderAnalyticsCharts(); animateCounts(document.getElementById('page-analitik')); }
      closeMobileSidebar();
    }
    window.__helpdeskShowPage = showPage;
    document.body.addEventListener('click', (e) => {
      const navEl = e.target.closest('[data-page]');
      if (navEl) { e.preventDefault(); showPage(navEl.dataset.page); return; }

      const settingBtn = e.target.closest('[data-setting]');
      if (settingBtn) { openSettingForm(settingBtn.dataset.setting); return; }
      const profileBtn = e.target.closest('[data-profile-contact]');
      if (profileBtn) { openContactProfile(profileBtn.dataset.profileContact); return; }
      const permBtn = e.target.closest('[data-perm-role]');
      if (permBtn) { if(requirePermission('roles','permissions','Anda tidak memiliki hak untuk mengatur permission role.')) openRolePermissions(Number(permBtn.dataset.permRole)); return; }
      const editU = e.target.closest('[data-edit-user]');
      if (editU) { if(requirePermission('users','edit','Anda tidak memiliki hak untuk mengedit akun.')) openUserForm(Number(editU.dataset.editUser)); return; }
      const delU = e.target.closest('[data-del-user]');
      if (delU) { if(requirePermission('users','delete','Anda tidak memiliki hak untuk menghapus akun.')) confirmDeleteUser(Number(delU.dataset.delUser)); return; }

      const row = e.target.closest('[data-open]');
      if (row && !e.target.closest('[data-edit-ticket],[data-del-ticket]')) { showPage('tiket-detail'); renderTicketDetail(row.dataset.open); return; }

      const editT = e.target.closest('[data-edit-ticket]');
      if (editT) { e.stopPropagation(); openTicketForm(editT.dataset.editTicket); return; }
      const delT = e.target.closest('[data-del-ticket]');
      if (delT) { e.stopPropagation(); confirmDeleteTicket(delT.dataset.delTicket); return; }

      const editR = e.target.closest('[data-edit-role]');
      if (editR) { if(requirePermission('roles','edit','Anda tidak memiliki hak untuk mengedit role.')) openRoleForm(Number(editR.dataset.editRole)); return; }
      const delR = e.target.closest('[data-del-role]');
      if (delR) { if(requirePermission('roles','delete','Anda tidak memiliki hak untuk menghapus role.')) confirmDeleteRole(Number(delR.dataset.delRole)); return; }
    });

    /* Kontak submenu */
    const kontakToggle = document.getElementById('kontak-toggle');
    const kontakSub = document.getElementById('kontak-sub');
    kontakToggle.addEventListener('click', () => {
      kontakToggle.classList.toggle('open');
      kontakSub.classList.toggle('open');
    });

    /* Sidebar mobile drawer */
    const sidebarEl = document.getElementById('sidebar');
    const overlayEl = document.getElementById('sidebar-overlay');
    function openMobileSidebar() { sidebarEl.classList.add('mobile-open'); overlayEl.classList.add('show'); }
    function closeMobileSidebar() { sidebarEl.classList.remove('mobile-open'); overlayEl.classList.remove('show'); }
    document.getElementById('sb-close-btn').addEventListener('click', closeMobileSidebar);
    overlayEl.addEventListener('click', closeMobileSidebar);
    document.getElementById('notification-btn')?.addEventListener('click', (e)=>{e.stopPropagation();showNotificationPanel();});
    document.getElementById('logout-btn')?.addEventListener('click', logout);
    document.getElementById('topbar-user')?.addEventListener('click', ()=>openCurrentUserProfile());
    document.addEventListener('click', e=>{ if(!e.target.closest('#notification-btn,#notification-panel')) document.getElementById('notification-panel')?.classList.remove('show'); });
    renderNotificationDot();

    /* Ticket filters */
    ['ticket-search', 'filter-status', 'filter-prioritas', 'filter-tipe'].forEach(id => {
      document.getElementById(id).addEventListener('input', renderTicketList);
      document.getElementById(id).addEventListener('change', renderTicketList);
    });
    document.getElementById('karyawan-search').addEventListener('input', renderKaryawan);
    document.getElementById('karyawan-filter-divisi').addEventListener('change', renderKaryawan);

    /* Buttons -> pages / toasts */
    document.getElementById('cetak-tiket-btn').addEventListener('click', () => { if(requirePermission('tiket','print','Anda tidak memiliki hak untuk mencetak tiket.')) showPage('cetak-tiket'); });
    document.getElementById('download-btn').addEventListener('click', () => window.print());
    document.getElementById('print-cancel-btn')?.addEventListener('click', () => showPage('tiket-detail'));
    document.getElementById('send-reply-btn').addEventListener('click', async () => { if(!requirePermission('tiket','reply','Anda tidak memiliki hak untuk membalas tiket.')) return;
      const box = document.getElementById('reply-text');
      if (!box.value.trim()) { showToast('Tulis balasan terlebih dahulu', 'triangle-alert'); return; }
      try {
        const res = await apiFetch(`/api/tickets/${ticketNumId(currentTicketId)}/reply`, { method: 'POST', body: { body: box.value.trim() } });
        const idx = tickets.findIndex(x => x.id === currentTicketId);
        if (idx !== -1) tickets[idx] = res.ticket;
        renderTicketDetail(currentTicketId);
        box.value = '';
        showToast('Balasan berhasil dikirim', 'send');
      } catch (err) {
        showToast(err.message || 'Gagal mengirim balasan', 'triangle-alert');
      }
    });
    document.getElementById('tutup-tiket-btn').addEventListener('click', async () => { if(!requirePermission('tiket','close','Anda tidak memiliki hak untuk menutup tiket.')) return;
      try {
        const res = await apiFetch(`/api/tickets/${ticketNumId(currentTicketId)}/close`, { method: 'PATCH' });
        const idx = tickets.findIndex(x => x.id === currentTicketId);
        if (idx !== -1) tickets[idx] = res.ticket;
        renderTicketDetail(currentTicketId); renderTicketList(); renderDashboard();
        showToast(`Tiket ${currentTicketId} telah ditutup`, 'check');
      } catch (err) {
        showToast(err.message || 'Gagal menutup tiket', 'triangle-alert');
      }
    });
    document.getElementById('hapus-tiket-btn').addEventListener('click', () => { if(requirePermission('tiket','delete','Anda tidak memiliki hak untuk menghapus tiket.')) confirmDeleteTicket(currentTicketId, true); });
    document.getElementById('edit-tiket-btn').addEventListener('click', () => { if(requirePermission('tiket','edit','Anda tidak memiliki hak untuk mengedit tiket.')) openTicketForm(currentTicketId); });
    document.getElementById('new-ticket-btn').addEventListener('click', () => { if(requirePermission('tiket','create','Anda tidak memiliki hak untuk membuat tiket.')) openTicketForm(null); });
    document.getElementById('new-role-btn').addEventListener('click', () => { if(requirePermission('roles','create','Anda tidak memiliki hak untuk menambah role.')) openRoleForm(null); });
    document.getElementById('new-user-btn').addEventListener('click', () => { if(requirePermission('users','create','Anda tidak memiliki hak untuk menambah akun.')) openUserForm(null); });

    function showToast(text, icon) {
      const t = document.getElementById('toast');
      document.getElementById('toast-text').textContent = text;
      t.querySelector('.toast-ic').innerHTML = `<svg class="icon" style="width:14px;height:14px;color:#fff;" data-lucide="${icon || 'check'}"></svg>`;
      lucide.createIcons();
      t.classList.add('show');
      clearTimeout(window._toastTimer);
      window._toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
    }
    window.__helpdeskToast = showToast;

    /* ============ MODAL ENGINE ============ */
    const modalOverlay = document.getElementById('modal-overlay');
    const modalBox = document.getElementById('modal-box');
    function openModal(title, bodyHTML, footHTML, small) {
      document.getElementById('modal-title').textContent = title;
      document.getElementById('modal-body').innerHTML = bodyHTML;
      document.getElementById('modal-foot').innerHTML = footHTML;
      modalBox.classList.toggle('modal-confirm', !!small);
      modalOverlay.classList.add('show');
      lucide.createIcons();
    }
    function closeModal() { modalOverlay.classList.remove('show'); }
    document.getElementById('modal-close-btn').addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', (e) => { if (e.target === modalOverlay) closeModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

    /* ============ TICKET FORM ============ */
    function openTicketForm(id) {
      const editing = !!id;
      const t = editing ? tickets.find(x => x.id === id) : null;
      const body = `
    <div class="form-field"><label>Judul Tiket</label><input class="form-input" id="f-judul" value="${t ? esc(t.judul) : ''}" placeholder="Contoh: Printer tidak bisa print"></div>
    <div class="form-field"><label>Deskripsi</label><textarea class="form-textarea" id="f-desc" placeholder="Jelaskan detail kendala...">${t ? esc(t.deskripsi) : ''}</textarea></div>
    <div class="form-row-2">
      <div class="form-field"><label>Nama Kontak</label><input class="form-input" id="f-kontak" value="${t ? esc(t.kontak) : ''}" placeholder="Nama pelapor"></div>
      <div class="form-field"><label>No. Telepon</label><input class="form-input" id="f-telepon" value="${t ? esc(t.telepon) : ''}" placeholder="08xx-xxxx-xxxx"></div>
    </div>
    <div class="form-row-2">
      <div class="form-field"><label>Prioritas</label><div class="select-wrap-form"><select class="form-select" id="f-prioritas">
        ${['rendah', 'sedang', 'tinggi'].map(p => `<option value="${p}" ${t && t.prioritas === p ? 'selected' : ''}>${prioLabel[p]}</option>`).join('')}
      </select><svg class="icon" data-lucide="chevron-down"></svg></div></div>
      <div class="form-field"><label>Status</label><div class="select-wrap-form"><select class="form-select" id="f-status">
        ${['baru', 'menunggu', 'proses', 'selesai'].map(s => `<option value="${s}" ${t && t.status === s ? 'selected' : ''}>${statusLabel[s]}</option>`).join('')}
      </select><svg class="icon" data-lucide="chevron-down"></svg></div></div>
    </div>
    <div class="form-row-2">
      <div class="form-field"><label>Tipe</label><div class="select-wrap-form"><select class="form-select" id="f-tipe">
        ${['Incident', 'Request', 'Problem', 'Feature Request'].map(v => `<option value="${v}" ${t && t.tipe === v ? 'selected' : ''}>${v}</option>`).join('')}
      </select><svg class="icon" data-lucide="chevron-down"></svg></div></div>
      <div class="form-field"><label>Sumber</label><div class="select-wrap-form"><select class="form-select" id="f-sumber">
        ${['Telepon', 'Email', 'Chat', 'Web'].map(v => `<option value="${v}" ${t && t.sumber === v ? 'selected' : ''}>${v}</option>`).join('')}
      </select><svg class="icon" data-lucide="chevron-down"></svg></div></div>
    </div>
    <div class="form-field"><label>Agen Penanggung Jawab</label><div class="select-wrap-form"><select class="form-select" id="f-agen">
      <option value="">Belum ditugaskan</option>
      ${usersList.map(u => `<option value="${u.id}" ${t && t.agen === u.nama ? 'selected' : ''}>${esc(u.nama)}</option>`).join('')}
    </select><svg class="icon" data-lucide="chevron-down"></svg></div></div>
  `;
      const foot = `
    <button class="btn btn-ghost" id="modal-cancel">Batal</button>
    <button class="btn btn-blue" id="modal-save"><svg class="icon" style="width:14px;height:14px;" data-lucide="check"></svg>${editing ? 'Simpan Perubahan' : 'Buat Tiket'}</button>
  `;
      openModal(editing ? `Edit Tiket ${t.id}` : 'Buat Tiket Baru', body, foot);
      document.getElementById('modal-cancel').addEventListener('click', closeModal);
      document.getElementById('modal-save').addEventListener('click', async () => {
        const judul = document.getElementById('f-judul').value.trim();
        if (!judul) { showToast('Judul tiket wajib diisi', 'triangle-alert'); return; }
        const data = {
          judul,
          deskripsi: document.getElementById('f-desc').value.trim() || '-',
          kontak_nama: document.getElementById('f-kontak').value.trim() || 'Tanpa nama',
          telepon: document.getElementById('f-telepon').value.trim() || '-',
          prioritas: document.getElementById('f-prioritas').value,
          status: document.getElementById('f-status').value,
          tipe: document.getElementById('f-tipe').value,
          sumber: document.getElementById('f-sumber').value,
          agen_id: document.getElementById('f-agen').value ? Number(document.getElementById('f-agen').value) : null,
        };
        try {
          if (editing) {
            const res = await apiFetch(`/api/tickets/${ticketNumId(id)}`, { method: 'PUT', body: data });
            const idx = tickets.findIndex(x => x.id === id);
            if (idx !== -1) tickets[idx] = res.ticket;
            showToast(`Tiket ${res.ticket.id} berhasil diperbarui`, 'check');
            if (currentTicketId === id) renderTicketDetail(res.ticket.id);
          } else {
            const res = await apiFetch('/api/tickets', { method: 'POST', body: data });
            tickets.unshift(res.ticket);
            showToast(`Tiket ${res.ticket.id} berhasil dibuat`, 'sparkles');
          }
          renderTicketList(); renderDashboard();
          closeModal();
          setTimeout(() => { const r = document.querySelector(`[data-open="${editing ? id : tickets[0].id}"]`); if (r) r.classList.add('row-new'); }, 60);
        } catch (err) {
          showToast(err.message || 'Gagal menyimpan tiket', 'triangle-alert');
        }
      });
    }
    function confirmDeleteTicket(id, fromDetail) {
      const t = tickets.find(x => x.id === id);
      if (!t) return;
      openModal('Hapus Tiket', `
    <div class="confirm-icon"><svg class="icon" style="width:24px;height:24px;" data-lucide="trash-2"></svg></div>
    <p class="confirm-text">Yakin ingin menghapus tiket <b>${esc(t.id)} - ${esc(t.judul)}</b>? Tindakan ini tidak dapat dibatalkan.</p>
  `, `
    <button class="btn btn-ghost" id="modal-cancel">Batal</button>
    <button class="btn btn-danger" id="modal-confirm-del">Ya, Hapus</button>
  `, true);
      document.getElementById('modal-cancel').addEventListener('click', closeModal);
      document.getElementById('modal-confirm-del').addEventListener('click', async () => {
        try {
          await apiFetch(`/api/tickets/${ticketNumId(id)}`, { method: 'DELETE' });
          tickets = tickets.filter(x => x.id !== id);
          renderTicketList(); renderDashboard();
          closeModal();
          showToast(`Tiket ${id} berhasil dihapus`, 'trash-2');
          if (fromDetail) showPage('tiket-list');
        } catch (err) {
          showToast(err.message || 'Gagal menghapus tiket', 'triangle-alert');
        }
      });
    }

    /* Divisi & Karyawan sengaja tidak punya form create/edit/delete di sini —
       datanya read-only, ditarik langsung dari db_indukk (lihat
       DivisiController & KaryawanController). Kelola datanya di sistem HR. */

    function openRolePermissions(id) {
      const r = rolesList.find(x=>x.id===id); if(!r) return;
      const body = `<p style="color:var(--text-mute);margin-top:0">Tentukan hak akses detail untuk <b>${esc(r.nama)}</b>. Centang sesuai kebutuhan. Daftar di bawah mengikuti struktur menu sidebar (<b>MENU</b> &amp; <b>LAINNYA</b>); setiap menu memiliki aksi <b>view / create / edit / delete</b> dan aksi khusus bila tersedia.</p><div class="permission-builder">${renderPermissionBuilder(r.permissions||[], 'perm')}</div>`;
      openModal('Hak Akses Role', body, `<button class="btn btn-ghost" id="modal-cancel">Batal</button><button class="btn btn-blue" id="modal-save"><svg class="icon" data-lucide="check"></svg>Simpan Hak Akses</button>`);
      document.querySelectorAll('[data-perm-all]').forEach(btn=>btn.addEventListener('click',()=>{ const resource=btn.dataset.permAll; document.querySelectorAll(`[data-perm^="${resource}."]`).forEach(x=>x.checked=true); }));
      document.getElementById('modal-cancel').addEventListener('click',closeModal);
      document.getElementById('modal-save').addEventListener('click', async ()=>{
        const permissions=[...document.querySelectorAll('[data-perm]:checked')].map(x=>x.dataset.perm);
        try {
          const res = await apiFetch(`/api/roles/${r.id}/permissions`, { method:'PUT', body:{ permissions } });
          Object.assign(r, res.role);
          renderRoles();
          if (currentUser?.roleId===r.id) { await loadBootstrapData(); }
          closeModal();
          showToast('Hak akses detail berhasil diperbarui','shield-check');
        } catch (err) { showToast(err.message || 'Gagal menyimpan hak akses','triangle-alert'); }
      });
    }

    /* ============ ROLE FORM ============ */
    function openRoleForm(id) {
      const editing = id != null;
      const r = editing ? rolesList.find(x => x.id === id) : null;
      const existingPerms = normalizePermissions(r?.permissions || ['dashboard.view','tiket.view']);
      const body = `
    <div class="form-field"><label>Nama Role</label><input class="form-input" id="f-nama" value="${r ? esc(r.nama) : ''}" placeholder="Contoh: Supervisor"></div>
    <div class="permission-builder compact"><div class="permission-builder-title">Hak akses awal (mengikuti struktur menu sidebar)</div>${renderPermissionBuilder(existingPerms, 'newperm')}</div>
  `;
      const foot = `<button class="btn btn-ghost" id="modal-cancel">Batal</button><button class="btn btn-blue" id="modal-save"><svg class="icon" style="width:14px;height:14px;" data-lucide="check"></svg>${editing ? 'Simpan Perubahan' : 'Tambah Role'}</button>`;
      openModal(editing ? 'Edit Role' : 'Tambah Role', body, foot);
      document.querySelectorAll('[data-newperm-all]').forEach(btn=>btn.addEventListener('click',()=>{ const resource=btn.dataset.newpermAll; document.querySelectorAll(`[data-newperm^="${resource}."]`).forEach(x=>x.checked=true); }));
      document.getElementById('modal-cancel').addEventListener('click', closeModal);
      document.getElementById('modal-save').addEventListener('click', async () => {
        const nama = document.getElementById('f-nama').value.trim();
        if (!nama) { showToast('Nama role wajib diisi', 'triangle-alert'); return; }
        const permissions=[...document.querySelectorAll('[data-newperm]:checked')].map(x=>x.dataset.newperm);
        try {
          if (editing) {
            const res = await apiFetch(`/api/roles/${r.id}`, { method:'PUT', body:{ nama, permissions } });
            Object.assign(r, res.role);
            showToast('Role berhasil diperbarui', 'check');
          } else {
            const res = await apiFetch('/api/roles', { method:'POST', body:{ nama, permissions } });
            rolesList.push(res.role);
            showToast('Role berhasil ditambahkan', 'sparkles');
          }
          renderRoles();
          closeModal();
        } catch (err) { showToast(err.message || 'Gagal menyimpan role', 'triangle-alert'); }
      });
    }
    function confirmDeleteRole(id) {
      const r = rolesList.find(x => x.id === id);
      if (!r) return;
      openModal('Hapus Role', `
    <div class="confirm-icon"><svg class="icon" style="width:24px;height:24px;" data-lucide="trash-2"></svg></div>
    <p class="confirm-text">Yakin ingin menghapus role <b>${esc(r.nama)}</b>?</p>
  `, `<button class="btn btn-ghost" id="modal-cancel">Batal</button><button class="btn btn-danger" id="modal-confirm-del">Ya, Hapus</button>`, true);
      document.getElementById('modal-cancel').addEventListener('click', closeModal);
      document.getElementById('modal-confirm-del').addEventListener('click', async () => {
        try {
          await apiFetch(`/api/roles/${id}`, { method:'DELETE' });
          rolesList = rolesList.filter(x => x.id !== id);
          renderRoles();
          closeModal();
          showToast('Role berhasil dihapus', 'trash-2');
        } catch (err) { showToast(err.message || 'Gagal menghapus role (mungkin masih dipakai user)', 'triangle-alert'); }
      });
    }

    function openUserForm(id) {
      const editing=id!=null, u=editing?usersList.find(x=>x.id===id):null;
      const roleOpts=rolesList.map(r=>`<option value="${r.id}" ${u?.roleId===r.id?'selected':''}>${esc(r.nama)}</option>`).join('');
      const divOpts='<option value="">Semua / Tidak ditentukan</option>'+divisiList.map(d=>`<option value="${d.id}" ${u?.divisiId===d.id?'selected':''}>${esc(d.nama)}</option>`).join('');
      const body=`<div class="form-grid-2"><div class="form-field"><label>Nama Lengkap</label><input class="form-input" id="u-nama" value="${u?esc(u.nama):''}"></div><div class="form-field"><label>Email Login</label><input class="form-input" type="email" id="u-email" value="${u?esc(u.email):''}"></div><div class="form-field"><label>Password</label><input class="form-input" type="password" id="u-pass" placeholder="${editing?'Kosongkan jika tidak diubah':'Minimal 6 karakter'}" value=""></div><div class="form-field"><label>Role</label><select class="form-input" id="u-role">${roleOpts}</select></div><div class="form-field"><label>Divisi</label><select class="form-input" id="u-divisi">${divOpts}</select></div><div class="form-field"><label>Status</label><select class="form-input" id="u-active"><option value="1" ${u?.active!==false?'selected':''}>Aktif</option><option value="0" ${u?.active===false?'selected':''}>Nonaktif</option></select></div></div>`;
      openModal(editing?'Edit Akun User':'Tambah Akun User',body,`<button class="btn btn-ghost" id="modal-cancel">Batal</button><button class="btn btn-blue" id="modal-save">Simpan Akun</button>`);
      document.getElementById('modal-cancel').addEventListener('click',closeModal);
      document.getElementById('modal-save').addEventListener('click', async ()=>{
        const nama=document.getElementById('u-nama').value.trim(),email=document.getElementById('u-email').value.trim().toLowerCase(),pass=document.getElementById('u-pass').value,roleId=Number(document.getElementById('u-role').value),divisiId=Number(document.getElementById('u-divisi').value)||null,active=document.getElementById('u-active').value==='1';
        if(!nama||!email||(!editing&&!pass)){showToast('Nama, email dan password wajib diisi','triangle-alert');return;}
        const payload={ nama, email, roleId, divisiId, active };
        if (pass) payload.password = pass;
        try {
          if (editing) {
            const res = await apiFetch(`/api/users/${u.id}`, { method:'PUT', body: payload });
            Object.assign(u, res.user);
          } else {
            const res = await apiFetch('/api/users', { method:'POST', body: payload });
            usersList.push(res.user);
          }
          renderUsers(); renderRoles();
          closeModal();
          showToast(editing?'Akun berhasil diperbarui':'Akun berhasil dibuat','user-check');
        } catch (err) { showToast(err.message || 'Gagal menyimpan akun', 'triangle-alert'); }
      });
    }
    function confirmDeleteUser(id){
      const u=usersList.find(x=>x.id===id); if(!u)return;
      openModal('Hapus Akun',`<div class="confirm-icon"><svg class="icon" data-lucide="trash-2"></svg></div><p class="confirm-text">Hapus akun <b>${esc(u.nama)}</b>?</p>`,`<button class="btn btn-ghost" id="modal-cancel">Batal</button><button class="btn btn-danger" id="modal-confirm-del">Ya, Hapus</button>`,true);
      document.getElementById('modal-cancel').addEventListener('click',closeModal);
      document.getElementById('modal-confirm-del').addEventListener('click', async ()=>{
        try {
          await apiFetch(`/api/users/${id}`, { method:'DELETE' });
          usersList=usersList.filter(x=>x.id!==id);
          renderUsers(); renderRoles();
          closeModal();
          showToast('Akun berhasil dihapus','trash-2');
        } catch (err) { showToast(err.message || 'Gagal menghapus akun', 'triangle-alert'); }
      });
    }
    function openContactProfile(name){ const u=usersList.find(x=>x.nama===name)||karyawanList.find(x=>x.nama===name); const count=tickets.filter(t=>t.kontak===name).length; const resolved=tickets.filter(t=>t.kontak===name&&t.status==='selesai').length; const email=u?.email||`${name.toLowerCase().replace(/\s+/g,'.')}@mitratani27.co.id`; const div=u?.divisiId?(u.divisiNama||divisiName(u.divisiId)):'—'; openModal('Profil Pengaju',`<div class="profile-modal"><div class="profile-avatar">${initials(name)}</div><h3>${esc(name)}</h3><p>${esc(email)}</p><div class="profile-stats"><div><strong>${count}</strong><span>Total tiket</span></div><div><strong>${resolved}</strong><span>Tiket selesai</span></div><div><strong>${count?Math.round((resolved/count)*100):0}%</strong><span>Penyelesaian</span></div></div><div class="profile-meta"><span>Divisi</span><b>${esc(div)}</b></div><div class="profile-meta"><span>Telepon</span><b>${esc(u?.tlp||'—')}</b></div></div>`,`<button class="btn btn-blue" id="modal-cancel">Tutup</button>`); document.getElementById('modal-cancel').addEventListener('click',closeModal); }
    function openCurrentUserProfile(){ if(!currentUser)return; openContactProfile(currentUser.nama); }
    function openSettingForm(key){
      const forms={
        company:`<div class="form-grid-2"><div class="form-field"><label>Nama Perusahaan</label><input class="form-input" id="s-name" value="${esc(settingsState.company.name)}"></div><div class="form-field"><label>Email</label><input class="form-input" id="s-email" value="${esc(settingsState.company.email)}"></div><div class="form-field"><label>Telepon</label><input class="form-input" id="s-phone" value="${esc(settingsState.company.phone)}"></div><div class="form-field"><label>Alamat</label><input class="form-input" id="s-address" value="${esc(settingsState.company.address)}"></div></div>`,
        email:`<div class="form-grid-2"><div class="form-field"><label>SMTP Host</label><input class="form-input" id="s-host" value="${esc(settingsState.email.host)}"></div><div class="form-field"><label>Port</label><input class="form-input" id="s-port" value="${esc(settingsState.email.port)}"></div><div class="form-field"><label>Email Pengirim</label><input class="form-input" id="s-sender" value="${esc(settingsState.email.sender)}"></div><label class="switch-row"><input type="checkbox" id="s-enabled" ${settingsState.email.enabled?'checked':''}><span>Aktifkan notifikasi email</span></label></div>`,
        categories:`<div class="form-field"><label>Kategori Tiket</label><textarea class="form-textarea" id="s-list">${settingsState.categories.join('\n')}</textarea><small>Satu kategori per baris.</small></div>`,
        statuses:`<div class="form-field"><label>Status Tiket</label><textarea class="form-textarea" id="s-list">${settingsState.statuses.join('\n')}</textarea><small>Urutan dari status awal sampai selesai.</small></div>`,
        backup:`<div class="backup-panel"><div><strong>Backup terakhir</strong><span>${esc(settingsState.backup.last)}</span></div><div><strong>Frekuensi</strong><select class="form-input" id="s-frequency"><option ${settingsState.backup.frequency==='Harian'?'selected':''}>Harian</option><option ${settingsState.backup.frequency==='Mingguan'?'selected':''}>Mingguan</option><option ${settingsState.backup.frequency==='Manual'?'selected':''}>Manual</option></select></div><button class="btn btn-blue" id="do-backup">Buat Backup Sekarang</button></div>`,
        general:`<div class="form-grid-2"><div class="form-field"><label>Zona Waktu</label><select class="form-input" id="s-tz"><option ${settingsState.general.timezone==='Asia/Jakarta'?'selected':''}>Asia/Jakarta</option><option>Asia/Makassar</option><option>Asia/Jayapura</option></select></div><div class="form-field"><label>Bahasa</label><select class="form-input" id="s-lang"><option>Bahasa Indonesia</option><option>English</option></select></div><label class="switch-row"><input type="checkbox" id="s-refresh" ${settingsState.general.autoRefresh?'checked':''}><span>Refresh dashboard otomatis</span></label></div>`};
      openModal(settingItems.find(x=>x.key===key)?.title||'Pengaturan',forms[key],`<button class="btn btn-ghost" id="modal-cancel">Batal</button><button class="btn btn-blue" id="modal-save">Simpan Pengaturan</button>`);
      document.getElementById('modal-cancel').addEventListener('click',closeModal);
      document.getElementById('do-backup')?.addEventListener('click', async ()=>{
        try {
          const value = { ...settingsState.backup, last: nowStr() };
          await apiFetch('/api/settings/backup', { method:'PUT', body:{ value } });
          settingsState.backup = value;
          closeModal();
          showToast('Backup simulasi berhasil dibuat','database-backup');
        } catch (err) { showToast(err.message || 'Gagal membuat backup', 'triangle-alert'); }
      });
      document.getElementById('modal-save').addEventListener('click', async ()=>{
        let value;
        if(key==='company') value={name:document.getElementById('s-name').value.trim(),email:document.getElementById('s-email').value.trim(),phone:document.getElementById('s-phone').value.trim(),address:document.getElementById('s-address').value.trim()};
        if(key==='email') value={host:document.getElementById('s-host').value.trim(),port:document.getElementById('s-port').value.trim(),sender:document.getElementById('s-sender').value.trim(),enabled:document.getElementById('s-enabled').checked};
        if(key==='categories') value=document.getElementById('s-list').value.split('\n').map(x=>x.trim()).filter(Boolean);
        if(key==='statuses') value=document.getElementById('s-list').value.split('\n').map(x=>x.trim()).filter(Boolean);
        if(key==='general') value={timezone:document.getElementById('s-tz').value,language:document.getElementById('s-lang').value,autoRefresh:document.getElementById('s-refresh').checked};
        if (key==='backup') { closeModal(); return; }
        try {
          await apiFetch(`/api/settings/${key}`, { method:'PUT', body:{ value } });
          settingsState[key]=value;
          closeModal();
          showToast('Pengaturan berhasil disimpan','check');
        } catch (err) { showToast(err.message || 'Gagal menyimpan pengaturan', 'triangle-alert'); }
      });
    }

    /* ============ COUNT UP ============ */
    function animateCounts(scope) {
      scope.querySelectorAll('.count-up').forEach(el => {
        const target = parseInt(el.dataset.target, 10) || 0;
        let t0 = null; const dur = 800;
        function step(t) {
          if (!t0) t0 = t;
          const p = Math.min((t - t0) / dur, 1);
          const eased = 1 - Math.pow(1 - p, 3);
          el.textContent = Math.round(eased * target);
          if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
      });
    }

    /* ============ CHARTS ============ */
    if (!window.Chart) {
      console.warn('Chart.js tidak tersedia. Grafik dilewati, fitur lain tetap aktif.');
    } else {
      Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
      Chart.defaults.color = '#7B9188';
    }
    let chartMonthly, chartStatus, chartPeriode, chartTipe;

    function renderCharts() {
      if (!window.Chart) return;
      const year = new Date().getFullYear();
      const monthly = getTicketMonthData(year);
      const cm = document.getElementById('chartMonthly');
      if (chartMonthly) chartMonthly.destroy();
      const grad = cm.getContext('2d').createLinearGradient(0, 0, 0, 220);
      grad.addColorStop(0, 'rgba(46,72,229,.28)'); grad.addColorStop(1, 'rgba(46,72,229,0)');
      chartMonthly = new Chart(cm, {
        type: 'line',
        data: { labels: monthly.labels, datasets: [{ label: 'Tiket dibuat', data: monthly.created, borderColor: '#087F5B', backgroundColor: grad, fill: true, tension: .35, borderWidth: 2.5, pointRadius: 2.5, pointHoverRadius: 5, pointHoverBackgroundColor: '#087F5B', pointHoverBorderColor: '#fff', pointHoverBorderWidth: 2 }] },
        options: { responsive: true, maintainAspectRatio: false, animation: { duration: 700, easing: 'easeOutQuart' }, plugins: { legend: { display: false }, tooltip: { backgroundColor: '#10221B', padding: 10, cornerRadius: 8, titleFont: { weight: 700 }, bodyFont: { weight: 600 } } }, scales: { x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11, weight: 600 } } }, y: { beginAtZero: true, grid: { color: '#EAF3EF' }, border: { display: false }, ticks: { precision: 0, stepSize: 1, font: { size: 11, weight: 600 } } } } }
      });

      const cs = document.getElementById('chartStatus');
      if (chartStatus) chartStatus.destroy();
      const counts = { baru: 0, menunggu: 0, proses: 0, selesai: 0 };
      tickets.forEach(t => counts[t.status]++);
      chartStatus = new Chart(cs, {
        type: 'doughnut',
        data: { labels: ['Baru', 'Menunggu', 'Proses', 'Selesai'], datasets: [{ data: [counts.baru, counts.menunggu, counts.proses, counts.selesai], backgroundColor: ['#4E8F74', '#B7791F', '#C88917', '#12A36F'], borderWidth: 0, hoverOffset: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '72%', animation: { animateRotate: true, duration: 700 }, plugins: { legend: { display: false }, tooltip: { backgroundColor: '#10221B', padding: 10, cornerRadius: 8 } } }
      });
    }

    function getTicketMonthData(year = new Date().getFullYear()) {
      const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      const created = Array(12).fill(0);
      const selesai = Array(12).fill(0);
      const open = Array(12).fill(0);

      // Grafik "tidak selesai" adalah backlog per akhir bulan, bukan hanya
      // tiket baru pada bulan tersebut. Jadi tiket Januari yang masih terbuka
      // akan tetap dihitung pada Februari, Maret, dst. sampai benar-benar
      // selesai. Tiket dari tahun sebelumnya yang masih terbuka juga ikut
      // menjadi backlog pada Januari tahun berjalan.
      const monthEnd = (monthIndex) => new Date(year, monthIndex + 1, 0, 23, 59, 59, 999);

      tickets.forEach(t => {
        const createdDate = new Date(t.created_at || '');
        if (Number.isNaN(createdDate.getTime())) return;

        // "Total tiket dibuat" tetap hanya tiket yang dibuat pada bulan itu.
        if (createdDate.getFullYear() === year) {
          created[createdDate.getMonth()]++;
        }

        // Untuk tiket yang selesai, gunakan closed_at bila tersedia. Fallback
        // ke updated_at agar data lama tetap dapat dihitung tanpa migrasi.
        const completionRaw = t.closed_at || (t.status === 'selesai' ? t.updated_at : null);
        const completionDate = completionRaw ? new Date(completionRaw) : null;
        const hasCompletion = completionDate && !Number.isNaN(completionDate.getTime());

        for (let month = 0; month < 12; month++) {
          const end = monthEnd(month);

          // Belum dibuat sampai akhir bulan ini -> belum menjadi backlog.
          if (createdDate > end) continue;

          // Masih terbuka pada akhir bulan -> masuk backlog bulan ini.
          if (!hasCompletion || completionDate > end) {
            open[month]++;
          }
        }

        // Jumlah selesai tetap dihitung berdasarkan bulan tiket ditutup.
        if (hasCompletion && completionDate.getFullYear() === year) {
          selesai[completionDate.getMonth()]++;
        }
      });

      return { labels, created, selesai, open };
    }

    function renderAnalyticsCharts() {
      if (!window.Chart) return;
      const year = new Date().getFullYear();
      const monthly = getTicketMonthData(year);
      const cp = document.getElementById('chartPeriode');
      if (chartPeriode) chartPeriode.destroy();
      chartPeriode = new Chart(cp, {
        type: 'bar',
        data: {
          labels: monthly.labels,
          datasets: [
            { label: 'Total tiket tidak selesai', data: monthly.open, backgroundColor: '#2188E5', borderRadius: 4, barPercentage: .62, categoryPercentage: .72 },
            { label: 'Total tiket dibuat', data: monthly.created, backgroundColor: '#9BC2E6', borderRadius: 4, barPercentage: .62, categoryPercentage: .72 },
            { label: 'Total tiket selesai', data: monthly.selesai, backgroundColor: '#EE8500', borderRadius: 4, barPercentage: .62, categoryPercentage: .72 },
          ]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          animation: { duration: 700, easing: 'easeOutQuart' },
          plugins: { legend: { display: true, position: 'right', labels: { usePointStyle: true, pointStyle: 'rectRounded', boxWidth: 9, boxHeight: 9, padding: 14, font: { size: 10, weight: 600 } } }, tooltip: { backgroundColor: '#10221B', padding: 10, cornerRadius: 8 } },
          scales: {
            x: { stacked: false, grid: { display: false }, border: { display: false }, ticks: { font: { size: 10, weight: 600 } } },
            y: { beginAtZero: true, grid: { color: '#E5E5E5' }, border: { display: false }, ticks: { precision: 0, stepSize: 1, font: { size: 11, weight: 600 } } }
          }
        }
      });

      const ct = document.getElementById('chartTipe');
      if (chartTipe) chartTipe.destroy();
      const typeCounts = {};
      tickets.forEach(t => { typeCounts[t.tipe] = (typeCounts[t.tipe] || 0) + 1; });
      const typeLabels = Object.keys(typeCounts);
      chartTipe = new Chart(ct, {
        type: 'doughnut',
        data: { labels: typeLabels, datasets: [{ data: typeLabels.map(k => typeCounts[k]), backgroundColor: ['#2188E5', '#9BC2E6', '#EE8500', '#6C63D9'], borderWidth: 0, hoverOffset: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', animation: { animateRotate: true, duration: 700 }, plugins: { legend: { display: false }, tooltip: { backgroundColor: '#10221B', padding: 10, cornerRadius: 8 } } }
      });

      const total = typeLabels.reduce((sum, k) => sum + typeCounts[k], 0) || 1;
      const legend = document.getElementById('type-legend');
      if (legend) legend.innerHTML = typeLabels.map((k, i) => `<div class="legend-row"><span class="legend-left"><span class="legend-sw" style="background:${['#2188E5','#9BC2E6','#EE8500','#6C63D9'][i % 4]};"></span>${esc(k)}</span><span class="legend-val">${Math.round(typeCounts[k] / total * 100)}%</span></div>`).join('');

      const yearTickets = tickets.filter(t => String(t.created_at || '').startsWith(String(year)));
      const yearDone = yearTickets.filter(t => t.status === 'selesai').length;
      const totalEl = document.querySelector('#page-analitik .kpi-card:nth-child(1) .count-up');
      const doneEl = document.querySelector('#page-analitik .kpi-card:nth-child(2) .count-up');
      if (totalEl) totalEl.dataset.target = yearTickets.length;
      if (doneEl) doneEl.dataset.target = yearDone;
    }



  /* Global search: cari tiket/karyawan dari topbar */
  const globalSearch = document.querySelector('.search-box input');
  globalSearch?.addEventListener('keydown', e => {
    if (e.key !== 'Enter') return;
    const q = e.target.value.trim().toLowerCase();
    if (!q) return;
    const ticket = tickets.find(t => [t.id,t.judul,t.kontak,t.tipe].some(v => String(v).toLowerCase().includes(q)));
    if (ticket) {
      showPage('tiket-detail');
      renderTicketDetail(ticket.id);
      showToast(`Tiket ${ticket.id} ditemukan`, 'search');
      return;
    }
    const k = karyawanList.find(x => [x.nama,x.email,x.tlp].some(v => String(v).toLowerCase().includes(q)));
    if (k) {
      showPage('karyawan');
      document.getElementById('karyawan-search').value = e.target.value;
      renderKaryawan();
      showToast(`Karyawan ${k.nama} ditemukan`, 'search');
      return;
    }
    showToast('Data tidak ditemukan', 'triangle-alert');
  });

  /* Shortcut keyboard untuk terasa seperti aplikasi desktop */
  document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault(); globalSearch?.focus(); globalSearch?.select();
    }
  });

  /* Refresh icon rendering after dynamic pages are inserted */
  lucide.createIcons();

  if (window.__HELPDESK_BOOT?.authenticated) {
    await loadBootstrapData();
  }
}

initLoginFlow();
window.__helpdeskReady = bootHelpdesk().catch(err => {
  console.error('Helpdesk initialization failed:', err);
  return false;
});
