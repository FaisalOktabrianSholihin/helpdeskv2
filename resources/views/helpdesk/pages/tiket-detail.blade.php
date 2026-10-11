<!DOCTYPE html>
<section class="page" id="page-tiket-detail">
<button class="back-link" data-page="tiket-list"><svg class="icon" data-lucide="arrow-left" style="width:15px;height:15px;"></svg>Kembali</button>
<div class="breadcrumb"><a data-page="tiket-list">Tiket</a> <svg class="icon" data-lucide="chevron-right" style="width:12px;height:12px;"></svg> Detail Tiket</div>
<h1 style="font-size:20px; font-weight:800; margin:4px 0 20px;">Tiket <span class="mono" id="detail-id" style="color:var(--primary);">#885</span></h1>
<div class="detail-grid">
<div>
<div class="card" style="margin-bottom:18px;">
<div class="card-head">
<h3>Detail Tiket</h3><span class="badge" id="detail-status-badge"><span class="dot"></span>Menunggu</span>
</div>
<div class="info-grid">
<div>
<div class="info-label">Keadaan</div>
<div class="info-value" id="detail-keadaan">Menunggu</div>
</div>
<div>
<div class="info-label">Prioritas</div><span class="badge" id="detail-prioritas-badge">Rendah</span>
</div>
<div>
<div class="info-label">Sumber</div>
<div class="info-value" id="detail-sumber">Telepon</div>
</div>
<div>
<div class="info-label">Tipe</div>
<div class="info-value" id="detail-tipe">Feature Request</div>
</div>
</div>
<div class="divider" style="margin:0 20px;"></div>
<div class="info-grid" style="grid-template-columns:1fr;">
<div>
<div class="info-label">Agen</div>
<div class="avatar-row" style="margin-top:6px;">
<div class="avatar-sm">MA</div>
<div class="info-value" id="detail-agen">Muhammad Andre</div>
<span style="color:var(--text-mute);">·</span>
<div class="avatar-sm" id="detail-kontak-initial">S</div>
<div class="info-value" id="detail-kontak">Sahrindra</div>
</div>
</div>
</div>
<p id="detail-meta" style="margin:2px 20px 18px; font-size:12px; color:var(--text-mute);">oleh Bagus
                  Tantowi pada Wed, 11 Feb at 9:33 AM via Telepon</p>
<div class="section-title" style="margin-top:0;">Deskripsi</div>
<div class="desc-box" id="detail-desc">Tolong buatkan rekomendasi perangkat komputer untuk kantor baru,
                  terima kasih.</div>
<div class="section-title">Komentar</div>
<div id="detail-comments">
<div class="comment">
<div class="avatar-sm" style="width:30px;height:30px;font-size:12px;">MS</div>
<div class="comment-body">
<div class="comment-name">Muhammad Andre Sahrindra<span class="comment-time">Wed, 11 Feb at 2:58
                          PM · Email keluar</span></div>
                      Hi Bagus Tantowi,<br/><br/>
                      Setelah dibahas lebih lanjut, kami merekomendasikan spesifikasi perangkat sebagai berikut:
                      <ol>
<li>Motherboard ASUS/MSI</li>
<li>RAM 16GB DDR4 (2 keping)</li>
<li>PSU Deepcool 500 W</li>
<li>Processor Core i5 gen 12</li>
<li>SSD NVME 1TB</li>
</ol>
                      Demikian rekomendasi yang dapat kami sampaikan, atas perhatiannya kami ucapkan terima kasih.
                    </div>
</div>
</div>
</div>
<div class="card">
<div class="card-head" style="padding-bottom:14px;">
<h3>Tambah balasan</h3>
</div>
<div class="card-body" style="padding-top:0;">
<textarea class="form-textarea" id="reply-text" placeholder="Tulis balasan untuk pelanggan..." style="min-height:90px;"></textarea>
<div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; flex-wrap:wrap; gap:10px;">
<div style="display:flex; gap:8px;">
<button class="icon-btn" style="width:34px;height:34px;"><svg class="icon" data-lucide="paperclip" style="width:16px;height:16px;"></svg></button>
<button class="icon-btn" style="width:34px;height:34px;"><svg class="icon" data-lucide="image" style="width:16px;height:16px;"></svg></button>
</div>
<button class="btn btn-blue btn-sm" id="send-reply-btn"><svg class="icon" data-lucide="send" style="width:14px;height:14px;"></svg>Kirim Balasan</button>
</div>
</div>
</div>
</div>
<div>
<div class="card" style="margin-bottom:18px;">
<div class="card-head">
<h3>Informasi Tiket</h3>
</div>
<div style="padding:6px 20px 18px;">
<div class="legend-row"><span class="legend-left" style="color:var(--text-mute); font-weight:600;">ID
                      Tiket</span><span class="legend-val mono" id="info-id">#885</span></div>
<div class="legend-row"><span class="legend-left" style="color:var(--text-mute); font-weight:600;">Dibuat</span><span class="legend-val" id="info-dibuat" style="font-weight:700;">11 Feb 2025 09:33</span></div>
<div class="legend-row"><span class="legend-left" style="color:var(--text-mute); font-weight:600;">Terakhir Update</span><span class="legend-val" id="info-updated" style="font-weight:700;">11 Feb 2025 13:56</span></div>
<div class="legend-row"><span class="legend-left" style="color:var(--text-mute); font-weight:600;">Status</span><span class="badge" id="info-status-badge">Menunggu</span></div>
<div class="legend-row"><span class="legend-left" style="color:var(--text-mute); font-weight:600;">Prioritas</span><span class="badge" id="info-prioritas-badge">Rendah</span></div>
<div class="legend-row"><span class="legend-left" style="color:var(--text-mute); font-weight:600;">Tipe</span><span class="legend-val" id="info-tipe" style="font-weight:700;">Feature Request</span></div>
<div class="legend-row"><span class="legend-left" style="color:var(--text-mute); font-weight:600;">Telepon</span><span class="legend-val" id="info-telepon" style="font-weight:700;">0813-1234-5678</span></div>
</div>
</div>
<div class="card">
<div class="card-head">
<h3>Aksi</h3>
</div>
<div class="aksi-stack">
<button class="btn btn-ghost" id="edit-tiket-btn"><svg class="icon" data-lucide="pencil" style="width:15px;height:15px;"></svg>Edit Tiket</button>
<button class="btn btn-ghost" id="cetak-tiket-btn"><svg class="icon" data-lucide="printer" style="width:15px;height:15px;"></svg>Cetak Tiket</button>
<button class="btn btn-danger-soft" id="tutup-tiket-btn"><svg class="icon" data-lucide="circle-x" style="width:15px;height:15px;"></svg>Tutup Tiket</button>
<button class="btn btn-danger-soft" id="hapus-tiket-btn" style="background:#fff; border:1px solid var(--border); color:var(--rose);"><svg class="icon" data-lucide="trash-2" style="width:15px;height:15px;"></svg>Hapus Tiket</button>
</div>
</div>
</div>
</div>
</section>
