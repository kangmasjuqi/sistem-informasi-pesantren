@extends('layouts.crud')

@section('page-title', 'Input Kehadiran')

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    /* ── Selector card ───────────────────────────────────────── */
    .selector-card {
        background: #f8faff; border: 1.5px solid #c7d2fe;
        border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;
    }
    .selector-title {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: #4f46e5; margin-bottom: .75rem;
    }

    /* ── Jenis tabs ───────────────────────────────────────────── */
    .jenis-tabs {
        display: flex; gap: .5rem; margin-bottom: 1rem; flex-wrap: wrap;
    }
    .jenis-tab {
        padding: .4rem 1rem; border-radius: 999px; border: 1.5px solid #e5e7eb;
        font-size: .82rem; font-weight: 600; cursor: pointer;
        transition: all .15s; background: #fff; color: #6b7280;
    }
    .jenis-tab:hover    { border-color: #6366f1; color: #4f46e5; background: #eef2ff; }
    .jenis-tab.active   { border-color: #4f46e5; color: #fff; background: #4f46e5; }

    /* ── Context bar ─────────────────────────────────────────── */
    .context-bar {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        border-radius: 12px; padding: 1rem 1.5rem; color: #fff;
        margin-bottom: 1.25rem; display: flex; align-items: center;
        justify-content: space-between; gap: 1rem; flex-wrap: wrap;
    }
    .context-bar-title { font-size: 1.05rem; font-weight: 800; margin: 0; }
    .context-bar-sub   { font-size: .78rem; opacity: .8; margin: 0 0 .15rem; }

    /* ── Summary bar ─────────────────────────────────────────── */
    .summary-bar {
        display: flex; gap: .75rem; flex-wrap: wrap; margin-bottom: 1rem;
    }
    .summary-pill {
        display: flex; align-items: center; gap: .4rem;
        padding: .3rem .9rem; border-radius: 999px;
        font-size: .8rem; font-weight: 700; border: 1.5px solid transparent;
    }
    .s-hadir { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
    .s-sakit { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
    .s-izin  { background: #dbeafe; color: #1e40af; border-color: #93c5fd; }
    .s-alpa  { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
    .s-belum { background: #f3f4f6; color: #6b7280; border-color: #d1d5db; }

    /* ── Already submitted banner ────────────────────────────── */
    .sudah-banner {
        background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px;
        padding: .6rem 1rem; margin-bottom: 1rem; font-size: .82rem; color: #92400e;
        display: flex; align-items: center; justify-content: space-between; gap: .5rem;
    }

    /* ── Santri list ─────────────────────────────────────────── */
    .santri-list { display: flex; flex-direction: column; gap: .4rem; }

    .santri-row {
        display: flex; align-items: center; gap: .75rem;
        padding: .6rem .75rem; border-radius: 10px;
        border: 1.5px solid #e5e7eb; background: #fff;
        transition: border-color .15s, background .15s;
    }
    .santri-row:hover { background: #f8faff; border-color: #c7d2fe; }
    .santri-row.status-sakit { border-left: 4px solid #f59e0b; background: #fffbeb; }
    .santri-row.status-izin  { border-left: 4px solid #3b82f6; background: #eff6ff; }
    .santri-row.status-alpa  { border-left: 4px solid #ef4444; background: #fef2f2; }
    .santri-row.status-hadir { border-left: 4px solid #10b981; }

    .santri-avatar {
        width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .82rem; color: #fff;
    }
    .santri-info { flex: 1; min-width: 0; }
    .santri-name { font-weight: 700; font-size: .88rem; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .santri-nis  { font-size: .72rem; color: #9ca3af; }

    /* ── Status pill buttons ─────────────────────────────────── */
    .status-pills { display: flex; gap: .3rem; flex-shrink: 0; }
    .status-pill {
        padding: .25rem .65rem; border-radius: 999px; font-size: .75rem; font-weight: 700;
        border: 1.5px solid transparent; cursor: pointer; transition: all .12s;
        white-space: nowrap;
    }
    .status-pill.pill-hadir         { border-color: #d1d5db; color: #6b7280; background: #f9fafb; }
    .status-pill.pill-hadir.active  { border-color: #10b981; color: #065f46; background: #d1fae5; }
    .status-pill.pill-sakit         { border-color: #d1d5db; color: #6b7280; background: #f9fafb; }
    .status-pill.pill-sakit.active  { border-color: #f59e0b; color: #92400e; background: #fef3c7; }
    .status-pill.pill-izin          { border-color: #d1d5db; color: #6b7280; background: #f9fafb; }
    .status-pill.pill-izin.active   { border-color: #3b82f6; color: #1e40af; background: #dbeafe; }
    .status-pill.pill-alpa          { border-color: #d1d5db; color: #6b7280; background: #f9fafb; }
    .status-pill.pill-alpa.active   { border-color: #ef4444; color: #991b1b; background: #fee2e2; }

    /* ── Note input ──────────────────────────────────────────── */
    .note-input {
        border: 1.5px solid #e5e7eb; border-radius: 6px; padding: .2rem .5rem;
        font-size: .75rem; color: #374151; width: 130px;
        transition: border-color .15s; outline: none;
    }
    .note-input:focus { border-color: #6366f1; }
    .note-input.has-note { border-color: #a5b4fc; background: #eef2ff; }

    /* ── Save bar ────────────────────────────────────────────── */
    .save-bar {
        position: sticky; bottom: 0; background: #fff;
        border-top: 2px solid #e5e7eb; padding: .75rem 1rem 1.5rem;
        display: flex; align-items: center; justify-content: space-between;
        gap: 1rem; flex-wrap: wrap; z-index: 10;
        margin: 0 -1.25rem -1.25rem; border-radius: 0 0 12px 12px;
    }

    /* ── Quick-set toolbar ───────────────────────────────────── */
    .bulk-toolbar {
        display: flex; gap: .5rem; align-items: center; flex-wrap: wrap;
        padding: .5rem .75rem; background: #f8faff; border-radius: 8px;
        border: 1px solid #e5e7eb; margin-bottom: .75rem; font-size: .78rem;
    }
    .bulk-btn {
        padding: .2rem .7rem; border-radius: 999px; border: 1.5px solid #e5e7eb;
        font-size: .75rem; font-weight: 600; cursor: pointer; background: #fff;
        transition: all .12s;
    }
    .bulk-btn:hover { background: #eff6ff; border-color: #93c5fd; color: #1e40af; }

    /* ── Rekap table ─────────────────────────────────────────── */
    .rekap-table { width: 100%; font-size: .82rem; border-collapse: collapse; }
    .rekap-table th, .rekap-table td { border: 1px solid #e5e7eb; padding: .45rem .6rem; }
    .rekap-table th { background: #f8faff; font-weight: 700; text-align: center; }
    .rekap-table td:nth-child(1),
    .rekap-table td:nth-child(2) { text-align: left; }
    .rekap-table td:not(:nth-child(1)):not(:nth-child(2)) { text-align: center; }

    .persen-bar {
        display: flex; align-items: center; gap: .4rem;
    }
    .persen-track {
        flex: 1; background: #e5e7eb; border-radius: 999px; height: 5px;
    }
    .persen-fill { height: 5px; border-radius: 999px; background: #10b981; }

    /* ── Placeholder ─────────────────────────────────────────── */
    .absen-placeholder {
        text-align: center; padding: 3rem 1rem; color: #9ca3af;
    }
</style>
@endsection

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-outline-primary" id="btnRekap">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M14 2H2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v1H1V4zm0 2h14v6a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6z"/></svg>
        Rekap Bulanan
    </button>
</div>
@endsection

@section('content')

{{-- ── Selector card ─────────────────────────────────────── --}}
<div class="selector-card">
    <div class="selector-title">📌 Sesi Kehadiran</div>

    {{-- Jenis kehadiran tabs --}}
    <div class="jenis-tabs" id="jenisTabs">
        <button class="jenis-tab active" data-jenis="pelajaran">📚 Pelajaran</button>
        <button class="jenis-tab"        data-jenis="sholat">🕌 Sholat</button>
        <button class="jenis-tab"        data-jenis="kegiatan">🎯 Kegiatan</button>
    </div>

    {{-- Pelajaran context --}}
    <div id="ctxPelajaran">
        <div class="form-row" style="grid-template-columns: 1fr 2fr 2fr; align-items: end;">
            <div>
                <label class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="inputTanggal">
            </div>
            <div>
                <label class="form-label">Kelas</label>
                <select class="form-select" id="selPengampu"></select>
            </div>
            <div>
                <button class="btn btn-primary" id="btnLoad" disabled>
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                    Mulai Absen
                </button>
            </div>
        </div>
    </div>

    {{-- Sholat/Kegiatan context --}}
    <div id="ctxNonPelajaran" style="display:none;">
        <div class="form-row" style="grid-template-columns: 1fr 2fr 2fr auto; align-items: end;">
            <div>
                <label class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="inputTanggal2">
            </div>
            <div>
                <label class="form-label">Semester</label>
                <select class="form-select" id="selSemester" style="width:100%;"></select>
            </div>
            <div>
                <label class="form-label">Kelas</label>
                <select class="form-select" id="selKelas" style="width:100%;"></select>
            </div>
            <div>
                <button class="btn btn-primary" id="btnLoad2" disabled>
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                    Mulai
                </button>
            </div>
        </div>
        <div id="keteranganKegiatanWrap" style="display:none; margin-top:.75rem;">
            <label class="form-label">Nama Kegiatan</label>
            <input type="text" class="form-control" id="inputKeteranganKegiatan" placeholder="Contoh: Upacara, Olahraga Pagi..." style="max-width:400px;">
        </div>
    </div>
</div>

{{-- ── Absen section (shown after load) ─────────────────── --}}
<div id="absenSection" style="display:none;">

    {{-- Context bar --}}
    <div class="context-bar">
        <div>
            <p class="context-bar-sub" id="ctxSub"></p>
            <h2 class="context-bar-title" id="ctxTitle"></h2>
        </div>
        <div style="display:flex; gap:.5rem; align-items:center; flex-wrap:wrap;">
            <span id="ctxTanggal" style="background:rgba(255,255,255,.18); border-radius:999px; padding:.2rem .75rem; font-size:.78rem; font-weight:600;"></span>
        </div>
    </div>

    {{-- Summary pills --}}
    <div class="summary-bar" id="summaryBar"></div>

    {{-- Already submitted warning --}}
    <div class="sudah-banner" id="sudahBanner" style="display:none;">
        ⚠️ Kehadiran sesi ini <strong>sudah pernah diinput</strong>. Menyimpan ulang akan menimpa data sebelumnya.
        <button class="btn btn-sm btn-outline-warning" id="btnEditMode">Edit Data</button>
    </div>

    <div class="card">
        <div class="card-body" style="padding-bottom: 0;">

            {{-- Bulk toolbar --}}
            <div class="bulk-toolbar">
                <span style="color:#6b7280; font-weight:600;">Set semua:</span>
                <button class="bulk-btn" data-bulk="hadir">✅ Semua Hadir</button>
                <button class="bulk-btn" data-bulk="alpa">❌ Semua Alpa</button>
                <span style="margin-left:.5rem; color:#9ca3af;">|</span>
                <span style="color:#6b7280;">Filter:</span>
                <button class="bulk-btn" data-filter="semua" style="font-weight:700; border-color:#6366f1; color:#4f46e5;">Semua</button>
                <button class="bulk-btn" data-filter="hadir">✅ Hadir</button>
                <button class="bulk-btn" data-filter="tidak">⚠️ Tidak Hadir</button>
                <span style="margin-left: auto; font-size:.72rem; color:#9ca3af;" id="filterLabel"></span>
            </div>

            {{-- Santri list --}}
            <div class="santri-list" id="santriList"></div>

            {{-- Save bar --}}
            <div class="save-bar">
                <div style="font-size:.8rem; color:#6b7280;" id="saveInfo">
                    Tentukan status kehadiran, lalu klik Simpan.
                </div>
                <button class="btn btn-primary" id="btnSave">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854 5.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 11.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                    Simpan Kehadiran
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Placeholder ───────────────────────────────────────── --}}
<div id="absenPlaceholder" class="card">
    <div class="card-body absen-placeholder">
        <div style="font-size:2.5rem; margin-bottom:.5rem;">📋</div>
        <div style="font-weight:600; color:#6b7280;">Pilih sesi untuk memulai absensi</div>
        <div style="font-size:.82rem; margin-top:.25rem;">Pilih jenis, tanggal, dan mata pelajaran / kelas di atas</div>
    </div>
</div>

{{-- ══ REKAP MODAL ══════════════════════════════════════════ --}}
<div class="modal fade" id="rekapModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rekap Kehadiran Bulanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-row" style="grid-template-columns:1fr 1fr 1fr 1fr auto; align-items:end; margin-bottom:1rem;">
                    <div>
                        <label class="form-label">Semester</label>
                        <select class="form-select" id="rekapSemester" style="width:100%;"></select>
                    </div>
                    <div>
                        <label class="form-label">Kelas</label>
                        <select class="form-select" id="rekapKelas" style="width:100%;"></select>
                    </div>
                    <div>
                        <label class="form-label">Bulan</label>
                        <select class="form-select" id="rekapBulan">
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                            <option value="{{ $i+1 }}" {{ now()->month == $i+1 ? 'selected' : '' }}>{{ $bln }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Jenis</label>
                        <select class="form-select" id="rekapJenis">
                            <option value="pelajaran">Pelajaran</option>
                            <option value="sholat">Sholat</option>
                            <option value="kegiatan">Kegiatan</option>
                        </select>
                    </div>
                    <div>
                        <button class="btn btn-primary" id="btnLoadRekap">Tampilkan</button>
                    </div>
                </div>
                <div id="rekapContent" class="nilai-table-wrap"></div>
            </div>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay"><div class="spinner"></div></div>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {

    const csrfToken    = $('meta[name="csrf-token"]').attr('content');
    const ACTIVE_SEM_ID = {{ $activeSemester?->id ?? 'null' }};
    const ACTIVE_SEM_NM = "{{ $activeSemester?->nama ?? '' }}";

    // ── State ──────────────────────────────────────────────────
    let currentJenis   = 'pelajaran';
    let currentContext = null;   // from session() response
    let statusMap      = {};     // { santri_id → 'hadir'|'sakit'|'izin'|'alpa' }
    let noteMap        = {};     // { santri_id → string }
    let allSantri      = [];     // full list for filter
    let filterMode     = 'semua';

    // ── Avatar colours ─────────────────────────────────────────
    const AV_COLORS = ['#6366f1','#ec4899','#14b8a6','#f59e0b','#8b5cf6'];
    function avColor(name) { return AV_COLORS[(name||'?').charCodeAt(0) % AV_COLORS.length]; }
    function initials(name) {
        if (!name) return '?';
        const p = name.trim().split(' ');
        return p.length >= 2 ? (p[0][0]+p[1][0]).toUpperCase() : name.substring(0,2).toUpperCase();
    }

    // ── Default date = today ───────────────────────────────────
    const today = new Date().toISOString().split('T')[0];
    $('#inputTanggal, #inputTanggal2').val(today);

    // ── Jenis tabs ─────────────────────────────────────────────
    $('#jenisTabs').on('click', '.jenis-tab', function () {
        $('.jenis-tab').removeClass('active');
        $(this).addClass('active');
        currentJenis = $(this).data('jenis');

        if (currentJenis === 'pelajaran') {
            $('#ctxPelajaran').show();
            $('#ctxNonPelajaran, #keteranganKegiatanWrap').hide();
        } else {
            $('#ctxPelajaran').hide();
            $('#ctxNonPelajaran').show();
            $('#keteranganKegiatanWrap').toggle(currentJenis === 'kegiatan');
        }

        // Hide absen section when jenis changes
        $('#absenSection, #absenPlaceholder').hide();
        $('#absenPlaceholder').show();
    });

    // ── Select2: pengampu (pelajaran) — today's schedule ──────
    $('#selPengampu').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih kelas hari ini...',
        allowClear: true,
        ajax: {
            url: '{{ route("kehadiran.today-schedule") }}',
            dataType: 'json', delay: 100,
            data: () => ({
                tanggal:     $('#inputTanggal').val(),
                semester_id: ACTIVE_SEM_ID,
            }),
            processResults: d => ({ results: d.results }),
            cache: false,
        },
        minimumInputLength: 0,
    });

    // Reload pengampu list when date changes
    $('#inputTanggal').on('change', function () {
        $('#selPengampu').val(null).trigger('change');
        // Force Select2 to re-query
        $('#selPengampu').select2('destroy');
        $('#selPengampu').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih kelas hari ini...',
            allowClear: true,
            ajax: {
                url: '{{ route("kehadiran.today-schedule") }}',
                dataType: 'json', delay: 100,
                data: () => ({ tanggal: $('#inputTanggal').val(), semester_id: ACTIVE_SEM_ID }),
                processResults: d => ({ results: d.results }),
                cache: false,
            },
            minimumInputLength: 0,
        });
    });

    $('#selPengampu').on('change', function () {
        $('#btnLoad').prop('disabled', !$(this).val());
    });

    // ── Select2: semester + kelas (non-pelajaran) ──────────────
    $('#selSemester').select2({
        theme: 'bootstrap-5', placeholder: 'Pilih semester...',
        ajax: {
            url: '{{ route("kehadiran.search-semester") }}',
            dataType: 'json', delay: 250,
            data: p => ({ q: p.term ?? '' }),
            processResults: d => ({ results: d.results }), cache: true,
        }, minimumInputLength: 0,
    });

    if (ACTIVE_SEM_ID) {
        $('#selSemester').append(new Option(ACTIVE_SEM_NM + ' ★', ACTIVE_SEM_ID, true, true)).trigger('change');
    }

    $('#selKelas').select2({
        theme: 'bootstrap-5', placeholder: 'Pilih kelas...',
        ajax: {
            url: '{{ route("kehadiran.search-kelas") }}',
            dataType: 'json', delay: 250,
            data: p => ({ q: p.term ?? '', semester_id: $('#selSemester').val() }),
            processResults: d => ({ results: d.results }), cache: false,
        }, minimumInputLength: 0,
    });

    $('#selKelas').on('change', function () {
        $('#btnLoad2').prop('disabled', !$(this).val());
    });

    // ── Load session ───────────────────────────────────────────
    function loadSession() {
        const jenis  = currentJenis;
        const tanggal = jenis === 'pelajaran' ? $('#inputTanggal').val() : $('#inputTanggal2').val();

        const params = { tanggal, jenis };

        if (jenis === 'pelajaran') {
            params.pengampu_id = $('#selPengampu').val();
        } else {
            params.kelas_id = $('#selKelas').val();
            if (jenis === 'kegiatan') {
                params.keterangan_kegiatan = $('#inputKeteranganKegiatan').val();
            }
        }

        showLoading();
        $.get('{{ route("kehadiran.session") }}', params, function (res) {
            hideLoading();
            if (!res.success) { showNotification('error', 'Gagal memuat data.'); return; }

            currentContext = { ...res.context, ...params };
            allSantri      = res.santri;
            statusMap      = {};
            noteMap        = {};

            // Pre-fill from existing or default all to 'hadir'
            res.santri.forEach(s => {
                const ex = res.existing[s.id];
                statusMap[s.id] = ex ? ex.status   : 'hadir';
                noteMap[s.id]   = ex ? (ex.keterangan || '') : '';
            });

            buildContextBar(res.context);
            buildSummaryBar(res.summary, res.santri.length);
            renderList(allSantri);
            filterMode = 'semua';
            updateFilterLabel();

            if (res.sudah_diabsen) {
                $('#sudahBanner').show();
                $('#btnSave').prop('disabled', true).text('Edit untuk menyimpan');
            } else {
                $('#sudahBanner').hide();
                $('#btnSave').prop('disabled', false).html('<svg width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854 5.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 11.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg> Simpan Kehadiran');
            }

            $('#absenPlaceholder').hide();
            $('#absenSection').show();
        }).fail(xhr => { hideLoading(); handleAjaxError(xhr); });
    }

    $('#btnLoad').on('click', loadSession);
    $('#btnLoad2').on('click', loadSession);

    // Edit mode: re-enable save button
    $('#btnEditMode').on('click', function () {
        $('#sudahBanner').hide();
        $('#btnSave').prop('disabled', false).html('<svg width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854 5.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 11.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg> Simpan Perubahan');
    });

    // ── Build context bar ──────────────────────────────────────
    function buildContextBar(ctx) {
        $('#ctxSub').text(ctx.sub);
        $('#ctxTitle').text(ctx.label);
        $('#ctxTanggal').text('📅 ' + ctx.tanggal);
    }

    // ── Build summary pills ────────────────────────────────────
    function buildSummaryBar(summary, total) {
        const hadirCount = Object.values(statusMap).filter(s => s === 'hadir').length;
        const sakitCount = Object.values(statusMap).filter(s => s === 'sakit').length;
        const izinCount  = Object.values(statusMap).filter(s => s === 'izin').length;
        const alpaCount  = Object.values(statusMap).filter(s => s === 'alpa').length;

        $('#summaryBar').html(`
            <span class="summary-pill s-hadir">✅ ${hadirCount} Hadir</span>
            <span class="summary-pill s-sakit">🤒 ${sakitCount} Sakit</span>
            <span class="summary-pill s-izin">📋 ${izinCount} Izin</span>
            <span class="summary-pill s-alpa">❌ ${alpaCount} Alpa</span>
            <span class="summary-pill s-belum" style="margin-left:auto;">👥 ${total} Santri</span>
        `);
    }

    // ── Render santri list ─────────────────────────────────────
    function renderList(santriList) {
        let html = '';
        santriList.forEach(s => {
            const status = statusMap[s.id] || 'hadir';
            const note   = noteMap[s.id]   || '';
            html += buildRow(s, status, note);
        });
        $('#santriList').html(html || '<div class="absen-placeholder">Tidak ada santri.</div>');
    }

    function buildRow(s, status, note) {
        const pills = ['hadir','sakit','izin','alpa'].map(st =>
            `<button class="status-pill pill-${st} ${status === st ? 'active' : ''}"
                data-santri="${s.id}" data-status="${st}">
                ${({ hadir:'✅', sakit:'🤒', izin:'📋', alpa:'❌' })[st]}
                ${({ hadir:'Hadir', sakit:'Sakit', izin:'Izin', alpa:'Alpa' })[st]}
            </button>`
        ).join('');

        const noteClass = note ? 'has-note' : '';

        return `
        <div class="santri-row status-${status}" id="row-${s.id}" data-santri="${s.id}">
            <div class="santri-avatar" style="background:${avColor(s.nama_lengkap)};">${initials(s.nama_lengkap)}</div>
            <div class="santri-info">
                <div class="santri-name">${s.nama_lengkap}</div>
                <div class="santri-nis">${s.nis}</div>
            </div>
            <div class="status-pills">${pills}</div>
            <input type="text" class="note-input ${noteClass}"
                data-santri="${s.id}"
                value="${note}"
                placeholder="Catatan...">
        </div>`;
    }

    // ── Status pill click ──────────────────────────────────────
    $(document).on('click', '.status-pill', function () {
        const santriId = $(this).data('santri');
        const status   = $(this).data('status');

        statusMap[santriId] = status;

        // Update pills in this row
        const $row = $(`#row-${santriId}`);
        $row.find('.status-pill').removeClass('active');
        $(this).addClass('active');

        // Update row accent
        $row.removeClass('status-hadir status-sakit status-izin status-alpa')
            .addClass(`status-${status}`);

        buildSummaryBar(null, allSantri.length);
        updateSaveInfo();
    });

    // ── Note input ─────────────────────────────────────────────
    $(document).on('input', '.note-input', function () {
        const santriId = $(this).data('santri');
        noteMap[santriId] = $(this).val();
        $(this).toggleClass('has-note', $(this).val() !== '');
    });

    // ── Bulk set buttons ───────────────────────────────────────
    $(document).on('click', '[data-bulk]', function () {
        const status = $(this).data('bulk');
        allSantri.forEach(s => { statusMap[s.id] = status; });
        renderList(getFilteredList());
        buildSummaryBar(null, allSantri.length);
        updateSaveInfo();
    });

    // ── Filter buttons ─────────────────────────────────────────
    $(document).on('click', '[data-filter]', function () {
        filterMode = $(this).data('filter');
        $('[data-filter]').css('font-weight', '400').css('border-color', '#e5e7eb').css('color', '#6b7280');
        $(this).css('font-weight', '700').css('border-color', '#6366f1').css('color', '#4f46e5');
        renderList(getFilteredList());
        updateFilterLabel();
    });

    function getFilteredList() {
        if (filterMode === 'tidak') return allSantri.filter(s => statusMap[s.id] !== 'hadir');
        if (filterMode === 'hadir') return allSantri.filter(s => statusMap[s.id] === 'hadir');
        return allSantri;
    }

    function updateFilterLabel() {
        const shown = getFilteredList().length;
        const total = allSantri.length;
        $('#filterLabel').text(shown === total ? `${total} santri` : `${shown} dari ${total} santri`);
    }

    function updateSaveInfo() {
        const hadirCount = Object.values(statusMap).filter(s => s === 'hadir').length;
        const tidakHadir = allSantri.length - hadirCount;
        $('#saveInfo').text(`${hadirCount} hadir · ${tidakHadir} tidak hadir`);
    }

    // ── Save ───────────────────────────────────────────────────
    $('#btnSave').on('click', function () {
        if (!currentContext) return;

        const rows = allSantri.map(s => ({
            santri_id:   s.id,
            status:      statusMap[s.id] || 'hadir',
            keterangan:  noteMap[s.id] || null,
        }));

        const payload = {
            _token:               csrfToken,
            tanggal:              currentContext.tanggal,
            jenis:                currentContext.jenis,
            pengampu_id:          currentContext.pengampu_id ?? null,
            kelas_id:             currentContext.kelas_id ?? null,
            keterangan_kegiatan:  currentContext.keterangan_kegiatan ?? null,
            rows,
        };

        showLoading();
        $.ajax({
            url: '{{ route("kehadiran.batch-save") }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: res => {
                hideLoading();
                $('#sudahBanner').show();
                $('#btnSave').prop('disabled', true).text('✅ Tersimpan');
                showNotification('success', res.message);
            },
            error: xhr => { hideLoading(); handleAjaxError(xhr); },
        });
    });

    // ── Rekap modal ────────────────────────────────────────────
    $('#btnRekap').on('click', () => $('#rekapModal').modal('show'));

    // Init rekap Select2s
    function s2(sel, url, placeholder, parent, extra) {
        return $(sel).select2({
            theme: 'bootstrap-5',
            dropdownParent: parent ? $(parent) : undefined,
            placeholder, allowClear: true,
            ajax: {
                url, dataType: 'json', delay: 250,
                data: p => ({ q: p.term ?? '', ...(extra ? extra() : {}) }),
                processResults: d => ({ results: d.results }), cache: false,
            }, minimumInputLength: 0,
        });
    }

    s2('#rekapSemester', '{{ route("kehadiran.search-semester") }}', 'Pilih semester...', '#rekapModal');
    s2('#rekapKelas',    '{{ route("kehadiran.search-kelas") }}',    'Pilih kelas...',    '#rekapModal', () => ({ semester_id: $('#rekapSemester').val() }));

    if (ACTIVE_SEM_ID) {
        $('#rekapSemester').append(new Option(ACTIVE_SEM_NM + ' ★', ACTIVE_SEM_ID, true, true)).trigger('change');
    }

    $('#btnLoadRekap').on('click', function () {
        const kelId  = $('#rekapKelas').val();
        const bulan  = $('#rekapBulan').val();
        const jenis  = $('#rekapJenis').val();
        if (!kelId) { showNotification('error', 'Pilih kelas terlebih dahulu.'); return; }

        showLoading();
        $.get('{{ route("kehadiran.rekap") }}', {
            kelas_id: kelId,
            bulan,
            tahun:    new Date().getFullYear(),
            jenis,
        }, function (res) {
            hideLoading();
            renderRekap(res.data);
        }).fail(xhr => { hideLoading(); handleAjaxError(xhr); });
    });

    function renderRekap(data) {
        if (!data || !data.length) {
            $('#rekapContent').html('<div class="absen-placeholder">Tidak ada data kehadiran.</div>');
            return;
        }
        let html = '<table class="rekap-table"><thead><tr>'
            + '<th>NIS</th><th>Nama Santri</th>'
            + '<th>Hadir</th><th>Sakit</th><th>Izin</th><th>Alpa</th><th>Total</th><th>% Hadir</th>'
            + '</tr></thead><tbody>';

        data.forEach(r => {
            const pct   = r.persen_hadir;
            const color = pct >= 80 ? '#10b981' : pct >= 60 ? '#f59e0b' : '#ef4444';
            html += `<tr>
                <td style="color:#9ca3af;font-size:.75rem;">${r.nis}</td>
                <td><strong>${r.nama_lengkap}</strong></td>
                <td style="color:#059669; font-weight:700;">${r.hadir}</td>
                <td style="color:#d97706; font-weight:700;">${r.sakit}</td>
                <td style="color:#2563eb; font-weight:700;">${r.izin}</td>
                <td style="color:#dc2626; font-weight:700;">${r.alpa}</td>
                <td>${r.total}</td>
                <td>
                    <div class="persen-bar">
                        <span style="font-weight:700; color:${color}; min-width:38px;">${pct}%</span>
                        <div class="persen-track">
                            <div class="persen-fill" style="width:${pct}%; background:${color};"></div>
                        </div>
                    </div>
                </td>
            </tr>`;
        });

        html += '</tbody></table>';
        $('#rekapContent').html(html);
    }

    // ── Helpers ───────────────────────────────────────────────
    function showLoading()  { $('#loadingOverlay').addClass('show'); }
    function hideLoading()  { $('#loadingOverlay').removeClass('show'); }

    function handleAjaxError(xhr) {
        let msg = 'Terjadi kesalahan pada server';
        if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
        if (xhr.responseJSON?.errors)  msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
        Swal.fire({ icon: 'error', title: 'Error!', text: msg, confirmButtonColor: '#ef4444' });
    }
});
</script>
@endsection