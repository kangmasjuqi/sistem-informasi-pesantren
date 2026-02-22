@extends('layouts.crud')

@section('page-title', 'Manajemen Perizinan')

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
        Ajukan Izin
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
                    <label class="form-label">No. Izin</label>
                    <input type="text" class="form-control" id="filterNomorIzin" placeholder="Cari nomor...">
                </div>
                <div>
                    <label class="form-label">Jenis Izin</label>
                    <select class="form-select" id="filterJenis">
                        <option value="">Semua Jenis</option>
                        <option value="pulang">Pulang</option>
                        <option value="kunjungan">Kunjungan</option>
                        <option value="sakit">Sakit</option>
                        <option value="keluar_sementara">Keluar Sementara</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="diajukan">Diajukan</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="filterTanggalDari">
                </div>
                <div>
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="filterTanggalSampai">
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
            <table class="table table-hover" id="perizinanTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>No. Izin</th>
                        <th>Santri</th>
                        <th>Jenis</th>
                        <th>Periode</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('perizinan.modal')
@include('perizinan.modal-approve')

<div class="loading-overlay" id="loadingOverlay"><div class="spinner"></div></div>
@endsection

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
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
        pulang:           { label: 'Pulang',           cls: 'kategori-bulanan' },
        kunjungan:        { label: 'Kunjungan',        cls: 'kategori-kegiatan' },
        sakit:            { label: 'Sakit',            cls: 'kategori-tahunan' },
        keluar_sementara: { label: 'Keluar Sementara', cls: 'kategori-lainnya' },
    };

    const statusMap = {
        diajukan:  { label: 'Diajukan',  cls: 'status-cicilan' },
        disetujui: { label: 'Disetujui', cls: 'status-aktif' },
        ditolak:   { label: 'Ditolak',   cls: 'status-tidak_aktif' },
        selesai:   { label: 'Selesai',   cls: 'badge-default' },
    };

    initDataTable();

    // ── Select2: Santri (AJAX search) ────────────────────────────
    $('#santri_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#perizinanModal'),
        placeholder: 'Ketik min. 2 huruf untuk mencari santri...',
        allowClear: true,
        ajax: {
            url: '{{ route("perizinan.search-santri") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true,
        },
        minimumInputLength: 2,
    });

    $('#btnCreate').on('click', function () { resetForm(); $('#modalTitle').text('Ajukan Perizinan Baru'); $('#perizinanModal').modal('show'); });
    $('#btnRefresh').on('click', function () { table.ajax.reload(); showNotification('success', 'Data berhasil direfresh'); });
    $('#btnApplyFilters').on('click', function () { table.ajax.reload(); });

    $('#perizinanForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#perizinanId').val();
        showLoading();
        $.ajax({
            url: id ? `/perizinan/${id}` : '/perizinan',
            method: 'POST',
            data: { _token: csrfToken, _method: id ? 'PUT' : 'POST',
                santri_id: $('#santri_id').val(), jenis_izin: $('#jenis_izin').val(),
                tanggal_mulai: $('#tanggal_mulai').val(), tanggal_selesai: $('#tanggal_selesai').val(),
                waktu_keluar: $('#waktu_keluar').val(), waktu_kembali: $('#waktu_kembali').val(),
                keperluan: $('#keperluan').val(), tujuan: $('#tujuan').val(),
                penjemput_nama: $('#penjemput_nama').val(), penjemput_hubungan: $('#penjemput_hubungan').val(),
                penjemput_telepon: $('#penjemput_telepon').val(), penjemput_identitas: $('#penjemput_identitas').val(),
                keterangan: $('#keterangan').val(),
            },
            success: function (res) { hideLoading(); $('#perizinanModal').modal('hide'); table.ajax.reload(); showNotification('success', res.message); },
            error:   function (xhr) { hideLoading(); handleAjaxError(xhr); },
        });
    });

    // Approve form
    $('#approveForm').on('submit', function (e) {
        e.preventDefault();
        const id     = $('#approveId').val();
        const action = $('input[name="action"]:checked').val();
        showLoading();
        $.ajax({
            url: `/perizinan/${id}/approve`, method: 'POST',
            data: { _token: csrfToken, action, catatan_persetujuan: $('#catatan_persetujuan').val() },
            success: function (res) { hideLoading(); $('#approveModal').modal('hide'); table.ajax.reload(); showNotification('success', res.message); },
            error:   function (xhr) { hideLoading(); handleAjaxError(xhr); },
        });
    });

    $(document).on('click', '.btn-edit',    function () { editPerizinan($(this).data('id')); });
    $(document).on('click', '.btn-approve', function () { openApprove($(this).data('id'), $(this).data('nomor')); });
    $(document).on('click', '.btn-selesai', function () { markSelesai($(this).data('id'), $(this).data('nomor')); });
    $(document).on('click', '.btn-delete',  function () {
        const id = $(this).data('id'), nomor = $(this).data('nomor');
        Swal.fire({ icon:'warning', title:'Hapus Perizinan?', html:`Izin <strong>${nomor}</strong> akan dihapus.`,
            showCancelButton:true, confirmButtonColor:'#ef4444', cancelButtonColor:'#6b7280',
            confirmButtonText:'Ya, Hapus!', cancelButtonText:'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            showLoading();
            $.ajax({ url:`/perizinan/${id}`, method:'POST', data:{_token:csrfToken,_method:'DELETE'},
                success: res => { hideLoading(); table.ajax.reload(); showNotification('success', res.message); },
                error:   xhr => { hideLoading(); handleAjaxError(xhr); },
            });
        });
    });

    function initDataTable() {
        table = $('#perizinanTable').DataTable({
            processing: true, serverSide: true,
            ajax: { url: '/perizinan/data', data: function (d) {
                d.nomor_izin      = $('#filterNomorIzin').val();
                d.jenis_izin      = $('#filterJenis').val();
                d.status          = $('#filterStatus').val();
                d.tanggal_dari    = $('#filterTanggalDari').val();
                d.tanggal_sampai  = $('#filterTanggalSampai').val();
            }},
            columns: [
                { data: 'nomor_izin' },
                { data: null, render: r => `<div><strong>${r.santri_nama}</strong><br><small class="text-muted">${r.santri_nis}</small></div>` },
                { data: 'jenis_izin', render: function (d) { const j = jenisMap[d] ?? {label:d, cls:'badge-default'}; return `<span class="kategori-badge ${j.cls}">${j.label}</span>`; }},
                { data: null, render: r => `<div style="font-size:.85rem;">${r.tanggal_mulai_fmt}<br><span class="text-muted">s/d ${r.tanggal_selesai_fmt}</span></div>` },
                { data: 'durasi_hari', className:'text-center', render: d => `<strong>${d}</strong> hari` },
                { data: 'status', render: function (d) { const s = statusMap[d] ?? {label:d, cls:'badge-default'}; return `<span class="status-badge ${s.cls}">${s.label.toUpperCase()}</span>`; }},
                { data: null, orderable: false, render: function (row) {
                    const canApprove = ['diajukan'].includes(row.status);
                    const canSelesai = row.status === 'disetujui';
                    const canEdit    = ['diajukan','ditolak'].includes(row.status);
                    return `<div style="display:flex; gap:.25rem; flex-wrap:wrap;">
                        ${canEdit    ? `<button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}" title="Edit"><svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg></button>` : ''}
                        ${canApprove ? `<button class="btn btn-sm btn-success btn-approve" data-id="${row.id}" data-nomor="${row.nomor_izin}" title="Setujui/Tolak">✓</button>` : ''}
                        ${canSelesai ? `<button class="btn btn-sm btn-warning btn-selesai" data-id="${row.id}" data-nomor="${row.nomor_izin}" title="Tandai Selesai">⏎</button>` : ''}
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" data-nomor="${row.nomor_izin}" title="Hapus"><svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button>
                    </div>`;
                }},
            ],
            order: [[0, 'desc']], pageLength: 10,
            language: { processing:'Memuat data...', search:'Cari:', lengthMenu:'Tampilkan _MENU_ data',
                info:'Menampilkan _START_ sampai _END_ dari _TOTAL_ data', infoEmpty:'0 data',
                infoFiltered:'(difilter dari _MAX_ total data)', zeroRecords:'Tidak ada data', emptyTable:'Tidak ada data',
                paginate:{ first:'Pertama', last:'Terakhir', next:'Selanjutnya', previous:'Sebelumnya' } },
        });
    }

    function editPerizinan(id) {
        showLoading();
        $.ajax({ url:`/perizinan/${id}`, method:'GET', success: function (res) {
            hideLoading(); const d = res.data;
            $('#perizinanId').val(d.id); $('#modalTitle').text('Edit Perizinan');
            // Select2: inject the selected santri option so it displays correctly
            const santriOption = new Option(
                `${d.santri_nis} - ${d.santri_nama}`,
                d.santri_id, true, true
            );
            $('#santri_id').empty().append(santriOption).trigger('change');
            $('#jenis_izin').val(d.jenis_izin);
            $('#tanggal_mulai').val(d.tanggal_mulai); $('#tanggal_selesai').val(d.tanggal_selesai);
            $('#waktu_keluar').val(d.waktu_keluar); $('#waktu_kembali').val(d.waktu_kembali);
            $('#keperluan').val(d.keperluan); $('#tujuan').val(d.tujuan);
            $('#penjemput_nama').val(d.penjemput_nama); $('#penjemput_hubungan').val(d.penjemput_hubungan);
            $('#penjemput_telepon').val(d.penjemput_telepon); $('#penjemput_identitas').val(d.penjemput_identitas);
            $('#keterangan').val(d.keterangan);
            $('#perizinanModal').modal('show');
        }, error: xhr => { hideLoading(); handleAjaxError(xhr); }});
    }

    function openApprove(id, nomor) {
        $('#approveId').val(id); $('#approveNomor').text(nomor);
        $('#catatan_persetujuan').val('');
        $('input[name="action"][value="disetujui"]').prop('checked', true);
        $('#approveModal').modal('show');
    }

    function markSelesai(id, nomor) {
        Swal.fire({ icon:'question', title:'Santri Sudah Kembali?',
            html:`Konfirmasi bahwa santri dengan izin <strong>${nomor}</strong> telah kembali ke pesantren.`,
            showCancelButton:true, confirmButtonColor:'#10b981', cancelButtonColor:'#6b7280',
            confirmButtonText:'Ya, Tandai Selesai!', cancelButtonText:'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            showLoading();
            $.ajax({ url:`/perizinan/${id}/selesai`, method:'POST', data:{_token:csrfToken},
                success: res => { hideLoading(); table.ajax.reload(); showNotification('success', res.message); },
                error:   xhr => { hideLoading(); handleAjaxError(xhr); },
            });
        });
    }

    function resetForm() { $('#perizinanForm')[0].reset(); $('#perizinanId').val(''); $('#santri_id').val(null).trigger('change'); }
    function showLoading() { $('#loadingOverlay').addClass('show'); }
    function hideLoading() { $('#loadingOverlay').removeClass('show'); }
    function showNotification(type, message) {
        Swal.fire({ icon:type, title:type==='success'?'Berhasil!':'Gagal!', text:message,
            timer:3000, timerProgressBar:true, showConfirmButton:false, toast:true, position:'top-end' });
    }
    function handleAjaxError(xhr) {
        let message = 'Terjadi kesalahan pada server';
        if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
        if (xhr.responseJSON?.errors)  message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        Swal.fire({ icon:'error', title:'Error!', html:message, confirmButtonColor:'#ef4444' });
    }
});
</script>
@endsection