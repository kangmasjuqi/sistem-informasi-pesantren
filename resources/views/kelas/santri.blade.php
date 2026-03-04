@extends('layouts.crud')

@section('page-title', 'Kelola Santri — ' . $kelas->nama_kelas)

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    .kelas-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        border-radius: 12px;
        padding: 1.5rem 2rem;
        color: #fff;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    .kelas-hero-title { font-size: 1.5rem; font-weight: 800; margin: 0 0 .2rem 0; }
    .kelas-hero-sub   { font-size: .875rem; opacity: .8; margin: 0; }
    .kelas-stats      { display: flex; gap: 1.5rem; flex-wrap: wrap; }
    .kelas-stat       { text-align: center; }
    .kelas-stat-val   { font-size: 1.6rem; font-weight: 800; line-height: 1; }
    .kelas-stat-lbl   { font-size: .7rem; opacity: .75; text-transform: uppercase; letter-spacing: .06em; }

    .cap-bar-wrap  { width: 160px; }
    .cap-bar-track { background: rgba(255,255,255,.25); border-radius: 999px; height: 8px; margin-top: .4rem; }
    .cap-bar-fill  { height: 8px; border-radius: 999px; background: #fff; transition: width .4s; }

    .readonly-banner {
        background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px;
        padding: .75rem 1rem; margin-bottom: 1rem;
        display: flex; align-items: center; gap: .5rem;
        font-size: .875rem; color: #92400e;
    }

    /* Enroll modal steps */
    .enroll-step { display: none; }
    .enroll-step.active { display: block; }
</style>
@endsection

@php
    $jumlah   = $kelas->santriAktif()->count();
    $kapasitas = $kelas->kapasitas;
    $pct      = $kapasitas > 0 ? min(100, round($jumlah / $kapasitas * 100)) : 0;
    $barColor = $pct >= 100 ? '#ef4444' : ($pct >= 80 ? '#f59e0b' : '#fff');
@endphp
@section('header-actions')
<div class="action-buttons d-flex gap-2 align-items-center">
    <a href="{{ route('kelas.index') }}" class="btn btn-outline-primary">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
        Kembali
    </a>
    @if($kelas->status !== 'completed' && $jumlah < $kapasitas)
    <button class="btn btn-primary" id="btnEnroll">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
        Masukkan Santri
    </button>
    @endif
</div>
@endsection

@section('content')

{{-- ── Kelas Hero Card ── --}}
<div class="kelas-hero">
    <div>
        <p class="kelas-hero-sub">
            {{ $kelas->tahunAjaran?->nama ?? '—' }}
            &nbsp;·&nbsp;
            Tingkat {{ $kelas->tingkat }}
            @if($kelas->waliKelas)
                &nbsp;·&nbsp; Wali Kelas: {{ $kelas->waliKelas->nama_lengkap }}
            @endif
        </p>
        <h1 class="kelas-hero-title">{{ $kelas->nama_kelas }}</h1>
        @if($kelas->deskripsi)
            <p class="kelas-hero-sub" style="margin-top:.3rem;">{{ $kelas->deskripsi }}</p>
        @endif
    </div>

    <div class="kelas-stats">
        <div class="kelas-stat">
            <div class="kelas-stat-val">{{ $jumlah }}</div>
            <div class="kelas-stat-lbl">Aktif</div>
        </div>
        <div class="kelas-stat">
            <div class="kelas-stat-val">{{ $kapasitas }}</div>
            <div class="kelas-stat-lbl">Kapasitas</div>
        </div>
        <div class="kelas-stat cap-bar-wrap">
            <div style="font-size:1rem; font-weight:700;">{{ $pct }}%</div>
            <div class="kelas-stat-lbl">Terisi</div>
            <div class="cap-bar-track">
                <div class="cap-bar-fill" style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Read-only banner ── --}}
@if($kelas->status === 'completed')
<div class="readonly-banner">
    <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>
    Kelas ini sudah <strong>Selesai</strong>. Data hanya bisa dilihat, tidak dapat diubah.
</div>
@endif

{{-- ── Main Table Card ── --}}
<div class="card">
    <div class="card-body">

        {{-- Filter bar --}}
        <div class="filters-section" style="margin-bottom:1rem;">
            <div class="filters-grid" style="grid-template-columns: 1fr 1fr auto;">
                <div>
                    <label class="form-label">Cari Santri</label>
                    <input type="text" class="form-control" id="filterCari" placeholder="Nama atau NIS...">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                    </select>
                </div>
                <div style="display:flex; align-items:end;">
                    <button class="btn btn-primary" id="btnApplyFilters" style="width:100%;">Terapkan</button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="santriTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>Santri</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Keluar</th>
                        <th>Durasi</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        @if($kelas->status !== 'completed')
                        <th width="80">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL: Enroll Santri
══════════════════════════════════════════════════ --}}
@if($kelas->status !== 'completed')
<div class="modal fade" id="enrollModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Masukkan Santri ke Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="enrollForm">
                <div class="modal-body">

                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Pilih Santri
                        </div>
                        <label class="form-label">Santri <span class="badge badge-required">WAJIB</span></label>
                        <select class="form-select" id="enroll_santri_id" name="santri_id" style="width:100%;" required></select>
                        <div class="form-text">Hanya santri yang belum aktif di kelas manapun pada tahun ajaran ini yang ditampilkan.</div>
                    </div>

                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Detail Pendaftaran
                        </div>
                        <div class="form-row">
                            <div>
                                <label class="form-label">Tanggal Masuk <span class="badge badge-required">WAJIB</span></label>
                                <input type="date" class="form-control" id="enroll_tanggal_masuk" name="tanggal_masuk" required>
                            </div>
                            <div>
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="enroll_keterangan" name="keterangan" placeholder="Opsional...">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854 5.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 11.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                        Daftarkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL: Exit Santri
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="exitModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="background:#fef2f2; border-bottom-color:#fecaca;">
                <h5 class="modal-title" style="color:#991b1b;">Keluarkan Santri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="exitForm">
                <div class="modal-body">
                    <input type="hidden" id="exit_ks_id">

                    <div style="background:#f9fafb; border-radius:8px; padding:.75rem 1rem; margin-bottom:1rem; border-left:3px solid #2563eb;">
                        <div style="font-size:.75rem; color:#6b7280;">Santri</div>
                        <div id="exit_santri_nama" style="font-weight:700; color:#111827;"></div>
                    </div>

                    <div style="margin-bottom:1rem;">
                        <label class="form-label">Alasan Keluar <span class="badge badge-required">WAJIB</span></label>
                        <div style="display:flex; flex-direction:column; gap:.5rem;" id="exitStatusGroup">
                            <label class="exit-radio-card" data-value="lulus">
                                <input type="radio" name="exit_status" value="lulus" style="display:none;">
                                <span class="exit-radio-icon">🎓</span>
                                <span>
                                    <strong>Lulus</strong>
                                    <small style="display:block; color:#6b7280;">Menyelesaikan jenjang kelas</small>
                                </span>
                            </label>
                            <label class="exit-radio-card" data-value="pindah">
                                <input type="radio" name="exit_status" value="pindah" style="display:none;">
                                <span class="exit-radio-icon">🔄</span>
                                <span>
                                    <strong>Pindah Kelas</strong>
                                    <small style="display:block; color:#6b7280;">Berpindah ke kelas lain</small>
                                </span>
                            </label>
                            <label class="exit-radio-card" data-value="keluar">
                                <input type="radio" name="exit_status" value="keluar" style="display:none;">
                                <span class="exit-radio-icon">🚪</span>
                                <span>
                                    <strong>Keluar</strong>
                                    <small style="display:block; color:#6b7280;">Meninggalkan pesantren</small>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div style="margin-bottom:1rem;">
                        <label class="form-label">Tanggal Keluar <span class="badge badge-required">WAJIB</span></label>
                        <input type="date" class="form-control" id="exit_tanggal_keluar" name="tanggal_keluar" required>
                    </div>

                    <div>
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" id="exit_keterangan" name="keterangan" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" id="btnExitSubmit" disabled>
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/><path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/></svg>
                        Keluarkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="loading-overlay" id="loadingOverlay"><div class="spinner"></div></div>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Exit modal radio cards */
    .exit-radio-card {
        display: flex; align-items: center; gap: .75rem;
        border: 2px solid #e5e7eb; border-radius: 8px;
        padding: .6rem .9rem; cursor: pointer;
        transition: border-color .15s, background .15s;
    }
    .exit-radio-card:hover        { border-color: #93c5fd; background: #eff6ff; }
    .exit-radio-card.selected     { border-color: #2563eb; background: #eff6ff; }
    .exit-radio-card.sel-lulus    { border-color: #10b981; background: #ecfdf5; }
    .exit-radio-card.sel-pindah   { border-color: #f59e0b; background: #fffbeb; }
    .exit-radio-card.sel-keluar   { border-color: #ef4444; background: #fef2f2; }
    .exit-radio-icon { font-size: 1.4rem; }
</style>
<script>
$(document).ready(function () {

    const KELAS_ID       = {{ $kelas->id }};
    const IS_COMPLETED   = {{ $kelas->status === 'completed' ? 'true' : 'false' }};
    const csrfToken      = $('meta[name="csrf-token"]').attr('content');
    const STATUS_OPTIONS = @json($statusOptions);

    // ── Build filter status dropdown from server data ─────────
    Object.entries(STATUS_OPTIONS).forEach(([val, opt]) => {
        $('#filterStatus').append(new Option(opt.label, val));
    });

    // ── DataTable ─────────────────────────────────────────────
    const colDefs = [
        {
            data: null,
            render: r => `<div>
                <strong>${r.santri_nama}</strong>
                <br><small class="text-muted">${r.santri_nis}</small>
            </div>`,
        },
        { data: 'tanggal_masuk_fmt',  render: d => d ?? '—' },
        { data: 'tanggal_keluar_fmt', render: d => d ? `<span class="text-muted">${d}</span>` : '—' },
        { data: 'durasi_label',       render: d => `<small class="text-muted">${d}</small>` },
        {
            data: 'keterangan',
            render: d => d
                ? `<span title="${d}" style="max-width:150px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${d}</span>`
                : '<span class="text-muted">—</span>',
        },
        {
            data: 'status',
            render: function (d) {
                const s = STATUS_OPTIONS[d] ?? { label: d, cls: 'badge-default' };
                return `<span class="status-badge ${s.cls}">${s.label.toUpperCase()}</span>`;
            },
        },
    ];

    // Only add action column if not completed
    if (!IS_COMPLETED) {
        colDefs.push({
            data: null,
            orderable: false,
            render: function (row) {
                if (row.status !== 'aktif') return '<span class="text-muted" style="font-size:.75rem;">—</span>';
                return `<button class="btn btn-sm btn-danger btn-exit"
                            data-id="${row.id}"
                            data-nama="${row.santri_nama}"
                            data-masuk="${row.tanggal_masuk}"
                            title="Keluarkan Santri">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                        <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                    </svg>
                </button>`;
            },
        });
    }

    const table = $('#santriTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `/kelas/${KELAS_ID}/santri/data`,
            data: d => {
                d.status = $('#filterStatus').val();
                d.search = { value: $('#filterCari').val() };
            },
        },
        columns: colDefs,
        pageLength: 100,
        order: [[0, 'asc']],
        columnDefs: [{ type: 'string', targets: 0 }],
        language: {
            processing:'Memuat data...', search:'', lengthMenu:'Tampilkan _MENU_ data',
            info:'Menampilkan _START_–_END_ dari _TOTAL_ santri', infoEmpty:'0 data',
            infoFiltered:'(difilter dari _MAX_)', zeroRecords:'Tidak ada santri ditemukan',
            emptyTable:'Belum ada santri di kelas ini',
            paginate:{ first:'«', last:'»', next:'›', previous:'‹' },
        },
    });

    $('#btnApplyFilters').on('click', () => table.ajax.reload());
    $('#filterCari').on('keypress', e => { if (e.which === 13) table.ajax.reload(); });

    // ── Select2: enroll santri search ─────────────────────────
    if (!IS_COMPLETED) {
        $('#enroll_santri_id').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#enrollModal'),
            placeholder: 'Ketik NIS atau nama santri...',
            allowClear: true,
            ajax: {
                url: `/kelas/${KELAS_ID}/santri/available`,
                dataType: 'json',
                delay: 300,
                data: params => ({ q: params.term ?? '' }),
                processResults: data => ({ results: data.results }),
                cache: false, // always fresh — availability changes
            },
            minimumInputLength: 0,
        });

        // ── Open enroll modal ─────────────────────────────────
        $('#btnEnroll').on('click', function () {
            $('#enrollForm')[0].reset();
            $('#enroll_santri_id').val(null).trigger('change');
            // Default tanggal masuk to today
            $('#enroll_tanggal_masuk').val(new Date().toISOString().split('T')[0]);
            $('#enrollModal').modal('show');
        });

        // ── Submit enroll ─────────────────────────────────────
        $('#enrollForm').on('submit', function (e) {
            e.preventDefault();
            showLoading();
            $.ajax({
                url: `/kelas/${KELAS_ID}/santri`,
                method: 'POST',
                data: {
                    _token:         csrfToken,
                    santri_id:      $('#enroll_santri_id').val(),
                    tanggal_masuk:  $('#enroll_tanggal_masuk').val(),
                    keterangan:     $('#enroll_keterangan').val(),
                },
                success: res => {
                    hideLoading();
                    $('#enrollModal').modal('hide');
                    table.ajax.reload();
                    refreshHeroStats();
                    showNotification('success', res.message);
                },
                error: xhr => { hideLoading(); handleAjaxError(xhr); },
            });
        });

        // ── Exit modal: radio card UX ─────────────────────────
        $('#exitStatusGroup').on('click', '.exit-radio-card', function () {
            const val = $(this).data('value');
            $(this).find('input[type=radio]').prop('checked', true);
            $('.exit-radio-card').removeClass('selected sel-lulus sel-pindah sel-keluar');
            $(this).addClass(`selected sel-${val}`);
            $('#btnExitSubmit').prop('disabled', false);
        });

        // ── Open exit modal ───────────────────────────────────
        $(document).on('click', '.btn-exit', function () {
            const id    = $(this).data('id');
            const nama  = $(this).data('nama');
            const masuk = $(this).data('masuk');

            $('#exit_ks_id').val(id);
            $('#exit_santri_nama').text(nama);
            // Default tanggal keluar to today, min = tanggal masuk
            const today = new Date().toISOString().split('T')[0];
            $('#exit_tanggal_keluar').val(today).attr('min', masuk);
            $('#exit_keterangan').val('');
            $('.exit-radio-card').removeClass('selected sel-lulus sel-pindah sel-keluar');
            $('input[name=exit_status]').prop('checked', false);
            $('#btnExitSubmit').prop('disabled', true);

            $('#exitModal').modal('show');
        });

        // ── Submit exit ───────────────────────────────────────
        $('#exitForm').on('submit', function (e) {
            e.preventDefault();
            const ksId  = $('#exit_ks_id').val();
            const status = $('input[name=exit_status]:checked').val();

            if (!status) {
                showNotification('error', 'Pilih alasan keluar terlebih dahulu.');
                return;
            }

            showLoading();
            $.ajax({
                url: `/kelas/${KELAS_ID}/santri/${ksId}/exit`,
                method: 'POST',
                data: {
                    _token:          csrfToken,
                    _method:         'PATCH',
                    status:          status,
                    tanggal_keluar:  $('#exit_tanggal_keluar').val(),
                    keterangan:      $('#exit_keterangan').val(),
                },
                success: res => {
                    hideLoading();
                    $('#exitModal').modal('hide');
                    table.ajax.reload();
                    refreshHeroStats();
                    showNotification('success', res.message);
                },
                error: xhr => { hideLoading(); handleAjaxError(xhr); },
            });
        });
    }

    // ── Refresh hero capacity bar without full reload ─────────
    function refreshHeroStats() {
        // Light fetch to get updated jumlah_santri for this kelas
        $.get(`/kelas/${KELAS_ID}`, function (res) {
            if (!res.data) return;
            const j   = res.data.jumlah_santri ?? 0;
            const cap = res.data.kapasitas ?? 1;
            const pct = Math.min(100, Math.round(j / cap * 100));
            const color = pct >= 100 ? '#ef4444' : pct >= 80 ? '#f59e0b' : '#fff';
            $('.kelas-stat-val').first().text(j);
            $('.cap-bar-fill').css({ width: pct + '%', background: color });
            $('.kelas-stat-val').eq(2).prev().text(pct + '%');
        });
    }

    // ── Helpers ───────────────────────────────────────────────
    function showLoading()  { $('#loadingOverlay').addClass('show'); }
    function hideLoading()  { $('#loadingOverlay').removeClass('show'); }

    function showNotification(type, message) {
        Swal.fire({
            icon: type, title: type === 'success' ? 'Berhasil!' : 'Gagal!', text: message,
            timer: 3000, timerProgressBar: true, showConfirmButton: false,
            toast: true, position: 'top-end',
        });
    }

    function handleAjaxError(xhr) {
        let message = 'Terjadi kesalahan pada server';
        if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
        if (xhr.responseJSON?.errors)  message = Object.values(xhr.responseJSON.errors).flat().join('\n');
        Swal.fire({ icon: 'error', title: 'Error!', text: message, confirmButtonColor: '#ef4444' });
    }
});
</script>
@endsection