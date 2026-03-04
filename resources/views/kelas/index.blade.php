@extends('layouts.crud')

@section('page-title', 'Manajemen Kelas')

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
        Tambah Kelas
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

        {{-- Filters --}}
        <div class="filters-section">
            <h6 style="margin:0 0 1rem 0; font-weight:700; color:#374151;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:-2px; margin-right:.5rem;"><path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>
                Filter Pencarian
            </h6>
            <div class="filters-grid">
                <div>
                    <label class="form-label">Tahun Ajaran</label>
                    <select class="form-select" id="filterTahunAjaran" style="width:100%;">
                        <option value="">Semua Tahun Ajaran</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tingkat</label>
                    <select class="form-select" id="filterTingkat">
                        <option value="">Semua Tingkat</option>
                        <option value="1">Tingkat 1</option>
                        <option value="2">Tingkat 2</option>
                        <option value="3">Tingkat 3</option>
                        <option value="Ibtidaiyah">Ibtidaiyah</option>
                        <option value="Tsanawiyah">Tsanawiyah</option>
                        <option value="Aliyah">Aliyah</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Nama Kelas</label>
                    <input type="text" class="form-control" id="filterNama" placeholder="Cari nama kelas...">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
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
            <table class="table table-hover" id="kelasTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>Tahun Ajaran</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Wali Kelas</th>
                        <th class="text-center">Kapasitas</th>
                        <th class="text-center">Terisi</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('kelas.modal')
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
    let table;
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    const statusMap = {
        true:  { label: 'Aktif',       cls: 'status-aktif' },
        false: { label: 'Tidak Aktif', cls: 'status-tidak_aktif' },
    };

    initDataTable();

    // ── Select2: tahun_ajaran_id (modal form) ─────────────────────
    $('#tahun_ajaran_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#kelasModal'),
        placeholder: 'Pilih tahun ajaran...',
        allowClear: true,
        ajax: {
            url: '{{ route("kelas.search-tahun-ajaran") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term ?? '' }),
            processResults: data => ({ results: data.results }),
            cache: true,
        },
        minimumInputLength: 0,
    });

    // ── Select2: wali_kelas_id (modal form) ───────────────────────
    $('#wali_kelas_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#kelasModal'),
        placeholder: 'Cari nama pengajar...',
        allowClear: true,
        ajax: {
            url: '{{ route("kelas.search-pengajar") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term ?? '' }),
            processResults: data => ({ results: data.results }),
            cache: true,
        },
        minimumInputLength: 2,
    });

    // ── Select2: filter Tahun Ajaran ──────────────────────────────
    $('#filterTahunAjaran').select2({
        theme: 'bootstrap-5',
        placeholder: 'Semua tahun ajaran...',
        allowClear: true,
        ajax: {
            url: '{{ route("kelas.search-tahun-ajaran") }}',
            dataType: 'json',
            delay: 300,
            data: params => ({ q: params.term ?? '' }),
            processResults: data => ({ results: data.results }),
            cache: true,
        },
        minimumInputLength: 0,
    });

    $('#btnCreate').on('click', function () {
        resetForm();
        $('#modalTitle').text('Tambah Kelas');
        $('#kelasModal').modal('show');
    });

    $('#btnRefresh').on('click', function () {
        table.ajax.reload();
        showNotification('success', 'Data berhasil direfresh');
    });

    $('#btnApplyFilters').on('click', function () { table.ajax.reload(); });

    $('#kelasForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#kelasId').val();
        showLoading();
        $.ajax({
            url: id ? `/kelas/${id}` : '/kelas',
            method: 'POST',
            data: {
                _token:          csrfToken,
                _method:         id ? 'PUT' : 'POST',
                tahun_ajaran_id: $('#tahun_ajaran_id').val(),
                wali_kelas_id:   $('#wali_kelas_id').val(),
                nama_kelas:      $('#nama_kelas').val(),
                tingkat:         $('#tingkat').val(),
                kapasitas:       $('#kapasitas').val(),
                deskripsi:       $('#deskripsi').val(),
                is_active:       $('#is_active').val(),
            },
            success: res => {
                hideLoading();
                $('#kelasModal').modal('hide');
                table.ajax.reload();
                showNotification('success', res.message);
            },
            error: xhr => { hideLoading(); handleAjaxError(xhr); },
        });
    });

    $(document).on('click', '.btn-edit',   function () { editKelas($(this).data('id')); });
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id'), nama = $(this).data('nama');
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Data Kelas?',
            html: `Kelas <strong>${nama}</strong> akan dihapus.<br><small class="text-muted">Kelas yang masih memiliki santri aktif tidak dapat dihapus.</small>`,
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            showLoading();
            $.ajax({
                url: `/kelas/${id}`,
                method: 'POST',
                data: { _token: csrfToken, _method: 'DELETE' },
                success: res => { hideLoading(); table.ajax.reload(); showNotification('success', res.message); },
                error:   xhr => { hideLoading(); handleAjaxError(xhr); },
            });
        });
    });

    function initDataTable() {
        table = $('#kelasTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/kelas/data',
                data: function (d) {
                    d.tahun_ajaran_id = $('#filterTahunAjaran').val();
                    d.tingkat         = $('#filterTingkat').val();
                    d.nama_kelas      = $('#filterNama').val();
                    d.is_active       = $('#filterStatus').val();
                },
            },
            columns: [
                {
                    data: 'tahun_ajaran_nama',
                    render: d => d
                        ? `<span class="text-muted" style="font-size:.85rem;">${d}</span>`
                        : '<span class="text-muted">—</span>',
                },
                {
                    data: 'nama_kelas',
                    render: (d, _, r) =>
                        `<strong>${d}</strong>` +
                        (r.deskripsi ? `<br><small class="text-muted">${r.deskripsi}</small>` : ''),
                },
                {
                    data: 'tingkat_label',
                    render: d => `<span class="kategori-badge kategori-bulanan">${d}</span>`,
                },
                {
                    data: 'wali_kelas_nama',
                    render: d => d ?? '<span class="text-muted">—</span>',
                },
                {
                    data: 'kapasitas',
                    className: 'text-center',
                    render: d => `<strong>${d}</strong>`,
                },
                {
                    data: null,
                    className: 'text-center',
                    render: function (r) {
                        const pct   = r.kapasitas > 0 ? Math.round((r.jumlah_santri / r.kapasitas) * 100) : 0;
                        const color = r.is_full ? '#ef4444' : pct >= 80 ? '#f59e0b' : '#10b981';
                        return `
                            <div style="min-width:90px;">
                                <strong style="color:${color};">${r.jumlah_santri}</strong>
                                <span class="text-muted">/ ${r.kapasitas}</span>
                                <div style="background:#e5e7eb;border-radius:999px;height:4px;margin-top:3px;">
                                    <div style="width:${Math.min(pct,100)}%;background:${color};height:4px;border-radius:999px;transition:width .3s;"></div>
                                </div>
                            </div>`;
                    },
                },
                {
                    data: 'is_active',
                    render: function (d) {
                        const s = statusMap[d] ?? { label: d ? 'Aktif' : 'Tidak Aktif', cls: 'badge-default' };
                        return `<span class="status-badge ${s.cls}">${s.label.toUpperCase()}</span>`;
                    },
                },
                {
                    data: null,
                    orderable: false,
                    render: function (row) {
                        return `<div style="display:flex; gap:.25rem;">
                            <button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}" title="Edit">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>
                            </button>
                            <!--<button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" data-nama="${row.nama_kelas}" title="Hapus">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
                            </button>-->
                        </div>`;
                    },
                },
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            language: {
                processing: 'Memuat data...', search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data', infoEmpty: '0 data',
                infoFiltered: '(difilter dari _MAX_ total data)', zeroRecords: 'Tidak ada data', emptyTable: 'Tidak ada data',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' },
            },
        });
    }

    function editKelas(id) {
        showLoading();
        $.ajax({
            url: `/kelas/${id}`,
            method: 'GET',
            success: function (res) {
                hideLoading();
                const d = res.data;
                $('#kelasId').val(d.id);
                $('#modalTitle').text('Edit Data Kelas');

                // Inject tahun ajaran option into Select2
                if (d.tahun_ajaran_id && d.tahun_ajaran_nama) {
                    const optTA = new Option(d.tahun_ajaran_nama, d.tahun_ajaran_id, true, true);
                    $('#tahun_ajaran_id').empty().append(optTA).trigger('change');
                }

                // Inject wali kelas option into Select2
                if (d.wali_kelas_id && d.wali_kelas_nama) {
                    const optWK = new Option(d.wali_kelas_nama, d.wali_kelas_id, true, true);
                    $('#wali_kelas_id').empty().append(optWK).trigger('change');
                } else {
                    $('#wali_kelas_id').val(null).trigger('change');
                }

                $('#nama_kelas').val(d.nama_kelas);
                $('#tingkat').val(d.tingkat);
                $('#kapasitas').val(d.kapasitas);
                $('#deskripsi').val(d.deskripsi);
                $('#is_active').val(d.is_active ? '1' : '0');

                $('#kelasModal').modal('show');
            },
            error: xhr => { hideLoading(); handleAjaxError(xhr); },
        });
    }

    function resetForm() {
        $('#kelasForm')[0].reset();
        $('#kelasId').val('');
        $('#tahun_ajaran_id').val(null).trigger('change');
        $('#wali_kelas_id').val(null).trigger('change');
        $('#kapasitas').val(30);
        $('#is_active').val('1');
    }

    function showLoading()  { $('#loadingOverlay').addClass('show'); }
    function hideLoading()  { $('#loadingOverlay').removeClass('show'); }

    function showNotification(type, message) {
        Swal.fire({
            icon: type, title: type === 'success' ? 'Berhasil!' : 'Gagal!', text: message,
            timer: 3000, timerProgressBar: true, showConfirmButton: false, toast: true, position: 'top-end',
        });
    }

    function handleAjaxError(xhr) {
        let message = 'Terjadi kesalahan pada server';
        if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
        if (xhr.responseJSON?.errors)  message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        Swal.fire({ icon: 'error', title: 'Error!', html: message, confirmButtonColor: '#ef4444' });
    }
});
</script>
@endsection