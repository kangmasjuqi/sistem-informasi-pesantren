@extends('layouts.crud')

@section('page-title', 'Manajemen Gedung')

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Gedung
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
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:-2px; margin-right:.5rem;">
                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                </svg>
                Filter Pencarian
            </h6>
            <div class="filters-grid">
                <div>
                    <label class="form-label">Kode Gedung</label>
                    <input type="text" class="form-control" id="filterKode" placeholder="Cari kode...">
                </div>
                <div>
                    <label class="form-label">Nama Gedung</label>
                    <input type="text" class="form-control" id="filterNama" placeholder="Cari nama...">
                </div>
                <div>
                    <label class="form-label">Jenis</label>
                    <select class="form-select" id="filterJenis">
                        <option value="">Semua Jenis</option>
                        <option value="asrama_putra">Asrama Putra</option>
                        <option value="asrama_putri">Asrama Putri</option>
                        <option value="kelas">Kelas</option>
                        <option value="serbaguna">Serbaguna</option>
                        <option value="masjid">Masjid</option>
                        <option value="kantor">Kantor</option>
                        <option value="perpustakaan">Perpustakaan</option>
                        <option value="lab">Laboratorium</option>
                        <option value="dapur">Dapur</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Kondisi</label>
                    <select class="form-select" id="filterKondisi">
                        <option value="">Semua Kondisi</option>
                        <option value="baik">Baik</option>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
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
                <div style="display:flex; align-items:end;">
                    <button class="btn btn-primary" id="btnApplyFilters" style="width:100%;">
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
            <table class="table table-hover" id="gedungTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Gedung</th>
                        <th>Jenis</th>
                        <th>Lantai</th>
                        <th>Kapasitas</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>

@include('gedung.modal')

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

    // Jenis & kondisi maps (mirrors PHP)
    const jenisMap = {
        asrama_putra: { label: 'Asrama Putra', icon: '🏠' },
        asrama_putri: { label: 'Asrama Putri', icon: '🏠' },
        kelas:        { label: 'Kelas',         icon: '🏫' },
        serbaguna:    { label: 'Serbaguna',     icon: '🏟️' },
        masjid:       { label: 'Masjid',        icon: '🕌' },
        kantor:       { label: 'Kantor',        icon: '🏢' },
        perpustakaan: { label: 'Perpustakaan',  icon: '📚' },
        lab:          { label: 'Laboratorium',  icon: '🔬' },
        dapur:        { label: 'Dapur',         icon: '🍳' },
        lainnya:      { label: 'Lainnya',       icon: '🏗️' },
    };

    const kondisiMap = {
        baik:         { label: 'Baik',         cls: 'status-aktif' },
        rusak_ringan: { label: 'Rusak Ringan', cls: 'status-cicilan' },
        rusak_berat:  { label: 'Rusak Berat',  cls: 'status-tidak_aktif' },
    };

    initDataTable();

    // ── Toolbar ──────────────────────────────────────────────────
    $('#btnCreate').on('click', function () {
        resetForm();
        $('#modalTitle').text('Tambah Gedung Baru');
        $('#gedungModal').modal('show');
    });

    $('#btnRefresh').on('click', function () {
        table.ajax.reload();
        showNotification('success', 'Data berhasil direfresh');
    });

    $('#btnApplyFilters').on('click', function () {
        table.ajax.reload();
    });

    // ── Form submit ──────────────────────────────────────────────
    $('#gedungForm').on('submit', function (e) {
        e.preventDefault();

        const id     = $('#gedungId').val();
        const url    = id ? `/gedung/${id}` : '/gedung';
        const method = id ? 'PUT' : 'POST';

        showLoading();

        $.ajax({
            url,
            method: 'POST',
            data: {
                _token:          csrfToken,
                _method:         method,
                kode_gedung:     $('#kode_gedung').val(),
                nama_gedung:     $('#nama_gedung').val(),
                jenis_gedung:    $('#jenis_gedung').val(),
                jumlah_lantai:   $('#jumlah_lantai').val(),
                kapasitas_total: $('#kapasitas_total').val(),
                alamat_lokasi:   $('#alamat_lokasi').val(),
                tahun_dibangun:  $('#tahun_dibangun').val(),
                kondisi:         $('#kondisi').val(),
                fasilitas:       $('#fasilitas').val(),
                keterangan:      $('#keterangan').val(),
                is_active:       $('#is_active').val(),
            },
            success: function (res) {
                hideLoading();
                $('#gedungModal').modal('hide');
                table.ajax.reload();
                showNotification('success', res.message);
            },
            error: function (xhr) {
                hideLoading();
                handleAjaxError(xhr);
            },
        });
    });

    // ── Edit & Delete ────────────────────────────────────────────
    $(document).on('click', '.btn-edit',   function () { editGedung($(this).data('id')); });
    $(document).on('click', '.btn-delete', function () {
        const id   = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            icon: 'warning',
            title: 'Hapus Gedung?',
            html: `Gedung <strong>${nama}</strong> akan dihapus.`,
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        }).then(result => {
            if (!result.isConfirmed) return;
            showLoading();
            $.ajax({
                url: `/gedung/${id}`,
                method: 'POST',
                data: { _token: csrfToken, _method: 'DELETE' },
                success: function (res) { hideLoading(); table.ajax.reload(); showNotification('success', res.message); },
                error:   function (xhr) { hideLoading(); handleAjaxError(xhr); },
            });
        });
    });

    // ── Functions ────────────────────────────────────────────────
    function initDataTable() {
        table = $('#gedungTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/gedung/data',
                data: function (d) {
                    d.kode_gedung  = $('#filterKode').val();
                    d.nama_gedung  = $('#filterNama').val();
                    d.jenis_gedung = $('#filterJenis').val();
                    d.kondisi      = $('#filterKondisi').val();
                    d.is_active    = $('#filterStatus').val();
                },
            },
            columns: [
                { data: 'kode_gedung' },
                {
                    data: 'nama_gedung',
                    render: (data, _, row) =>
                        `<div><strong>${data}</strong>${row.alamat_lokasi ? `<br><small class="text-muted">${row.alamat_lokasi.substring(0,50)}${row.alamat_lokasi.length>50?'…':''}</small>` : ''}</div>`,
                },
                {
                    data: 'jenis_gedung',
                    render: function (data) {
                        const j = jenisMap[data] ?? { label: data, icon: '🏗️' };
                        return `<span class="kategori-badge kategori-lainnya">${j.icon} ${j.label}</span>`;
                    },
                },
                { data: 'jumlah_lantai', className: 'text-center', render: d => `<strong>${d}</strong> lt` },
                {
                    data: 'kapasitas_total',
                    className: 'text-center',
                    render: d => d ? `<strong>${d}</strong> org` : '<span class="text-muted">—</span>',
                },
                {
                    data: 'kondisi',
                    render: function (data) {
                        const k = kondisiMap[data] ?? { label: data, cls: 'badge-default' };
                        return `<span class="status-badge ${k.cls}">${k.label}</span>`;
                    },
                },
                {
                    data: 'is_active',
                    render: d => d
                        ? '<span class="status-badge status-aktif">AKTIF</span>'
                        : '<span class="status-badge status-tidak_aktif">TIDAK AKTIF</span>',
                },
                {
                    data: null,
                    orderable: false,
                    render: function (row) {
                        return `
                        <div style="display:flex; gap:.25rem;">
                            <button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}" title="Edit">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" data-nama="${row.nama_gedung}" title="Hapus">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                            </button>
                        </div>`;
                    },
                },
            ],
            order: [[1, 'asc']],
            pageLength: 10,
            language: {
                processing: 'Memuat data...', search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Tidak ada data yang ditemukan',
                emptyTable: 'Tidak ada data tersedia',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' },
            },
        });
    }

    function editGedung(id) {
        showLoading();
        $.ajax({
            url: `/gedung/${id}`,
            method: 'GET',
            success: function (res) {
                hideLoading();
                const d = res.data;
                $('#gedungId').val(d.id);
                $('#modalTitle').text('Edit Gedung');
                $('#kode_gedung').val(d.kode_gedung);
                $('#nama_gedung').val(d.nama_gedung);
                $('#jenis_gedung').val(d.jenis_gedung);
                $('#jumlah_lantai').val(d.jumlah_lantai);
                $('#kapasitas_total').val(d.kapasitas_total);
                $('#alamat_lokasi').val(d.alamat_lokasi);
                $('#tahun_dibangun').val(d.tahun_dibangun);
                $('#kondisi').val(d.kondisi);
                $('#fasilitas').val(d.fasilitas);
                $('#keterangan').val(d.keterangan);
                $('#is_active').val(d.is_active ? '1' : '0');
                $('#gedungModal').modal('show');
            },
            error: function (xhr) { hideLoading(); handleAjaxError(xhr); },
        });
    }

    function resetForm() { $('#gedungForm')[0].reset(); $('#gedungId').val(''); }
    function showLoading() { $('#loadingOverlay').addClass('show'); }
    function hideLoading() { $('#loadingOverlay').removeClass('show'); }

    function showNotification(type, message) {
        Swal.fire({ icon: type, title: type === 'success' ? 'Berhasil!' : 'Gagal!', text: message,
            timer: 3000, timerProgressBar: true, showConfirmButton: false, toast: true, position: 'top-end' });
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