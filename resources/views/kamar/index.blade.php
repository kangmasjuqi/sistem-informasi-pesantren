@extends('layouts.crud')

@section('page-title', 'Manajemen Kamar')

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
        Tambah Kamar
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
        <div class="filters-section">
            <h6 style="margin:0 0 1rem 0; font-weight:700; color:#374151;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:-2px; margin-right:.5rem;"><path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>
                Filter Pencarian
            </h6>
            <div class="filters-grid">
                <div>
                    <label class="form-label">Gedung</label>
                    <select class="form-select" id="filterGedung">
                        <option value="">Semua Gedung</option>
                        @foreach($gedungs as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gedung }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">No. Kamar</label>
                    <input type="text" class="form-control" id="filterNomor" placeholder="Cari nomor...">
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
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                        Terapkan
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="kamarTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>Gedung</th>
                        <th>No. Kamar</th>
                        <th>Lantai</th>
                        <th>Kapasitas</th>
                        <th>Penghuni</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('kamar.modal')
<div class="loading-overlay" id="loadingOverlay"><div class="spinner"></div></div>
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

    const kondisiMap = {
        baik:         { label:'Baik',         cls:'status-aktif' },
        rusak_ringan: { label:'Rusak Ringan', cls:'status-cicilan' },
        rusak_berat:  { label:'Rusak Berat',  cls:'status-tidak_aktif' },
    };

    initDataTable();

    $('#btnCreate').on('click', function () { resetForm(); $('#modalTitle').text('Tambah Kamar'); $('#kamarModal').modal('show'); });
    $('#btnRefresh').on('click', function () { table.ajax.reload(); showNotification('success', 'Data berhasil direfresh'); });
    $('#btnApplyFilters').on('click', function () { table.ajax.reload(); });

    $('#kamarForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#kamarId').val();
        showLoading();
        $.ajax({
            url: id ? `/kamar/${id}` : '/kamar', method:'POST',
            data: { _token:csrfToken, _method:id?'PUT':'POST',
                gedung_id:$('#gedung_id').val(), nomor_kamar:$('#nomor_kamar').val(),
                nama_kamar:$('#nama_kamar').val(), lantai:$('#lantai').val(),
                kapasitas:$('#kapasitas').val(), luas:$('#luas').val(),
                fasilitas:$('#fasilitas').val(), kondisi:$('#kondisi').val(),
                is_active:$('#is_active').val(), keterangan:$('#keterangan').val() },
            success: res => { hideLoading(); $('#kamarModal').modal('hide'); table.ajax.reload(); showNotification('success', res.message); },
            error:   xhr => { hideLoading(); handleAjaxError(xhr); },
        });
    });

    $(document).on('click', '.btn-edit',   function () { editKamar($(this).data('id')); });
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id'), nama = $(this).data('nama');
        Swal.fire({ icon:'warning', title:'Hapus Kamar?', html:`Kamar <strong>${nama}</strong> akan dihapus.`,
            showCancelButton:true, confirmButtonColor:'#ef4444', cancelButtonColor:'#6b7280',
            confirmButtonText:'Ya, Hapus!', cancelButtonText:'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            showLoading();
            $.ajax({ url:`/kamar/${id}`, method:'POST', data:{_token:csrfToken,_method:'DELETE'},
                success: res => { hideLoading(); table.ajax.reload(); showNotification('success', res.message); },
                error:   xhr => { hideLoading(); handleAjaxError(xhr); },
            });
        });
    });

    function initDataTable() {
        table = $('#kamarTable').DataTable({
            processing:true, serverSide:true,
            ajax:{ url:'/kamar/data', data: function (d) {
                d.gedung_id   = $('#filterGedung').val();
                d.nomor_kamar = $('#filterNomor').val();
                d.kondisi     = $('#filterKondisi').val();
                d.is_active   = $('#filterStatus').val();
            }},
            columns:[
                { data:'gedung_nama' },
                { data:null, render: r => `<div><strong>${r.nomor_kamar}</strong>${r.nama_kamar?`<br><small class="text-muted">${r.nama_kamar}</small>`:''}</div>` },
                { data:'lantai', className:'text-center', render: d => `Lt. ${d}` },
                { data:'kapasitas', className:'text-center', render: d => `<strong>${d}</strong> org` },
                { data:null, className:'text-center', render: function (r) {
                    const pct  = r.kapasitas > 0 ? Math.round((r.penghuni / r.kapasitas) * 100) : 0;
                    const color= pct < 75 ? '#10b981' : pct < 100 ? '#f59e0b' : '#ef4444';
                    return `<div style="display:flex; align-items:center; gap:.5rem; min-width:100px;">
                        <div style="flex:1; background:#e5e7eb; border-radius:99px; height:6px; overflow:hidden;">
                            <div style="width:${Math.min(pct,100)}%; height:100%; background:${color}; border-radius:99px;"></div>
                        </div>
                        <span style="font-size:.8rem; font-weight:700; color:${color};">${r.penghuni}/${r.kapasitas}</span>
                    </div>`;
                }},
                { data:'kondisi', render: function (d) { const k=kondisiMap[d]??{label:d,cls:'badge-default'}; return `<span class="status-badge ${k.cls}">${k.label}</span>`; }},
                { data:'is_active', render: d => d ? '<span class="status-badge status-aktif">AKTIF</span>' : '<span class="status-badge status-tidak_aktif">NONAKTIF</span>' },
                { data:null, orderable:false, render: function (row) { return `
                    <div style="display:flex; gap:.25rem;">
                        <button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}" title="Edit"><svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg></button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" data-nama="${row.nomor_kamar}" title="Hapus"><svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button>
                    </div>`; }},
            ],
            order:[[0,'asc'],[1,'asc']], pageLength:10,
            language:{ processing:'Memuat data...', search:'Cari:', lengthMenu:'Tampilkan _MENU_ data',
                info:'Menampilkan _START_ sampai _END_ dari _TOTAL_ data', infoEmpty:'0 data',
                infoFiltered:'(difilter dari _MAX_ total data)', zeroRecords:'Tidak ada data', emptyTable:'Tidak ada data',
                paginate:{first:'Pertama',last:'Terakhir',next:'Selanjutnya',previous:'Sebelumnya'} },
        });
    }

    function editKamar(id) {
        showLoading();
        $.ajax({ url:`/kamar/${id}`, method:'GET', success: function (res) {
            hideLoading(); const d = res.data;
            $('#kamarId').val(d.id); $('#modalTitle').text('Edit Kamar');
            $('#gedung_id').val(d.gedung_id); $('#nomor_kamar').val(d.nomor_kamar);
            $('#nama_kamar').val(d.nama_kamar); $('#lantai').val(d.lantai);
            $('#kapasitas').val(d.kapasitas); $('#luas').val(d.luas);
            $('#fasilitas').val(d.fasilitas); $('#kondisi').val(d.kondisi);
            $('#is_active').val(d.is_active?'1':'0'); $('#keterangan').val(d.keterangan);
            $('#kamarModal').modal('show');
        }, error: xhr => { hideLoading(); handleAjaxError(xhr); }});
    }

    function resetForm() { $('#kamarForm')[0].reset(); $('#kamarId').val(''); }
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