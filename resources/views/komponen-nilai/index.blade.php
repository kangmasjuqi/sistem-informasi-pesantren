@extends('layouts.crud')

@section('page-title', 'Komponen Nilai')

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Komponen Nilai
    </button>
    <button class="btn btn-outline-primary" id="btnRefresh">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
        </svg>
        Refresh
    </button>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">

        {{-- Filters --}}
        <div class="filters-section">
            <h6 style="margin: 0 0 1rem 0; font-weight: 700; color: #374151;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -2px; margin-right: 0.5rem;">
                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                </svg>
                Filter Pencarian
            </h6>
            <div class="filters-grid">
                <div>
                    <label class="form-label">Kode</label>
                    <input type="text" class="form-control" id="filterKode" placeholder="Cari kode...">
                </div>
                <div>
                    <label class="form-label">Nama Komponen</label>
                    <input type="text" class="form-control" id="filterNama" placeholder="Cari nama...">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
                <div style="display: flex; align-items: end;">
                    <button class="btn btn-primary" id="btnApplyFilters" style="width: 100%;">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover" id="knTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Komponen</th>
                        <th>Bobot</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>

@include('komponen-nilai.modal')

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    let table;
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    initDataTable();

    // ── Toolbar buttons ─────────────────────────────────────────
    $('#btnCreate').on('click', function () {
        resetForm();
        $('#modalTitle').text('Tambah Komponen Nilai');
        $('#knModal').modal('show');
    });

    $('#btnRefresh').on('click', function () {
        table.ajax.reload();
        showNotification('success', 'Data berhasil direfresh');
    });

    $('#btnApplyFilters').on('click', function () {
        table.ajax.reload();
    });

    // ── Bobot: live progress bar update ─────────────────────────
    $('#bobot').on('input', function () {
        const val = Math.min(100, Math.max(0, parseInt($(this).val()) || 0));
        $('#bobotBar').css('width', val + '%');
        $('#bobotLabel').text(val + '%');

        // Colour-code the bar
        $('#bobotBar')
            .removeClass('bg-success bg-warning bg-danger')
            .addClass(val <= 40 ? 'bg-success' : val <= 70 ? 'bg-warning' : 'bg-danger');
    });

    // ── Form submit ──────────────────────────────────────────────
    $('#knForm').on('submit', function (e) {
        e.preventDefault();

        const id     = $('#knId').val();
        const url    = id ? `/komponen-nilai/${id}` : '/komponen-nilai';
        const method = id ? 'PUT' : 'POST';

        const formData = {
            _token:    csrfToken,
            _method:   method,
            kode:      $('#kode').val(),
            nama:      $('#nama').val(),
            bobot:     $('#bobot').val(),
            deskripsi: $('#deskripsi').val(),
            is_active: $('#is_active').val(),
        };

        showLoading();

        $.ajax({
            url,
            method: 'POST',
            data: formData,
            success: function (res) {
                hideLoading();
                $('#knModal').modal('hide');
                table.ajax.reload();
                showNotification('success', res.message);
            },
            error: function (xhr) {
                hideLoading();
                handleAjaxError(xhr);
            },
        });
    });

    // ── Edit ─────────────────────────────────────────────────────
    $(document).on('click', '.btn-edit', function () {
        editKomponenNilai($(this).data('id'));
    });

    // ── Delete ───────────────────────────────────────────────────
    $(document).on('click', '.btn-delete', function () {
        const id   = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            icon: 'warning',
            title: 'Hapus Komponen Nilai?',
            html: `Komponen <strong>${nama}</strong> akan dihapus.`,
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        }).then(result => {
            if (!result.isConfirmed) return;

            showLoading();
            $.ajax({
                url: `/komponen-nilai/${id}`,
                method: 'POST',
                data: { _token: csrfToken, _method: 'DELETE' },
                success: function (res) {
                    hideLoading();
                    table.ajax.reload();
                    showNotification('success', res.message);
                },
                error: function (xhr) {
                    hideLoading();
                    handleAjaxError(xhr);
                },
            });
        });
    });

    // ── Functions ────────────────────────────────────────────────
    function initDataTable() {
        table = $('#knTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/komponen-nilai/data',
                data: function (d) {
                    d.kode      = $('#filterKode').val();
                    d.nama      = $('#filterNama').val();
                    d.is_active = $('#filterStatus').val();
                },
            },
            columns: [
                { data: 'kode' },
                {
                    data: 'nama',
                    render: function (data) {
                        return `<strong>${data}</strong>`;
                    },
                },
                {
                    data: 'bobot',
                    className: 'text-center',
                    render: function (data) {
                        const color = data <= 40 ? '#10b981' : data <= 70 ? '#f59e0b' : '#ef4444';
                        return `
                            <div style="display:flex; align-items:center; gap:0.75rem; min-width:120px;">
                                <div style="flex:1; background:#e5e7eb; border-radius:99px; height:8px; overflow:hidden;">
                                    <div style="width:${data}%; height:100%; background:${color}; border-radius:99px; transition:width .3s;"></div>
                                </div>
                                <span style="font-weight:700; font-size:0.875rem; color:${color}; min-width:36px;">${data}%</span>
                            </div>`;
                    },
                },
                {
                    data: 'deskripsi',
                    render: function (data) {
                        if (!data) return '<span class="text-muted" style="font-size:0.8125rem;">—</span>';
                        return data.length > 60
                            ? `<span title="${data}">${data.substring(0, 60)}…</span>`
                            : data;
                    },
                },
                {
                    data: 'is_active',
                    render: function (data) {
                        return data
                            ? '<span class="status-badge status-aktif">AKTIF</span>'
                            : '<span class="status-badge status-tidak_aktif">TIDAK AKTIF</span>';
                    },
                },
                {
                    data: null,
                    orderable: false,
                    render: function (row) {
                        return `
                            <div style="display:flex; gap:0.25rem;">
                                <button class="btn btn-sm btn-primary btn-edit"
                                        data-id="${row.id}" title="Edit">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                    </svg>
                                </button>
                                <!--<button class="btn btn-sm btn-danger btn-delete"
                                        data-id="${row.id}" data-nama="${row.nama}" title="Hapus">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                    </svg>
                                </button>-->
                            </div>`;
                    },
                },
            ],
            order: [[1, 'asc']],
            pageLength: 10,
            language: {
                processing:   'Memuat data...',
                search:       'Cari:',
                lengthMenu:   'Tampilkan _MENU_ data',
                info:         'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty:    'Menampilkan 0 sampai 0 dari 0 data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords:  'Tidak ada data yang ditemukan',
                emptyTable:   'Tidak ada data tersedia',
                paginate: {
                    first:    'Pertama',
                    last:     'Terakhir',
                    next:     'Selanjutnya',
                    previous: 'Sebelumnya',
                },
            },
        });
    }

    function editKomponenNilai(id) {
        showLoading();
        $.ajax({
            url: `/komponen-nilai/${id}`,
            method: 'GET',
            success: function (res) {
                hideLoading();
                const d = res.data;
                $('#knId').val(d.id);
                $('#modalTitle').text('Edit Komponen Nilai');
                $('#kode').val(d.kode);
                $('#nama').val(d.nama);
                $('#bobot').val(d.bobot).trigger('input'); // sync progress bar
                $('#deskripsi').val(d.deskripsi);
                $('#is_active').val(d.is_active ? '1' : '0');
                $('#knModal').modal('show');
            },
            error: function (xhr) {
                hideLoading();
                handleAjaxError(xhr);
            },
        });
    }

    function resetForm() {
        $('#knForm')[0].reset();
        $('#knId').val('');
        $('#bobotBar').css('width', '0%').removeClass('bg-warning bg-danger').addClass('bg-success');
        $('#bobotLabel').text('0%');
    }

    function showLoading()  { $('#loadingOverlay').addClass('show'); }
    function hideLoading()  { $('#loadingOverlay').removeClass('show'); }

    function showNotification(type, message) {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : 'Perhatian!',
            text: message,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
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