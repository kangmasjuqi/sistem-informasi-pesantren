@extends('layouts.crud')

@section('page-title', 'Manajemen Wali Santri')

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
        Tambah Wali
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
                    <label class="form-label">Santri</label>
                    <select class="form-select" id="filterSantri" style="width:100%;">
                        <option value="">Semua Santri</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jenis Wali</label>
                    <select class="form-select" id="filterJenis">
                        <option value="">Semua</option>
                        <option value="ayah">👨 Ayah</option>
                        <option value="ibu">👩 Ibu</option>
                        <option value="wali">🧑 Wali</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Nama Wali</label>
                    <input type="text" class="form-control" id="filterNama" placeholder="Cari nama...">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua</option>
                        <option value="hidup">Hidup</option>
                        <option value="meninggal">Meninggal</option>
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
            <table class="table table-hover" id="waliTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>Santri</th>
                        <th>Jenis</th>
                        <th>Nama Wali</th>
                        <th>Pekerjaan</th>
                        <th>Penghasilan</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('wali.modal')
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

    const jenisMap = {
        ayah: { label: '👨 Ayah', cls: 'kategori-bulanan' },
        ibu:  { label: '👩 Ibu',  cls: 'kategori-kegiatan' },
        wali: { label: '🧑 Wali', cls: 'kategori-lainnya' },
    };
    const statusMap = {
        hidup:     { label: 'Hidup',     cls: 'status-aktif' },
        meninggal: { label: 'Meninggal', cls: 'status-tidak_aktif' },
    };

    initDataTable();

    // ── Select2: santri_id (modal form) ──────────────────────────
    $('#santri_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#waliModal'),
        placeholder: 'Ketik min. 2 huruf untuk mencari santri...',
        allowClear: true,
        ajax: {
            url: '{{ route("santri.search-santri") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results }),
            cache: true,
        },
        minimumInputLength: 2,
    });

    // ── Select2: filter Santri (filter bar) ──────────────────────
    $('#filterSantri').select2({
        theme: 'bootstrap-5',
        placeholder: 'Cari santri...',
        allowClear: true,
        ajax: {
            url: '{{ route("santri.search-santri") }}',
            dataType: 'json',
            delay: 300,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results }),
            cache: true,
        },
        minimumInputLength: 2,
    });

    $('#btnCreate').on('click', function () { resetForm(); $('#modalTitle').text('Tambah Wali Santri'); $('#waliModal').modal('show'); });
    $('#btnRefresh').on('click', function () { table.ajax.reload(); showNotification('success', 'Data berhasil direfresh'); });
    $('#btnApplyFilters').on('click', function () { table.ajax.reload(); });

    $('#waliForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#waliId').val();
        // Clean penghasilan before submit
        const penghasilanRaw = $('#penghasilan').val().replace(/\./g, '').replace(',', '.');
        $('#penghasilan').val(penghasilanRaw);
        showLoading();
        $.ajax({
            url: id ? `/wali-santri/${id}` : '/wali-santri',
            method: 'POST',
            data: { _token: csrfToken, _method: id ? 'PUT' : 'POST',
                santri_id: $('#santri_id').val(), jenis_wali: $('#jenis_wali').val(),
                nama_lengkap: $('#nama_lengkap').val(), nik: $('#nik').val(),
                tempat_lahir: $('#tempat_lahir').val(), tanggal_lahir: $('#tanggal_lahir').val(),
                pendidikan_terakhir: $('#pendidikan_terakhir').val(), pekerjaan: $('#pekerjaan').val(),
                penghasilan: penghasilanRaw, telepon: $('#telepon').val(),
                email: $('#email').val(), alamat: $('#alamat').val(),
                status: $('#status').val(), keterangan: $('#keterangan').val(),
            },
            success: res => { hideLoading(); $('#waliModal').modal('hide'); table.ajax.reload(); showNotification('success', res.message); },
            error:   xhr => { hideLoading(); handleAjaxError(xhr); },
        });
    });

    $(document).on('click', '.btn-edit',   function () { editWali($(this).data('id')); });
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id'), nama = $(this).data('nama');
        Swal.fire({ icon: 'warning', title: 'Hapus Data Wali?',
            html: `Data <strong>${nama}</strong> akan dihapus.`,
            showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            showLoading();
            $.ajax({ url: `/wali-santri/${id}`, method: 'POST', data: { _token: csrfToken, _method: 'DELETE' },
                success: res => { hideLoading(); table.ajax.reload(); showNotification('success', res.message); },
                error:   xhr => { hideLoading(); handleAjaxError(xhr); },
            });
        });
    });

    // ── Live rupiah formatting for penghasilan ────────────────────
    $(document).on('input', '#penghasilan', function () {
        let val = this.value.replace(/\D/g, '');
        if (val) this.value = parseInt(val).toLocaleString('id-ID');
    });

    function initDataTable() {
        table = $('#waliTable').DataTable({
            processing: true, serverSide: true,
            ajax: { url: '/wali-santri/data', data: function (d) {
                d.santri_id    = $('#filterSantri').val();
                d.jenis_wali   = $('#filterJenis').val();
                d.nama_lengkap = $('#filterNama').val();
                d.status       = $('#filterStatus').val();
            }},
            columns: [
                { data: null, render: r => `<div><strong>${r.santri_nama}</strong><br><small class="text-muted">${r.santri_nis}</small></div>` },
                { data: 'jenis_wali', render: function (d) {
                    const j = jenisMap[d] ?? { label: d, cls: 'badge-default' };
                    return `<span class="kategori-badge ${j.cls}">${j.label}</span>`;
                }},
                { data: 'nama_lengkap', render: (d, _, r) => `<strong>${d}</strong>${r.pekerjaan ? `<br><small class="text-muted">${r.pekerjaan}</small>` : ''}` },
                { data: 'pekerjaan', render: d => d ?? '<span class="text-muted">—</span>' },
                { data: 'penghasilan_fmt', className: 'text-end', render: d => `<strong style="color:var(--primary-color);">${d}</strong>` },
                { data: 'telepon', render: d => d
                    ? `<a href="tel:${d}" style="color:inherit; text-decoration:none;">${d}</a>`
                    : '<span class="text-muted">—</span>' },
                { data: 'status', render: function (d) {
                    const s = statusMap[d] ?? { label: d, cls: 'badge-default' };
                    return `<span class="status-badge ${s.cls}">${s.label.toUpperCase()}</span>`;
                }},
                { data: null, orderable: false, render: function (row) {
                    return `<div style="display:flex; gap:.25rem;">
                        <button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}" title="Edit">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" data-nama="${row.nama_lengkap}" title="Hapus">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
                        </button>
                    </div>`;
                }},
            ],
            order: [[0, 'asc']], pageLength: 10,
            language: { processing:'Memuat data...', search:'Cari:', lengthMenu:'Tampilkan _MENU_ data',
                info:'Menampilkan _START_ sampai _END_ dari _TOTAL_ data', infoEmpty:'0 data',
                infoFiltered:'(difilter dari _MAX_ total data)', zeroRecords:'Tidak ada data', emptyTable:'Tidak ada data',
                paginate:{ first:'Pertama', last:'Terakhir', next:'Selanjutnya', previous:'Sebelumnya' } },
        });
    }

    function editWali(id) {
        showLoading();
        $.ajax({ url: `/wali-santri/${id}`, method: 'GET', success: function (res) {
            hideLoading();
            const d = res.data;
            $('#waliId').val(d.id);
            $('#modalTitle').text('Edit Data Wali Santri');
            // Select2: inject santri option
            const opt = new Option(`${d.santri_nis} – ${d.santri_nama}`, d.santri_id, true, true);
            $('#santri_id').empty().append(opt).trigger('change');
            $('#jenis_wali').val(d.jenis_wali);
            $('#nama_lengkap').val(d.nama_lengkap); $('#nik').val(d.nik);
            $('#tempat_lahir').val(d.tempat_lahir); $('#tanggal_lahir').val(d.tanggal_lahir);
            $('#pendidikan_terakhir').val(d.pendidikan_terakhir); $('#pekerjaan').val(d.pekerjaan);
            $('#penghasilan').val(d.penghasilan);
            $('#telepon').val(d.telepon); $('#email').val(d.email);
            $('#alamat').val(d.alamat); $('#status').val(d.status); $('#keterangan').val(d.keterangan);
            $('#waliModal').modal('show');
        }, error: xhr => { hideLoading(); handleAjaxError(xhr); }});
    }

    function resetForm() {
        $('#waliForm')[0].reset();
        $('#waliId').val('');
        $('#santri_id').val(null).trigger('change');
    }

    function showLoading()  { $('#loadingOverlay').addClass('show'); }
    function hideLoading()  { $('#loadingOverlay').removeClass('show'); }

    function handleAjaxError(xhr) {
        let message = 'Terjadi kesalahan pada server';
        if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
        if (xhr.responseJSON?.errors)  message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        Swal.fire({ icon: 'error', title: 'Error!', html: message, confirmButtonColor: '#ef4444' });
    }
});
</script>
@endsection