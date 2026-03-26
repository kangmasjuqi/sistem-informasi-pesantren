@extends('layouts.crud')

@section('page-title', 'Manajemen Pengampu')

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    /* ── Batch row ───────────────────────────────────────────── */
    .batch-row {
        display: grid;
        grid-template-columns: 2fr 2fr 1fr 1fr 36px;
        gap: .5rem;
        align-items: center;
        padding: .4rem .25rem;
        border-radius: 6px;
        transition: background .15s;
    }
    .batch-row:hover { background: #f9fafb; }
    .batch-row + .batch-row { border-top: 1px solid #f3f4f6; }

    .btn-remove-row {
        width: 32px; height: 32px; padding: 0;
        display: flex; align-items: center; justify-content: center;
        border-radius: 6px; flex-shrink: 0;
    }

    /* ── Grouping badge in table ─────────────────────────────── */
    .pengajar-cell { display: flex; align-items: center; gap: .5rem; }
    .pengajar-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: #4f46e5; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 700; flex-shrink: 0;
    }
</style>
@endsection

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
        Tambah Penugasan
    </button>
    <button class="btn btn-outline-primary" id="btnRefresh">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/></svg>
        Refresh
    </button>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">

        {{-- ── Filters ── --}}
        <div class="filters-section">
            <h6 style="margin:0 0 1rem 0; font-weight:700; color:#374151;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:-2px; margin-right:.5rem;"><path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>
                Filter Pencarian
            </h6>
            <div class="filters-grid" style="grid-template-columns: 4fr 2fr 2fr 2fr 2fr auto;">
                <div>
                    <label class="form-label">Semester</label>
                    <select class="form-select" id="filterSemester" style="width:100%;">
                        <option value="">Semua Semester</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Pengajar</label>
                    <select class="form-select" id="filterPengajar" style="width:100%;">
                        <option value="">Semua Pengajar</option>
                    </select>
                </div>
                <!--<div>
                    <label class="form-label">Kelas</label>
                    <select class="form-select" id="filterKelas" style="width:100%;">
                        <option value="">Semua Kelas</option>
                    </select>
                </div>-->
                <div>
                    <label class="form-label">Mata Pelajaran</label>
                    <select class="form-select" id="filterMapel" style="width:100%;">
                        <option value="">Semua Mapel</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua</option>
                    </select>
                </div>
                <div style="display:flex; align-items:end;">
                    <button class="btn btn-primary" id="btnApplyFilters" style="width:100%;">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                        Terapkan
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="pengampuTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>Pengajar</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Tgl Mulai</th>
                        <th>Status</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('pengampu.modal')
<div class="loading-overlay" id="loadingOverlay"><div class="spinner"></div></div>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {

    const csrfToken    = $('meta[name="csrf-token"]').attr('content');
    const STATUS_OPTIONS = @json($statusOptions);
    const ACTIVE_SEM_ID  = {{ $activeSemester?->id ?? 'null' }};
    const ACTIVE_SEM_NAME= "{{ $activeSemester?->nama ?? '' }}";

    let rowIndex = 0; // unique key per batch row

    // ── Build filter status dropdown ──────────────────────────
    Object.entries(STATUS_OPTIONS).forEach(([val, opt]) => {
        $('#filterStatus').append(new Option(opt.label, val));
    });

    // ── Shared Select2 factory ────────────────────────────────
    function makeSelect2(selector, url, placeholder, parent, minLen = 0) {
        return $(selector).select2({
            theme: 'bootstrap-5',
            dropdownParent: parent ? $(parent) : undefined,
            placeholder,
            allowClear: true,
            ajax: {
                url,
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term ?? '' }),
                processResults: data => ({ results: data.results }),
                cache: true,
            },
            minimumInputLength: minLen,
        });
    }

    // ── Filter bar Select2s ───────────────────────────────────
    makeSelect2('#filterSemester',  '{{ route("pengampu.search-semester") }}',       'Cari semester...');
    makeSelect2('#filterPengajar',  '{{ route("pengampu.search-pengajar") }}',       'Cari pengajar...',    null, 2);
    makeSelect2('#filterKelas',     '{{ route("pengampu.search-kelas") }}',          'Cari kelas...');
    makeSelect2('#filterMapel',     '{{ route("pengampu.search-mata-pelajaran") }}', 'Cari mata pelajaran...');

    // Pre-select active semester in filter
    if (ACTIVE_SEM_ID) {
        const opt = new Option(ACTIVE_SEM_NAME + ' ★', ACTIVE_SEM_ID, true, true);
        $('#filterSemester').append(opt).trigger('change');
    }

    // ── Batch modal Select2s ──────────────────────────────────
    makeSelect2('#batch_pengajar_id', '{{ route("pengampu.search-pengajar") }}',      'Ketik NIP atau nama...', '#batchModal', 2);
    makeSelect2('#batch_semester_id', '{{ route("pengampu.search-semester") }}',      'Pilih semester...',      '#batchModal');

    // Pre-select active semester in batch modal
    if (ACTIVE_SEM_ID) {
        const opt = new Option(ACTIVE_SEM_NAME + ' ★', ACTIVE_SEM_ID, true, true);
        $('#batch_semester_id').append(opt).trigger('change');
    }

    // ── Edit modal Select2s ───────────────────────────────────
    makeSelect2('#edit_pengajar_id',       '{{ route("pengampu.search-pengajar") }}',       'Cari pengajar...',       '#editModal', 2);
    makeSelect2('#edit_semester_id',       '{{ route("pengampu.search-semester") }}',       'Cari semester...',       '#editModal');
    makeSelect2('#edit_kelas_id',          '{{ route("pengampu.search-kelas") }}',          'Cari kelas...',          '#editModal');
    makeSelect2('#edit_mata_pelajaran_id', '{{ route("pengampu.search-mata-pelajaran") }}', 'Cari mata pelajaran...', '#editModal');

    // ── Batch row template ────────────────────────────────────
    function addBatchRow(kelasId = null, kelasText = null, mapelId = null, mapelText = null) {
        const idx   = rowIndex++;
        const today = new Date().toISOString().split('T')[0];

        const html = `
        <div class="batch-row" id="row-${idx}">
            <div>
                <select class="form-select form-select-sm row-kelas" data-idx="${idx}" style="width:100%;"></select>
            </div>
            <div>
                <select class="form-select form-select-sm row-mapel" data-idx="${idx}" style="width:100%;"></select>
            </div>
            <div>
                <input type="date" class="form-control form-control-sm row-tgl-mulai" value="${today}">
            </div>
            <div>
                <input type="date" class="form-control form-control-sm row-tgl-selesai" placeholder="Opsional">
            </div>
            <div>
                <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row" data-idx="${idx}" title="Hapus baris">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
                </button>
            </div>
        </div>`;

        $('#batchRows').append(html);

        // Init Select2 on the new row's selects
        const $row = $(`#row-${idx}`);

        $row.find('.row-kelas').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#batchModal'),
            placeholder: 'Pilih kelas...',
            allowClear: true,
            ajax: {
                url: '{{ route("pengampu.search-kelas") }}',
                dataType: 'json', delay: 250,
                data: params => ({ q: params.term ?? '' }),
                processResults: data => ({ results: data.results }),
                cache: true,
            },
            minimumInputLength: 0,
        });

        $row.find('.row-mapel').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#batchModal'),
            placeholder: 'Pilih mata pelajaran...',
            allowClear: true,
            ajax: {
                url: '{{ route("pengampu.search-mata-pelajaran") }}',
                dataType: 'json', delay: 250,
                data: params => ({ q: params.term ?? '' }),
                processResults: data => ({ results: data.results }),
                cache: true,
            },
            minimumInputLength: 0,
        });

        // Pre-fill if values given (e.g. copy-row)
        if (kelasId) {
            $row.find('.row-kelas').append(new Option(kelasText, kelasId, true, true)).trigger('change');
        }
        if (mapelId) {
            $row.find('.row-mapel').append(new Option(mapelText, mapelId, true, true)).trigger('change');
        }

        updateRowCounter();
    }

    function updateRowCounter() {
        const n = $('#batchRows .batch-row').length;
        $('#rowCounter').text(`(${n} baris)`);
        $('#btnBatchSubmit').prop('disabled', n === 0);
    }

    // ── Open batch modal ──────────────────────────────────────
    $('#btnCreate').on('click', function () {
        $('#batchForm')[0].reset();
        $('#batch_pengajar_id').val(null).trigger('change');
        $('#batchRows').empty();
        $('#batchWarning').hide();
        rowIndex = 0;
        // Restore active semester
        if (ACTIVE_SEM_ID) {
            $('#batch_semester_id').find(`option[value="${ACTIVE_SEM_ID}"]`).prop('selected', true).trigger('change');
        }
        addBatchRow(); // start with 1 empty row
        $('#batchModal').modal('show');
    });

    // Add row button
    $('#btnAddRow').on('click', () => addBatchRow());

    // Remove row
    $(document).on('click', '.btn-remove-row', function () {
        const idx = $(this).data('idx');
        $(`#row-${idx}`).remove();
        updateRowCounter();
    });

    // ── Submit batch ──────────────────────────────────────────
    $('#batchForm').on('submit', function (e) {
        e.preventDefault();
        $('#batchWarning').hide();

        const pengajarId = $('#batch_pengajar_id').val();
        const semesterId = $('#batch_semester_id').val();

        if (!pengajarId || !semesterId) {
            showNotification('error', 'Pengajar dan Semester wajib dipilih.');
            return;
        }

        // Collect rows
        const items = [];
        let hasError = false;

        $('#batchRows .batch-row').each(function () {
            const kelasId  = $(this).find('.row-kelas').val();
            const mapelId  = $(this).find('.row-mapel').val();
            const tglMulai = $(this).find('.row-tgl-mulai').val();
            const tglSelesai = $(this).find('.row-tgl-selesai').val();

            if (!kelasId || !mapelId || !tglMulai) {
                hasError = true;
                $(this).css('background', '#fef2f2');
            } else {
                $(this).css('background', '');
                items.push({
                    kelas_id:          kelasId,
                    mata_pelajaran_id: mapelId,
                    tanggal_mulai:     tglMulai,
                    tanggal_selesai:   tglSelesai || null,
                });
            }
        });

        if (hasError) {
            showNotification('error', 'Lengkapi semua baris yang ditandai merah.');
            return;
        }

        if (items.length === 0) {
            showNotification('error', 'Tambahkan minimal 1 baris penugasan.');
            return;
        }

        // Check client-side duplicates within the batch itself
        const keys = items.map(i => `${i.kelas_id}-${i.mata_pelajaran_id}`);
        const dupes = keys.filter((k, i) => keys.indexOf(k) !== i);
        if (dupes.length) {
            $('#batchWarning').html('⚠️ Terdapat duplikasi kelas × mata pelajaran dalam satu batch. Periksa kembali baris Anda.').show();
            return;
        }

        showLoading();
        $.ajax({
            url: '{{ route("pengampu.batch-store") }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                _token:      csrfToken,
                pengajar_id: pengajarId,
                semester_id: semesterId,
                keterangan:  $('#batch_keterangan').val(),
                items,
            }),
            success: res => {
                hideLoading();
                $('#batchModal').modal('hide');
                table.ajax.reload();
                showNotification('success', res.message);
            },
            error: xhr => { hideLoading(); handleAjaxError(xhr); },
        });
    });

    // ── DataTable ─────────────────────────────────────────────
    const table = $('#pengampuTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("pengampu.data") }}',
            data: d => {
                d.semester_id       = $('#filterSemester').val();
                d.pengajar_id       = $('#filterPengajar').val();
                d.kelas_id          = $('#filterKelas').val();
                d.mata_pelajaran_id = $('#filterMapel').val();
                d.status            = $('#filterStatus').val();
            },
        },
        columns: [
            {
                data: null,
                render: r => {
                    const ini = (r.pengajar_nama || '?').substring(0, 2).toUpperCase();
                    return `<div class="pengajar-cell">
                        <div class="pengajar-avatar">${ini}</div>
                        <div>
                            <strong>${r.pengajar_nama}</strong>
                            <br><small class="text-muted">${r.semester_nama ?? ''}</small>
                        </div>
                    </div>`;
                },
            },
            {
                data: 'mata_pelajaran_nama',
                render: d => `<strong>${d}</strong>`,
            },
            {
                data: 'kelas_nama',
                render: d => `<span class="kategori-badge kategori-bulanan">${d}</span>`,
            },
            {
                data: 'tanggal_mulai_fmt',
                render: (d, _, r) => {
                    let s = d ?? '—';
                    if (r.tanggal_selesai_fmt) s += `<br><small class="text-muted">s/d ${r.tanggal_selesai_fmt}</small>`;
                    return s;
                },
            },
            {
                data: 'status',
                render: d => {
                    const s = STATUS_OPTIONS[d] ?? { label: d, cls: 'badge-default' };
                    return `<span class="status-badge ${s.cls}">${s.label.toUpperCase()}</span>`;
                },
            },
            {
                data: null,
                orderable: false,
                render: row => `<div style="display:flex; gap:.25rem;">
                    <button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}" title="Edit">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>
                    </button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" data-nama="${row.pengajar_nama} – ${row.mata_pelajaran_nama}" title="Hapus">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
                    </button>
                </div>`,
            },
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        language: {
            processing: 'Memuat data...', search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data', infoEmpty: '0 data',
            infoFiltered: '(difilter dari _MAX_ total data)', zeroRecords: 'Tidak ada data', emptyTable: 'Tidak ada data',
            paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' },
        },
    });

    $('#btnApplyFilters').on('click', () => table.ajax.reload());
    $('#btnRefresh').on('click', () => { table.ajax.reload(); showNotification('success', 'Data berhasil direfresh'); });

    // ── Edit ──────────────────────────────────────────────────
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        showLoading();
        $.ajax({
            url: `/pengampu/${id}`, method: 'GET',
            success: function (res) {
                hideLoading();
                const d = res.data;
                $('#editId').val(d.id);

                // Inject Select2 values
                [
                    ['#edit_pengajar_id',       d.pengajar_id,       d.pengajar_nama],
                    ['#edit_semester_id',       d.semester_id,       d.semester_nama],
                    ['#edit_kelas_id',          d.kelas_id,          d.kelas_nama],
                    ['#edit_mata_pelajaran_id', d.mata_pelajaran_id, d.mata_pelajaran_nama],
                ].forEach(([sel, val, text]) => {
                    $(sel).empty().append(new Option(text, val, true, true)).trigger('change');
                });

                $('#edit_tanggal_mulai').val(d.tanggal_mulai);
                $('#edit_tanggal_selesai').val(d.tanggal_selesai ?? '');
                $('#edit_status').val(d.status);
                $('#edit_keterangan').val(d.keterangan ?? '');

                $('#editModal').modal('show');
            },
            error: xhr => { hideLoading(); handleAjaxError(xhr); },
        });
    });

    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editId').val();
        showLoading();
        $.ajax({
            url: `/pengampu/${id}`, method: 'POST',
            data: {
                _token:             csrfToken,
                _method:            'PUT',
                pengajar_id:        $('#edit_pengajar_id').val(),
                semester_id:        $('#edit_semester_id').val(),
                kelas_id:           $('#edit_kelas_id').val(),
                mata_pelajaran_id:  $('#edit_mata_pelajaran_id').val(),
                tanggal_mulai:      $('#edit_tanggal_mulai').val(),
                tanggal_selesai:    $('#edit_tanggal_selesai').val(),
                status:             $('#edit_status').val(),
                keterangan:         $('#edit_keterangan').val(),
            },
            success: res => { hideLoading(); $('#editModal').modal('hide'); table.ajax.reload(); showNotification('success', res.message); },
            error:   xhr => { hideLoading(); handleAjaxError(xhr); },
        });
    });

    // ── Delete ────────────────────────────────────────────────
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id'), nama = $(this).data('nama');
        Swal.fire({
            icon: 'warning', title: 'Hapus Penugasan?',
            html: `Penugasan <strong>${nama}</strong> akan dihapus.`,
            showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            showLoading();
            $.ajax({
                url: `/pengampu/${id}`, method: 'POST',
                data: { _token: csrfToken, _method: 'DELETE' },
                success: res => { hideLoading(); table.ajax.reload(); showNotification('success', res.message); },
                error:   xhr => { hideLoading(); handleAjaxError(xhr); },
            });
        });
    });

    // ── Helpers ───────────────────────────────────────────────
    function showLoading()  { $('#loadingOverlay').addClass('show'); }
    function hideLoading()  { $('#loadingOverlay').removeClass('show'); }

    function handleAjaxError(xhr) {
        let msg = 'Terjadi kesalahan pada server';
        if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
        if (xhr.responseJSON?.errors)  msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        Swal.fire({ icon: 'error', title: 'Error!', html: msg, confirmButtonColor: '#ef4444' });
    }
});
</script>
@endsection