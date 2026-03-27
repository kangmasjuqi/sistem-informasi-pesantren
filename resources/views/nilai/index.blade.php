@extends('layouts.crud')

@section('page-title', 'Input Nilai')

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    /* ── Context bar ─────────────────────────────────────────── */
    .context-bar {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        border-radius: 12px; padding: 1.25rem 1.5rem; color: #fff;
        margin-bottom: 1.5rem; display: flex; align-items: center;
        justify-content: space-between; gap: 1rem; flex-wrap: wrap;
    }
    .context-bar-title { font-size: 1.1rem; font-weight: 800; margin: 0 0 .2rem; }
    .context-bar-sub   { font-size: .8rem; opacity: .8; margin: 0; }
    .context-pills     { display: flex; gap: .5rem; flex-wrap: wrap; }
    .context-pill {
        background: rgba(255,255,255,.15); border-radius: 999px;
        padding: .2rem .75rem; font-size: .75rem; font-weight: 600;
    }

    /* ── Selector card ───────────────────────────────────────── */
    .selector-card {
        background: #f8faff; border: 1.5px solid #c7d2fe;
        border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;
    }
    .selector-title {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: #4f46e5; margin-bottom: .75rem;
    }

    /* ── Spreadsheet ─────────────────────────────────────────── */
    .nilai-table-wrap {
        overflow-x: auto; border-radius: 10px; border: 1.5px solid #e5e7eb;
    }
    .nilai-table { border-collapse: collapse; width: 100%; font-size: .82rem; }
    .nilai-table th {
        background: #f8faff; padding: .6rem .75rem; border: 1px solid #e5e7eb;
        white-space: nowrap; font-weight: 700; color: #374151;
        position: sticky; top: 0; z-index: 2;
    }
    .nilai-table th:nth-child(1), .nilai-table td:nth-child(1) {
        position: sticky; left: 0; z-index: 3; background: #f8faff; min-width: 90px;
    }
    .nilai-table th:nth-child(2), .nilai-table td:nth-child(2) {
        position: sticky; left: 90px; z-index: 3; background: #fff;
        min-width: 180px; border-right: 2px solid #c7d2fe;
    }
    .nilai-table th:nth-child(2) { background: #f8faff; }
    .nilai-table td { border: 1px solid #e5e7eb; padding: .3rem .5rem; vertical-align: middle; }
    .nilai-table tr:hover td,
    .nilai-table tr:hover td:nth-child(1),
    .nilai-table tr:hover td:nth-child(2) { background: #eff6ff; }

    .score-input {
        width: 68px; border: 1.5px solid #e5e7eb; border-radius: 6px;
        padding: .25rem .4rem; text-align: center; font-size: .82rem;
        font-weight: 600; transition: border-color .15s, background .15s; outline: none;
    }
    .score-input:focus { border-color: #2563eb; background: #eff6ff; }
    .score-input.dirty { border-color: #f59e0b; background: #fffbeb; }
    .score-high { color: #059669; } .score-mid { color: #d97706; } .score-low { color: #dc2626; }

    .avg-cell { font-weight: 700; text-align: center; min-width: 70px; }
    .grade-badge {
        display: inline-block; min-width: 22px; text-align: center;
        border-radius: 4px; font-size: .7rem; font-weight: 700; padding: .1rem .3rem; margin-left: .25rem;
    }
    .grade-A { background: #d1fae5; color: #065f46; }
    .grade-B { background: #dbeafe; color: #1e40af; }
    .grade-C { background: #fef3c7; color: #92400e; }
    .grade-D { background: #fee2e2; color: #991b1b; }
    .grade-E { background: #f3f4f6; color: #6b7280; }

    .komponen-header { text-align: center; min-width: 90px; }
    .komponen-bobot  { font-size: .65rem; font-weight: 400; color: #9ca3af; display: block; }

    /* ── Sticky save bar ─────────────────────────────────────── */
    .save-bar {
        position: sticky; bottom: 0; background: #fff;
        border-top: 2px solid #e5e7eb; padding: .75rem 1rem;
        display: flex; align-items: center; justify-content: space-between;
        gap: 1rem; flex-wrap: wrap; z-index: 10; margin: 0 -1.25rem -1.25rem;
        border-radius: 0 0 12px 12px;
    }
    .save-bar-info { font-size: .8rem; color: #6b7280; }
    .dirty-count { font-weight: 700; color: #d97706; }

    /* ── Empty/loading states ────────────────────────────────── */
    .grid-placeholder {
        text-align: center; padding: 3rem 1rem; color: #9ca3af;
    }
    .grid-placeholder-icon { font-size: 2.5rem; margin-bottom: .5rem; }

    /* ── Rekap tab ───────────────────────────────────────────── */
    .rekap-table th { text-align: center; font-size: .75rem; }
    .rekap-table td { text-align: center; }
    .rekap-table td:nth-child(1),
    .rekap-table td:nth-child(2) { text-align: left; }
</style>
@endsection

@section('header-actions')
<div class="action-buttons d-flex gap-2 align-items-center">
    <button class="btn btn-outline-primary" id="btnRekap">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M14 2H2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v1H1V4zm0 2h14v6a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6z"/></svg>
        Rekap Nilai
    </button>
</div>
@endsection

@section('content')

{{-- ── Selector card ─────────────────────────────────────── --}}
<div class="selector-card">
    <div class="selector-title">📌 Pilih Konteks Penilaian</div>
    <div class="form-row" style="grid-template-columns: 3fr 3fr 4fr 2fr auto; align-items: end;">
        <div>
            <label class="form-label">Semester</label>
            <select class="form-select" id="selSemester" style="width:100%;"></select>
        </div>
        <div>
            <label class="form-label">Kelas</label>
            <select class="form-select" id="selKelas" style="width:100%;"></select>
        </div>
        <div>
            <label class="form-label">Mata Pelajaran / Pengampu</label>
            <select class="form-select" id="selPengampu" style="width:100%;"></select>
            <!--<div class="form-text">Pilih semester & kelas dahulu untuk mempersempit pilihan.</div>-->
        </div>
        <div>
            <button class="btn btn-primary" id="btnLoadGrid" disabled>
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                Tampilkan
            </button>
        </div>
    </div>
</div>

{{-- ── Context banner (shown after grid loads) ─────────── --}}
<div class="context-bar" id="contextBar" style="display:none;">
    <div>
        <p class="context-bar-sub" id="ctxSub"></p>
        <h2 class="context-bar-title" id="ctxTitle"></h2>
    </div>
    <div class="context-pills" id="ctxPills"></div>
</div>

{{-- ── Tabs ──────────────────────────────────────────────── --}}
<div id="gridSection" style="display:none;">
    <div class="card" id="panelInput" style="border-radius: 0 12px 12px 12px;">
        <div class="card-body" style="padding-bottom:0;">

            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:.75rem; flex-wrap:wrap; gap:.5rem;">
                <div style="font-size:.8rem; color:#6b7280;">
                    Klik sel untuk mengedit. Tab/Enter untuk pindah antar sel. Perubahan ditandai 🟡.
                </div>
                <div style="display:flex; gap:.5rem; align-items:center;">
                    <input type="date" class="form-control form-control-sm" id="tanggalInput" style="width:150px;">
                    <button class="btn btn-sm btn-outline-secondary" id="btnFillDate" title="Isi tanggal hari ini">Hari Ini</button>
                    <button class="btn btn-sm btn-outline-danger" id="btnClearDirty" style="display:none;">Batalkan Perubahan</button>
                </div>
            </div>

            <div class="nilai-table-wrap" id="nilaiTableWrap">
                <div class="grid-placeholder" id="gridPlaceholder">
                    <div class="grid-placeholder-icon">📋</div>
                    <div style="font-weight:600; color:#6b7280;">Pilih pengampu di atas untuk memulai input nilai</div>
                </div>
                <table class="nilai-table" id="nilaiTable" style="display:none;">
                    <thead id="nilaiThead"></thead>
                    <tbody id="nilaiTbody"></tbody>
                    <tfoot id="nilaiTfoot"></tfoot>
                </table>
            </div>

            <div class="save-bar" id="saveBar" style="display:none;">
                <div class="save-bar-info">
                    <span id="dirtyInfo">Tidak ada perubahan.</span>
                </div>
                <div style="display:flex; gap:.5rem;">
                    <button class="btn btn-sm btn-outline-secondary" id="btnClearDirty2">Batalkan</button>
                    <button class="btn btn-primary" id="btnSave">
                        <svg width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854 5.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 11.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                        Simpan Nilai
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- REKAP MODAL (full semester) --}}
<div class="modal fade" id="rekapModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rekap Nilai Semester</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-row" style="grid-template-columns:1fr 1fr auto; margin-bottom:1rem; align-items:end;">
                    <div>
                        <label class="form-label">Semester</label>
                        <select class="form-select" id="rekapSemester" style="width:100%;"></select>
                    </div>
                    <div>
                        <label class="form-label">Kelas</label>
                        <select class="form-select" id="rekapKelas" style="width:100%;"></select>
                    </div>
                    <div>
                        <button class="btn btn-primary" id="btnLoadRekap">Tampilkan</button>
                    </div>
                </div>
                <div id="rekapModalContent" class="nilai-table-wrap"></div>
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

    const csrfToken     = $('meta[name="csrf-token"]').attr('content');
    const ACTIVE_SEM_ID = {{ $activeSemester?->id ?? 'null' }};
    const ACTIVE_SEM_NM = "{{ $activeSemester?->nama ?? '' }}";

    // Grid state
    let currentPengampuId = null;
    let currentKomponens  = [];  // [{id, nama, bobot}]
    let originalData      = {};  // {santri_id: {komponen_id: nilai}}
    let dirtyMap          = {};  // {santri_id: {komponen_id: newVal}}

    // ── Select2 factory ───────────────────────────────────────
    function s2(selector, url, placeholder, parent, minLen, extraData) {
        return $(selector).select2({
            theme: 'bootstrap-5',
            dropdownParent: parent ? $(parent) : undefined,
            placeholder, allowClear: true,
            ajax: {
                url, dataType: 'json', delay: 250,
                data: params => ({ q: params.term ?? '', ...(extraData ? extraData() : {}) }),
                processResults: d => ({ results: d.results }),
                cache: false,
            },
            minimumInputLength: minLen ?? 0,
        });
    }

    // Main selectors
    s2('#selSemester', '{{ route("nilai.search-semester") }}', 'Pilih semester...');
    s2('#selKelas',    '{{ route("nilai.search-kelas") }}',    'Pilih kelas...', null, 0,
        () => ({ semester_id: $('#selSemester').val() }));
    s2('#selPengampu', '{{ route("nilai.search-pengampu") }}', 'Pilih mata pelajaran...', null, 0,
        () => ({ semester_id: $('#selSemester').val(), kelas_id: $('#selKelas').val() }));

    // Rekap modal selectors
    s2('#rekapSemester', '{{ route("nilai.search-semester") }}', 'Pilih semester...', '#rekapModal');
    s2('#rekapKelas',    '{{ route("nilai.search-kelas") }}',    'Pilih kelas...',    '#rekapModal', 0,
        () => ({ semester_id: $('#rekapSemester').val() }));

    // Pre-select active semester
    if (ACTIVE_SEM_ID) {
        const opt = new Option(ACTIVE_SEM_NM + ' ★', ACTIVE_SEM_ID, true, true);
        $('#selSemester').append(opt).trigger('change');
        const opt2 = new Option(ACTIVE_SEM_NM + ' ★', ACTIVE_SEM_ID, true, true);
        $('#rekapSemester').append(opt2).trigger('change');
    }

    // Enable load button when pengampu selected
    $('#selPengampu').on('change', function () {
        $('#btnLoadGrid').prop('disabled', !$(this).val());
    });

    // ── Default date = today ───────────────────────────────────
    $('#tanggalInput').val(new Date().toISOString().split('T')[0]);
    $('#btnFillDate').on('click', () => $('#tanggalInput').val(new Date().toISOString().split('T')[0]));

    // ── Tab switching ──────────────────────────────────────────
    $('[data-tab]').on('click', function () {
        const tab = $(this).data('tab');
        $('[data-tab]').removeClass('active');
        $(this).addClass('active');
        $('#panelInput, #panelRekap').hide();
        tab === 'input' ? $('#panelInput').show() : loadRekapTab();
    });

    // ── Load grid ─────────────────────────────────────────────
    $('#btnLoadGrid').on('click', function () {
        const pengampuId = $('#selPengampu').val();
        if (!pengampuId) return;
        currentPengampuId = pengampuId;
        dirtyMap = {};
        showLoading();

        $.get('{{ route("nilai.grid") }}', { pengampu_id: pengampuId }, function (res) {
            hideLoading();
            if (!res.success) { showNotification('error', 'Gagal memuat data.'); return; }

            currentKomponens = res.komponen_list;
            buildContextBar(res.pengampu);
            buildGrid(res.rows, res.komponen_list);
            $('#gridSection').show();
            $('#panelInput').show();
            $('#panelRekap').hide();
            $('[data-tab="input"]').addClass('active');
            $('[data-tab="rekap"]').removeClass('active');
        }).fail(xhr => { hideLoading(); handleAjaxError(xhr); });
    });

    // ── Build context bar ──────────────────────────────────────
    function buildContextBar(ctx) {
        $('#ctxSub').text(ctx.semester + ' · ' + ctx.pengajar);
        $('#ctxTitle').text(ctx.mata_pelajaran);
        $('#ctxPills').html(`
            <span class="context-pill">🏫 ${ctx.kelas}</span>
            <span class="context-pill">📚 ${ctx.mata_pelajaran}</span>
        `);
        $('#contextBar').show();
    }

    // ── Build spreadsheet ─────────────────────────────────────
    function buildGrid(rows, komponens) {
        originalData = {};

        // ── THEAD ──────────────────────────────────────────────
        let thead = '<tr>';
        thead += '<th>NIS</th>';
        thead += '<th>Nama Santri</th>';
        komponens.forEach(k => {
            thead += `<th class="komponen-header">
                ${k.nama}
                <span class="komponen-bobot">bobot ${k.bobot ?? 1} | maks 100}</span>
            </th>`;
        });
        thead += '<th class="avg-cell" style="background:#f0f9ff; min-width:90px;">Rata-rata</th>';
        thead += '</tr>';
        $('#nilaiThead').html(thead);

        // ── TBODY ──────────────────────────────────────────────
        let tbody = '';
        rows.forEach(row => {
            originalData[row.santri_id] = {};
            tbody += `<tr data-santri="${row.santri_id}">`;
            tbody += `<td style="color:#9ca3af; font-size:.78rem;">${row.nis}</td>`;
            tbody += `<td><strong>${row.nama_lengkap}</strong></td>`;

            komponens.forEach(k => {
                const score  = row.scores[k.id];
                const val    = score?.nilai ?? '';
                const color  = val !== '' ? scoreColorClass(parseFloat(val)) : '';
                originalData[row.santri_id][k.id] = val;

                tbody += `<td style="text-align:center;">
                    <input type="number" class="score-input ${color}"
                        data-santri="${row.santri_id}"
                        data-komponen="${k.id}"
                        data-original="${val}"
                        value="${val}"
                        min="0" max="100"
                        step="0.5"
                        placeholder="—">
                </td>`;
            });

            // Avg cell
            const avgColor = row.rata_rata !== null ? scoreColorClass(row.rata_rata) : '';
            const grade    = row.grade ?? '';
            tbody += `<td class="avg-cell" id="avg-${row.santri_id}">
                ${row.rata_rata !== null
                    ? `<span class="${avgColor}">${row.rata_rata}</span><span class="grade-badge grade-${grade}">${grade}</span>`
                    : '<span class="text-muted">—</span>'}
            </td>`;
            tbody += '</tr>';
        });
        $('#nilaiTbody').html(tbody);

        // ── TFOOT (column averages) ────────────────────────────
        let tfoot = '<tr style="background:#f8faff; font-weight:700;">';
        tfoot += '<td colspan="2" style="text-align:right; color:#6b7280; font-size:.75rem;">Rata-rata kelas</td>';
        komponens.forEach(k => {
            const vals = rows.map(r => r.scores[k.id]?.nilai).filter(v => v !== null && v !== undefined);
            const avg  = vals.length ? (vals.reduce((a, b) => a + parseFloat(b), 0) / vals.length).toFixed(1) : '—';
            tfoot += `<td class="avg-cell" id="col-avg-${k.id}">${avg}</td>`;
        });
        tfoot += '<td></td></tr>';
        $('#nilaiTfoot').html(tfoot);

        $('#gridPlaceholder').hide();
        $('#nilaiTable').show();
        $('#saveBar').show();
        updateDirtyInfo();
    }

    // ── Score input events ─────────────────────────────────────
    $(document).on('input', '.score-input', function () {
        const santriId  = $(this).data('santri');
        const komponenId= $(this).data('komponen');
        const original  = $(this).data('original').toString();
        const newVal    = $(this).val();
        const max       = parseFloat($(this).attr('max')) || 100;

        // Clamp
        if (newVal !== '' && parseFloat(newVal) > max) {
            $(this).val(max);
        }

        if (!dirtyMap[santriId]) dirtyMap[santriId] = {};

        if (newVal.toString() !== original) {
            dirtyMap[santriId][komponenId] = newVal;
            $(this).addClass('dirty').removeClass('score-high score-mid score-low');
        } else {
            delete dirtyMap[santriId][komponenId];
            if (!Object.keys(dirtyMap[santriId]).length) delete dirtyMap[santriId];
            $(this).removeClass('dirty');
            if (newVal !== '') $(this).addClass(scoreColorClass(parseFloat(newVal)));
        }

        updateDirtyInfo();
        recalcRowAvg(santriId);
    });

    // Tab / Enter keyboard navigation between inputs
    $(document).on('keydown', '.score-input', function (e) {
        if (e.key === 'Tab' || e.key === 'Enter') {
            e.preventDefault();
            const inputs = $('.score-input').toArray();
            const idx    = inputs.indexOf(this);
            const next   = e.shiftKey ? inputs[idx - 1] : inputs[idx + 1];
            if (next) { $(next).focus().select(); }
        }
    });

    // ── Recalculate row average live ──────────────────────────
    function recalcRowAvg(santriId) {
        let totalBobot = 0, totalW = 0, hasAny = false;

        currentKomponens.forEach(k => {
            const $input = $(`.score-input[data-santri="${santriId}"][data-komponen="${k.id}"]`);
            const val = $input.val();
            if (val !== '') {
                hasAny = true;
                const bobot = k.bobot ?? 1;
                totalBobot += bobot;
                totalW     += parseFloat(val) * bobot;
            }
        });

        const avg = (hasAny && totalBobot > 0) ? (totalW / totalBobot).toFixed(2) : null;
        const grade = avg !== null ? letterGrade(parseFloat(avg)) : null;
        const color = avg !== null ? scoreColorClass(parseFloat(avg)) : '';

        $(`#avg-${santriId}`).html(
            avg !== null
                ? `<span class="${color}">${avg}</span><span class="grade-badge grade-${grade}">${grade}</span>`
                : '<span class="text-muted">—</span>'
        );
    }

    // ── Dirty info ────────────────────────────────────────────
    function updateDirtyInfo() {
        const count = Object.values(dirtyMap).reduce((a, b) => a + Object.keys(b).length, 0);
        if (count > 0) {
            $('#dirtyInfo').html(`<span class="dirty-count">${count} sel diubah</span> — belum disimpan.`);
            $('#btnClearDirty, #btnClearDirty2').show();
        } else {
            $('#dirtyInfo').text('Tidak ada perubahan.');
            $('#btnClearDirty, #btnClearDirty2').hide();
        }
    }

    // ── Clear dirty ───────────────────────────────────────────
    function clearDirty() {
        $('.score-input').each(function () {
            const orig = $(this).data('original').toString();
            $(this).val(orig).removeClass('dirty');
            if (orig !== '') $(this).addClass(scoreColorClass(parseFloat(orig)));
        });
        dirtyMap = {};
        updateDirtyInfo();
        // Re-recalc all averages
        const santriIds = [...new Set($('.score-input').map(function() { return $(this).data('santri'); }).toArray())];
        santriIds.forEach(recalcRowAvg);
    }

    $('#btnClearDirty, #btnClearDirty2').on('click', clearDirty);

    // ── Save ──────────────────────────────────────────────────
    $('#btnSave').on('click', function () {
        if (!currentPengampuId) return;

        // Collect ALL visible inputs (not just dirty) for a full upsert
        const rows = [];
        $('.score-input').each(function () {
            rows.push({
                santri_id:         $(this).data('santri'),
                komponen_nilai_id: $(this).data('komponen'),
                nilai:             $(this).val() !== '' ? $(this).val() : null,
                catatan:           null,
            });
        });

        const tanggal = $('#tanggalInput').val();
        if (!tanggal) { showNotification('error', 'Tanggal input harus diisi.'); return; }

        showLoading();
        $.ajax({
            url: '{{ route("nilai.batch-save") }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                _token:       csrfToken,
                pengampu_id:  currentPengampuId,
                tanggal_input: tanggal,
                rows,
            }),
            success: res => {
                hideLoading();
                // Update originals so dirty state clears
                $('.score-input').each(function () {
                    $(this).data('original', $(this).val()).removeClass('dirty');
                    const v = $(this).val();
                    if (v !== '') $(this).addClass(scoreColorClass(parseFloat(v)));
                });
                dirtyMap = {};
                updateDirtyInfo();
                showNotification('success', res.message);
            },
            error: xhr => { hideLoading(); handleAjaxError(xhr); },
        });
    });

    // ── Rekap tab ─────────────────────────────────────────────
    function loadRekapTab() {
        $('#panelRekap').show();
        const semId = $('#selSemester').val();
        const kelId = $('#selKelas').val();
        if (!semId || !kelId) {
            $('#rekapContent').html('<div class="grid-placeholder"><div class="grid-placeholder-icon">📊</div><div>Pilih semester dan kelas untuk melihat rekap.</div></div>');
            return;
        }
        showLoading();
        $.get('{{ route("nilai.rekap") }}', { semester_id: semId, kelas_id: kelId }, function (res) {
            hideLoading();
            renderRekapTable('#rekapContent', res);
        }).fail(xhr => { hideLoading(); handleAjaxError(xhr); });
    }

    // ── Rekap modal ────────────────────────────────────────────
    $('#btnRekap').on('click', () => $('#rekapModal').modal('show'));

    $('#btnLoadRekap').on('click', function () {
        const semId = $('#rekapSemester').val();
        const kelId = $('#rekapKelas').val();
        if (!semId || !kelId) { showNotification('error', 'Pilih semester dan kelas.'); return; }
        showLoading();
        $.get('{{ route("nilai.rekap") }}', { semester_id: semId, kelas_id: kelId }, function (res) {
            hideLoading();
            renderRekapTable('#rekapModalContent', res);
        }).fail(xhr => { hideLoading(); handleAjaxError(xhr); });
    });

    function renderRekapTable(target, res) {
        if (!res.success || !res.santri_list.length) {
            $(target).html('<div class="grid-placeholder"><div>Tidak ada data nilai.</div></div>');
            return;
        }

        let html = '<table class="nilai-table rekap-table" style="width:100%;">';
        html += '<thead><tr><th>NIS</th><th>Nama Santri</th>';
        res.pengampu_list.forEach(p => { html += `<th>${p.mata_pelajaran}</th>`; });
        html += '<th style="background:#f0f9ff;">Rata-rata</th></tr></thead><tbody>';

        res.santri_list.forEach(s => {
            html += `<tr><td style="color:#9ca3af; font-size:.78rem;">${s.nis}</td>`;
            html += `<td><strong>${s.nama_lengkap}</strong></td>`;
            res.pengampu_list.forEach(p => {
                const ms    = s.mapel_scores[p.id];
                const avg   = ms?.avg;
                const grade = ms?.grade;
                const color = avg !== null && avg !== undefined ? scoreColorClass(avg) : '';
                html += `<td>${avg !== null && avg !== undefined
                    ? `<span class="${color}">${avg}</span><span class="grade-badge grade-${grade}">${grade}</span>`
                    : '<span class="text-muted">—</span>'
                }</td>`;
            });
            const ga    = s.grand_avg;
            const gc    = ga !== null ? scoreColorClass(ga) : '';
            const gg    = ga !== null ? letterGrade(ga) : null;
            html += `<td class="avg-cell" style="background:#f0f9ff;">${ga !== null
                ? `<span class="${gc}">${ga}</span><span class="grade-badge grade-${gg}">${gg}</span>`
                : '<span class="text-muted">—</span>'
            }</td></tr>`;
        });

        html += '</tbody></table>';
        $(target).html(html);
    }

    // ── Helpers ───────────────────────────────────────────────
    function scoreColorClass(v) {
        return v >= 80 ? 'score-high' : v >= 65 ? 'score-mid' : 'score-low';
    }

    function letterGrade(v) {
        return v >= 90 ? 'A' : v >= 80 ? 'B' : v >= 70 ? 'C' : v >= 60 ? 'D' : 'E';
    }

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