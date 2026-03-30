@extends('layouts.crud')

@section('page-title', 'Jadwal Pelajaran')

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    /* ── Selector card ───────────────────────────────────────── */
    .selector-card {
        background: #f8faff; border: 1.5px solid #c7d2fe;
        border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;
    }
    .selector-title {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: #4f46e5; margin-bottom: .75rem;
    }

    /* ── Context bar ─────────────────────────────────────────── */
    .context-bar {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        border-radius: 12px; padding: 1.1rem 1.5rem; color: #fff;
        margin-bottom: 1.25rem; display: flex; align-items: center;
        justify-content: space-between; gap: 1rem; flex-wrap: wrap;
    }
    .context-bar-title { font-size: 1.1rem; font-weight: 800; margin: 0; }
    .context-bar-sub   { font-size: .8rem; opacity: .8; margin: 0 0 .2rem; }
    .context-pill {
        background: rgba(255,255,255,.18); border-radius: 999px;
        padding: .2rem .75rem; font-size: .75rem; font-weight: 600;
    }

    /* ── Timetable grid ──────────────────────────────────────── */
    .timetable-wrap { overflow-x: auto; }

    .timetable {
        display: grid;
        grid-template-columns: 80px repeat(var(--day-count, 6), 1fr);
        gap: 0;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        min-width: 700px;
    }

    .tt-header {
        background: #f8faff; padding: .6rem .5rem; font-weight: 700;
        font-size: .78rem; color: #374151; text-align: center;
        border-bottom: 2px solid #e5e7eb; border-right: 1px solid #e5e7eb;
    }
    .tt-header:last-child { border-right: none; }
    .tt-header.today { background: #eff6ff; color: #1d4ed8; }

    .tt-time-col {
        background: #fafafa; padding: .5rem .4rem;
        font-size: .7rem; color: #9ca3af; text-align: center;
        border-right: 1px solid #e5e7eb; border-bottom: 1px solid #f3f4f6;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .tt-time-jam-ke { font-weight: 700; font-size: .75rem; color: #6b7280; }

    .tt-cell {
        border-right: 1px solid #e5e7eb; border-bottom: 1px solid #f3f4f6;
        padding: .35rem; min-height: 72px; position: relative;
        transition: background .15s;
    }
    .tt-cell:last-child { border-right: none; }
    .tt-cell.empty:hover { background: #f0f9ff; cursor: pointer; }
    .tt-cell.empty:hover::after {
        content: '+'; position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; color: #93c5fd; font-weight: 300;
    }

    /* Slot card inside a cell */
    .slot-card {
        border-radius: 8px; padding: .4rem .55rem;
        font-size: .75rem; height: 100%; min-height: 62px;
        display: flex; flex-direction: column; gap: .2rem;
        border-left: 3px solid transparent;
        cursor: pointer; transition: opacity .15s, transform .1s;
    }
    .slot-card:hover { opacity: .85; transform: scale(1.02); }
    .slot-card.s-aktif  { background: #eff6ff; border-left-color: #2563eb; }
    .slot-card.s-libur  { background: #fef3c7; border-left-color: #f59e0b; opacity: .7; }
    .slot-card.s-diganti{ background: #fef2f2; border-left-color: #ef4444; }

    .slot-mapel   { font-weight: 700; color: #111827; line-height: 1.2; }
    .slot-pengajar{ color: #6b7280; font-size: .7rem; }
    .slot-time    { color: #9ca3af; font-size: .68rem; margin-top: auto; }
    .slot-room    { color: #7c3aed; font-size: .68rem; }

    .slot-actions {
        position: absolute; top: .3rem; right: .3rem;
        display: none; gap: .2rem;
    }
    .slot-card:hover .slot-actions { display: flex; }
    .slot-action-btn {
        width: 20px; height: 20px; border-radius: 4px; border: none;
        display: flex; align-items: center; justify-content: center;
        font-size: .6rem; cursor: pointer; padding: 0;
    }
    .slot-action-btn.edit   { background: #dbeafe; color: #1d4ed8; }
    .slot-action-btn.delete { background: #fee2e2; color: #dc2626; }

    /* ── Conflict badge ──────────────────────────────────────── */
    .conflict-alert {
        background: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px;
        padding: .6rem .9rem; margin-bottom: .75rem; font-size: .82rem; color: #991b1b;
        display: none;
    }

    /* ── Legend ──────────────────────────────────────────────── */
    .legend { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .legend-item { display: flex; align-items: center; gap: .4rem; font-size: .75rem; color: #6b7280; }
    .legend-dot  { width: 10px; height: 10px; border-radius: 2px; }
</style>
@endsection

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-outline-primary" id="btnListView">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/></svg>
        Tampilan List
    </button>
</div>
@endsection

@section('content')

{{-- ── Selector card ─────────────────────────────────────── --}}
<div class="selector-card">
    <div class="selector-title">📌 Pilih Semester & Kelas</div>
    <div class="form-row" style="grid-template-columns: 1fr 1fr auto; align-items: end;">
        <div>
            <label class="form-label">Semester</label>
            <select class="form-select" id="selSemester" style="width:100%;"></select>
        </div>
        <div>
            <label class="form-label">Kelas</label>
            <select class="form-select" id="selKelas" style="width:100%;"></select>
        </div>
        <div>
            <button class="btn btn-primary" id="btnLoadTimetable" disabled>
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                Tampilkan
            </button>
        </div>
    </div>
</div>

{{-- ── Timetable section ─────────────────────────────────── --}}
<div id="timetableSection" style="display:none;">

    <div class="context-bar">
        <div>
            <p class="context-bar-sub" id="ctxSemester"></p>
            <h2 class="context-bar-title" id="ctxKelas"></h2>
        </div>
        <div style="display:flex; gap:.5rem; align-items:center; flex-wrap:wrap;">
            <span class="context-pill" id="ctxWali"></span>
            <span class="context-pill" id="ctxSlots"></span>
            <button class="btn btn-sm" style="background:rgba(255,255,255,.2); color:#fff; border-color:rgba(255,255,255,.3);" id="btnAddSlot">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
                Tambah Slot
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="legend">
                <div class="legend-item"><div class="legend-dot" style="background:#eff6ff; border:2px solid #2563eb;"></div> Aktif</div>
                <div class="legend-item"><div class="legend-dot" style="background:#fef3c7; border:2px solid #f59e0b;"></div> Libur</div>
                <div class="legend-item"><div class="legend-dot" style="background:#fef2f2; border:2px solid #ef4444;"></div> Diganti</div>
                <div class="legend-item" style="margin-left:auto; color:#9ca3af;">Klik sel kosong untuk menambah · Klik kartu untuk edit</div>
            </div>

            <div class="timetable-wrap">
                <div class="timetable" id="timetableGrid"></div>
            </div>

        </div>
    </div>
</div>

{{-- ── Placeholder ───────────────────────────────────────── --}}
<div id="timetablePlaceholder" class="card">
    <div class="card-body" style="text-align:center; padding:3rem; color:#9ca3af;">
        <div style="font-size:2.5rem; margin-bottom:.5rem;">🗓️</div>
        <div style="font-weight:600; color:#6b7280;">Pilih semester dan kelas untuk melihat jadwal</div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL: Add / Edit Slot
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="slotModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="slotModalTitle">Tambah Slot Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="slotForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="slotId">

                    <div class="conflict-alert" id="conflictAlert"></div>

                    {{-- Mata pelajaran / pengampu ──────────────── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Mata Pelajaran
                        </div>
                        <label class="form-label">Pengampu (Mapel × Pengajar) <span class="badge badge-required">WAJIB</span></label>
                        <select class="form-select" id="slot_pengampu_id" name="pengampu_id" style="width:100%;" required></select>
                        <div class="form-text">Daftar mapel yang tersedia untuk kelas dan semester yang dipilih.</div>
                    </div>

                    {{-- Waktu ───────────────────────────────────── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Waktu
                        </div>
                        <div class="form-row" style="grid-template-columns: 1fr 1fr 1fr 1fr;">
                            <div>
                                <label class="form-label">Hari <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="slot_hari" name="hari" required>
                                    <option value="">Pilih Hari</option>
                                    @foreach($hariOptions as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Jam Ke-</label>
                                <input type="number" class="form-control" id="slot_jam_ke" name="jam_ke" min="1" max="20" placeholder="1, 2, 3...">
                            </div>
                            <div>
                                <label class="form-label">Jam Mulai <span class="badge badge-required">WAJIB</span></label>
                                <input type="time" class="form-control" id="slot_jam_mulai" name="jam_mulai" required>
                            </div>
                            <div>
                                <label class="form-label">Jam Selesai <span class="badge badge-required">WAJIB</span></label>
                                <input type="time" class="form-control" id="slot_jam_selesai" name="jam_selesai" required>
                            </div>
                        </div>
                    </div>

                    {{-- Detail ──────────────────────────────────── --}}
                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Detail
                        </div>
                        <div class="form-row" style="grid-template-columns: 1fr 1fr;">
                            <div>
                                <label class="form-label">Ruangan</label>
                                <input type="text" class="form-control" id="slot_ruangan" name="ruangan" placeholder="Contoh: Lab A, Ruang 101...">
                            </div>
                            <div>
                                <label class="form-label">Status</label>
                                <select class="form-select" id="slot_status" name="status">
                                    @foreach($statusOptions as $val => $opt)
                                        <option value="{{ $val }}">{{ $opt['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row full" style="margin-top:.5rem;">
                            <div>
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" id="slot_keterangan" name="keterangan" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854 5.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 11.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
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
    const HARI_OPTIONS  = @json($hariOptions);

    let currentSemesterId = null;
    let currentKelasId    = null;
    let pengampuOptions   = [];  // cached from timetable response

    // ── Select2 factory ───────────────────────────────────────
    function s2(sel, url, placeholder, parent, extraData) {
        return $(sel).select2({
            theme: 'bootstrap-5',
            dropdownParent: parent ? $(parent) : undefined,
            placeholder, allowClear: true,
            ajax: {
                url, dataType: 'json', delay: 250,
                data: p => ({ q: p.term ?? '', ...(extraData ? extraData() : {}) }),
                processResults: d => ({ results: d.results }),
                cache: false,
            },
            minimumInputLength: 0,
        });
    }

    s2('#selSemester', '{{ route("jadwal-pelajaran.search-semester") }}', 'Pilih semester...');
    s2('#selKelas',    '{{ route("jadwal-pelajaran.search-kelas") }}',    'Pilih kelas...', null,
        () => ({ semester_id: $('#selSemester').val() }));

    // Pre-select active semester
    if (ACTIVE_SEM_ID) {
        $('#selSemester').append(new Option(ACTIVE_SEM_NM + ' ★', ACTIVE_SEM_ID, true, true)).trigger('change');
    }

    $('#selKelas').on('change', function () {
        $('#btnLoadTimetable').prop('disabled', !$(this).val());
    });

    // ── Load timetable ─────────────────────────────────────────
    $('#btnLoadTimetable').on('click', loadTimetable);

    function loadTimetable() {
        currentSemesterId = $('#selSemester').val();
        currentKelasId    = $('#selKelas').val();
        if (!currentSemesterId || !currentKelasId) return;

        showLoading();
        $.get('{{ route("jadwal-pelajaran.timetable") }}', {
            semester_id: currentSemesterId,
            kelas_id:    currentKelasId,
        }, function (res) {
            hideLoading();
            if (!res.success) { showNotification('error', 'Gagal memuat jadwal.'); return; }

            pengampuOptions = res.pengampu_options;

            // Context bar
            $('#ctxSemester').text(res.semester.nama);
            $('#ctxKelas').text('Kelas ' + res.kelas.nama_kelas + ' — Tingkat ' + res.kelas.tingkat);
            $('#ctxWali').text(res.kelas.wali_kelas ? '👤 ' + res.kelas.wali_kelas : '');
            $('#ctxSlots').text(res.total_slots + ' slot jadwal');

            buildTimetable(res.timetable);
            $('#timetablePlaceholder').hide();
            $('#timetableSection').show();
        }).fail(xhr => { hideLoading(); handleAjaxError(xhr); });
    }

    // ── Build timetable grid ───────────────────────────────────
    function buildTimetable(timetable) {
        const days = Object.keys(timetable);
        const $grid = $('#timetableGrid');
        $grid.css('--day-count', days.length).empty();

        // Headers
        $grid.append('<div class="tt-header" style="font-size:.7rem; color:#9ca3af;">Jam</div>');
        days.forEach(hari => {
            $grid.append(`<div class="tt-header">${HARI_OPTIONS[hari]}</div>`);
        });

        // Collect all unique time slots across all days, sorted
        const allTimes = new Set();
        days.forEach(hari => {
            timetable[hari].forEach(slot => {
                allTimes.add(slot.jam_mulai + '|' + slot.jam_selesai + '|' + (slot.jam_ke ?? ''));
            });
        });

        if (allTimes.size === 0) {
            // No slots yet — show one empty row per day with add CTA
            $grid.append(`<div class="tt-time-col" style="font-size:.7rem; color:#d1d5db;">—</div>`);
            days.forEach(hari => {
                $grid.append(`
                    <div class="tt-cell empty"
                        data-hari="${hari}"
                        data-jam-mulai="07:00" data-jam-selesai="08:00"
                        title="Klik untuk tambah jadwal ${HARI_OPTIONS[hari]}">
                    </div>`);
            });
            return;
        }

        // Sort time slots
        const sortedTimes = [...allTimes].sort((a, b) => a.localeCompare(b));

        sortedTimes.forEach(timeKey => {
            const [jamMulai, jamSelesai, jamKe] = timeKey.split('|');

            // Time label cell
            $grid.append(`
                <div class="tt-time-col">
                    ${jamKe ? `<span class="tt-time-jam-ke">${jamKe}</span>` : ''}
                    <span>${jamMulai}</span>
                    <span style="font-size:.65rem; color:#d1d5db;">↓</span>
                    <span>${jamSelesai}</span>
                </div>`);

            days.forEach(hari => {
                const slot = timetable[hari].find(
                    s => s.jam_mulai === jamMulai && s.jam_selesai === jamSelesai
                );

                if (slot) {
                    $grid.append(buildSlotCell(slot, hari));
                } else {
                    $grid.append(`
                        <div class="tt-cell empty"
                            data-hari="${hari}"
                            data-jam-mulai="${jamMulai}"
                            data-jam-selesai="${jamSelesai}"
                            data-jam-ke="${jamKe}"
                            title="Tambah jadwal ${HARI_OPTIONS[hari]} ${jamMulai}">
                        </div>`);
                }
            });
        });

        // One extra empty row at bottom for adding new time slots
        $grid.append(`<div class="tt-time-col" style="color:#d1d5db; font-size:.7rem;">+ baru</div>`);
        days.forEach(hari => {
            $grid.append(`
                <div class="tt-cell empty"
                    data-hari="${hari}"
                    data-jam-mulai="" data-jam-selesai=""
                    title="Tambah slot baru ${HARI_OPTIONS[hari]}">
                </div>`);
        });
    }

    function buildSlotCell(slot, hari) {
        const roomBadge = slot.ruangan
            ? `<span class="slot-room">📍 ${slot.ruangan}</span>`
            : '';

        return `
        <div class="tt-cell">
            <div class="slot-card s-${slot.status}" data-id="${slot.id}">
                <div class="slot-mapel">${slot.nama_mapel}</div>
                <div class="slot-pengajar">${slot.pengajar_nama}</div>
                ${roomBadge}
                <div class="slot-time">${slot.waktu_label}</div>
                <div class="slot-actions">
                    <button class="slot-action-btn edit btn-slot-edit" data-id="${slot.id}" title="Edit">✏️</button>
                    <button class="slot-action-btn delete btn-slot-delete" data-id="${slot.id}" data-nama="${slot.nama_mapel}" title="Hapus">🗑️</button>
                </div>
            </div>
        </div>`;
    }

    // ── Slot modal helpers ─────────────────────────────────────

    function initPengampuSelect(selectedId, selectedText) {
        const $sel = $('#slot_pengampu_id');

        // Only destroy if Select2 is already initialised on this element
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }

        $sel.empty();

        const data = pengampuOptions.map(p => ({ id: p.id, text: p.text }));

        $sel.select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#slotModal'),
            placeholder: 'Pilih mata pelajaran...',
            allowClear: true,
            data: [{ id: '', text: '' }, ...data], // empty option required for allowClear + placeholder
        });

        if (selectedId) {
            $sel.val(selectedId).trigger('change');
        } else {
            $sel.val(null).trigger('change');
        }
    }

    function openAddModal(hari, jamMulai, jamSelesai, jamKe) {
        $('#slotId').val('');
        $('#slotModalTitle').text('Tambah Slot Jadwal');
        $('#conflictAlert').hide();
        $('#slotForm')[0].reset();
        initPengampuSelect(null, null);

        if (hari)       $('#slot_hari').val(hari);
        if (jamMulai)   $('#slot_jam_mulai').val(jamMulai);
        if (jamSelesai) $('#slot_jam_selesai').val(jamSelesai);
        if (jamKe)      $('#slot_jam_ke').val(jamKe);
        $('#slot_status').val('aktif');

        $('#slotModal').modal('show');
    }

    // ── Click empty cell → open add modal pre-filled ──────────
    $(document).on('click', '.tt-cell.empty', function () {
        if (!pengampuOptions.length) {
            showNotification('error', 'Tidak ada pengampu tersedia untuk kelas ini.');
            return;
        }
        openAddModal(
            $(this).data('hari'),
            $(this).data('jam-mulai'),
            $(this).data('jam-selesai'),
            $(this).data('jam-ke')
        );
    });

    // ── Header button → open add modal blank ──────────────────
    $('#btnAddSlot').on('click', () => openAddModal(null, null, null, null));

    // ── Edit slot ──────────────────────────────────────────────
    $(document).on('click', '.btn-slot-edit', function (e) {
        e.stopPropagation();
        const id = $(this).data('id');
        showLoading();
        $.get(`/jadwal-pelajaran/${id}`, function (res) {
            hideLoading();
            const d = res.data;
            $('#slotId').val(d.id);
            $('#slotModalTitle').text('Edit Slot Jadwal');
            $('#conflictAlert').hide();
            initPengampuSelect(d.pengampu_id, d.pengampu_text);
            $('#slot_hari').val(d.hari);
            $('#slot_jam_ke').val(d.jam_ke ?? '');
            $('#slot_jam_mulai').val(d.jam_mulai);
            $('#slot_jam_selesai').val(d.jam_selesai);
            $('#slot_ruangan').val(d.ruangan ?? '');
            $('#slot_status').val(d.status);
            $('#slot_keterangan').val(d.keterangan ?? '');
            $('#slotModal').modal('show');
        }).fail(xhr => { hideLoading(); handleAjaxError(xhr); });
    });

    // ── Delete slot ────────────────────────────────────────────
    $(document).on('click', '.btn-slot-delete', function (e) {
        e.stopPropagation();
        const id   = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            icon: 'warning', title: 'Hapus Slot Jadwal?',
            html: `Jadwal <strong>${nama}</strong> akan dihapus.`,
            showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            showLoading();
            $.ajax({
                url: `/jadwal-pelajaran/${id}`, method: 'POST',
                data: { _token: csrfToken, _method: 'DELETE' },
                success: res => { hideLoading(); showNotification('success', res.message); loadTimetable(); },
                error:   xhr => { hideLoading(); handleAjaxError(xhr); },
            });
        });
    });

    // ── Submit form ────────────────────────────────────────────
    $('#slotForm').on('submit', function (e) {
        e.preventDefault();
        $('#conflictAlert').hide();
        const id = $('#slotId').val();
        showLoading();

        $.ajax({
            url: id ? `/jadwal-pelajaran/${id}` : '/jadwal-pelajaran',
            method: 'POST',
            data: {
                _token:       csrfToken,
                _method:      id ? 'PUT' : 'POST',
                pengampu_id:  $('#slot_pengampu_id').val(),
                hari:         $('#slot_hari').val(),
                jam_ke:       $('#slot_jam_ke').val(),
                jam_mulai:    $('#slot_jam_mulai').val(),
                jam_selesai:  $('#slot_jam_selesai').val(),
                ruangan:      $('#slot_ruangan').val(),
                status:       $('#slot_status').val(),
                keterangan:   $('#slot_keterangan').val(),
            },
            success: res => {
                hideLoading();
                $('#slotModal').modal('hide');
                loadTimetable();
                showNotification('success', res.message);
            },
            error: xhr => {
                hideLoading();
                if (xhr.status === 422 && xhr.responseJSON?.conflicts) {
                    const msgs = xhr.responseJSON.conflicts.map(c => `• ${c}`).join('<br>');
                    $('#conflictAlert').html(`⚠️ <strong>Konflik Jadwal:</strong><br>${msgs}`).show();
                } else {
                    handleAjaxError(xhr);
                }
            },
        });
    });

    // ── List view button ───────────────────────────────────────
    $('#btnListView').on('click', function () {
        if (!currentSemesterId || !currentKelasId) {
            showNotification('error', 'Pilih semester dan kelas terlebih dahulu.');
            return;
        }
        // Open list in new tab or redirect
        window.open(`/jadwal-pelajaran/list?semester_id=${currentSemesterId}&kelas_id=${currentKelasId}`, '_blank');
    });

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