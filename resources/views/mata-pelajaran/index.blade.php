@extends('layouts.dashboard')

@section('page-title', 'Manajemen Mata Pelajaran')

@section('extra-css')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    :root {
        --primary-color: #1a3a2e;
        --primary-hover: #265443;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --border-color: #e5e7eb;
        --text-muted: #6b7280;
        --bg-light: #f9fafb;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        margin-bottom: 2rem;
    }

    .card-body {
        padding: 2rem;
    }

    .filters-section {
        background: var(--bg-light);
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-label .badge {
        font-size: 0.625rem;
        font-weight: 600;
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
        margin-left: 0.5rem;
    }

    .badge-required {
        background: #fee2e2;
        color: #dc2626;
    }

    .badge-optional {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .badge-info {
        background: #dbeafe;
        color: #2563eb;
    }

    .form-control, .form-select {
        border: 1.5px solid var(--border-color);
        border-radius: 8px;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(26, 58, 46, 0.1);
        outline: none;
    }

    .btn {
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        border: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .btn-outline-primary {
        background: transparent;
        color: var(--primary-color);
        border: 1.5px solid var(--primary-color);
    }

    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
    }

    table.dataTable thead th {
        background: var(--bg-light);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #374151;
        padding: 1rem;
        border-bottom: 2px solid var(--border-color);
    }

    table.dataTable tbody td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    table.dataTable tbody tr:hover {
        background: var(--bg-light);
    }

    .kategori-badge {
        padding: 0.25rem 0.625rem;
        border-radius: 4px;
        font-size: 0.6875rem;
        font-weight: 600;
    }

    .kategori-agama {
        background: #dcfce7;
        color: #166534;
    }

    .kategori-umum {
        background: #dbeafe;
        color: #1e40af;
    }

    .kategori-keterampilan {
        background: #fef3c7;
        color: #92400e;
    }

    .kategori-ekstrakurikuler {
        background: #e0e7ff;
        color: #4338ca;
    }

    .status-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .modal-content {
        border: none;
        border-radius: 16px;
    }

    .modal-header {
        background: linear-gradient(135deg, #1a3a2e 0%, #0f2419 100%);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 1.5rem 2rem;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-body {
        padding: 2rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-row.full {
        grid-template-columns: 1fr;
    }

    .form-text {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.375rem;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loading-overlay.show {
        display: flex;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Mata Pelajaran
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
        <!-- Filters -->
        <div class="filters-section">
            <h6 style="margin: 0 0 1rem 0; font-weight: 700; color: #374151;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -2px; margin-right: 0.5rem;">
                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                </svg>
                Filter Pencarian
            </h6>
            <div class="filters-grid">
                <div>
                    <label class="form-label">Kode Mapel</label>
                    <input type="text" class="form-control" id="filterKode" placeholder="Cari kode...">
                </div>
                <div>
                    <label class="form-label">Nama Mapel</label>
                    <input type="text" class="form-control" id="filterNama" placeholder="Cari nama...">
                </div>
                <div>
                    <label class="form-label">Kategori</label>
                    <select class="form-select" id="filterKategori">
                        <option value="">Semua Kategori</option>
                        <option value="agama">Agama</option>
                        <option value="umum">Umum</option>
                        <option value="keterampilan">Keterampilan</option>
                        <option value="ekstrakurikuler">Ekstrakurikuler</option>
                    </select>
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

        <!-- DataTable -->
        <div class="table-responsive">
            <table class="table table-hover" id="mapelTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Kategori</th>
                        <th>Bobot SKS</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('mata-pelajaran/modal')

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
$(document).ready(function() {
    let table;
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Initialize DataTable
    initDataTable();

    // Buttons
    $('#btnCreate').on('click', function() {
        resetForm();
        $('#modalTitle').text('Tambah Mata Pelajaran Baru');
        $('#mapelModal').modal('show');
    });

    $('#btnRefresh').on('click', function() {
        table.ajax.reload();
        showNotification('success', 'Data berhasil direfresh');
    });

    $('#btnApplyFilters').on('click', function() {
        table.ajax.reload();
    });

    // Form Submit
    $('#mapelForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#mapelId').val();
        const url = id ? `/pelajaran/${id}` : '/pelajaran';
        const method = id ? 'PUT' : 'POST';
        
        const formData = {
            _token: csrfToken,
            _method: method,
            kode_mapel: $('#kode_mapel').val(),
            nama_mapel: $('#nama_mapel').val(),
            kategori: $('#kategori').val(),
            bobot_sks: $('#bobot_sks').val(),
            deskripsi: $('#deskripsi').val(),
            is_active: $('#is_active').val()
        };

        showLoading();

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                $('#mapelModal').modal('hide');
                table.ajax.reload();
                showNotification('success', response.message);
            },
            error: function(xhr) {
                hideLoading();
                handleAjaxError(xhr);
            }
        });
    });

    // Edit
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        editMapel(id);
    });

    // Functions
    function initDataTable() {
        table = $('#mapelTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/pelajaran/data',
                data: function(d) {
                    d.kode_mapel = $('#filterKode').val();
                    d.nama_mapel = $('#filterNama').val();
                    d.kategori = $('#filterKategori').val();
                    d.is_active = $('#filterStatus').val();
                }
            },
            columns: [
                { data: 'kode_mapel' },
                { data: 'nama_mapel' },
                {
                    data: 'kategori',
                    render: function(data) {
                        const badges = {
                            'agama': 'kategori-agama',
                            'umum': 'kategori-umum',
                            'keterampilan': 'kategori-keterampilan',
                            'ekstrakurikuler': 'kategori-ekstrakurikuler'
                        };
                        const names = {
                            'agama': 'Agama',
                            'umum': 'Umum',
                            'keterampilan': 'Keterampilan',
                            'ekstrakurikuler': 'Ekstrakurikuler'
                        };
                        return `<span class="kategori-badge ${badges[data]}">${names[data]}</span>`;
                    }
                },
                {
                    data: 'bobot_sks',
                    render: function(data) {
                        return `<strong>${data}</strong> jam/minggu`;
                    }
                },
                {
                    data: 'is_active',
                    render: function(data) {
                        return data
                            ? '<span class="status-badge status-active">AKTIF</span>'
                            : '<span class="status-badge status-inactive">TIDAK AKTIF</span>';
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data) {
                        return `
                            <div style="display: flex; gap: 0.25rem;">
                                <button class="btn btn-sm btn-primary btn-edit" data-id="${data}" title="Edit">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                    </svg>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, 'asc']],
            pageLength: 10,
            language: {
                processing: "Memuat data...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang ditemukan",
                emptyTable: "Tidak ada data tersedia",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    }

    function editMapel(id) {
        showLoading();
        
        $.ajax({
            url: `/pelajaran/${id}`,
            method: 'GET',
            success: function(response) {
                hideLoading();
                const data = response.data;
                
                $('#mapelId').val(data.id);
                $('#modalTitle').text('Edit Mata Pelajaran');
                $('#kode_mapel').val(data.kode_mapel);
                $('#nama_mapel').val(data.nama_mapel);
                $('#kategori').val(data.kategori);
                $('#bobot_sks').val(data.bobot_sks);
                $('#deskripsi').val(data.deskripsi);
                $('#is_active').val(data.is_active ? '1' : '0');
                
                $('#mapelModal').modal('show');
            },
            error: function(xhr) {
                hideLoading();
                handleAjaxError(xhr);
            }
        });
    }

    function resetForm() {
        $('#mapelForm')[0].reset();
        $('#mapelId').val('');
    }

    function showLoading() {
        $('#loadingOverlay').addClass('show');
    }

    function hideLoading() {
        $('#loadingOverlay').removeClass('show');
    }

    function showNotification(type, message) {
        const icons = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        Swal.fire({
            icon: icons[type],
            title: type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : 'Perhatian!',
            text: message,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    function handleAjaxError(xhr) {
        let message = 'Terjadi kesalahan pada server';
        
        if (xhr.responseJSON) {
            if (xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            
            if (xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                message = Object.values(errors).flat().join('<br>');
            }
        }
        
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            html: message,
            confirmButtonColor: '#ef4444'
        });
    }
});
</script>
@endsection