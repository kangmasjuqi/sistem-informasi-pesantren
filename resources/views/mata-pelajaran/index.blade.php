@extends('layouts.crud')

@section('page-title', 'Manajemen Mata Pelajaran')

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