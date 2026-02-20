@extends('layouts.crud')

@section('page-title', 'Manajemen Pembayaran')

@section('header-actions')

<div class="action-buttons d-flex gap-2">

    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Pembayaran
    </button>

    <button class="btn btn-outline-primary" id="btnRefresh">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd"
                  d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
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
                    <label class="form-label">Nama Santri</label>
                    <input type="text" class="form-control" id="filterSantriName" placeholder="Cari nama santri...">
                </div>
                <div>
                    <label class="form-label">Jenis Pembayaran</label>
                    <select class="form-select" id="filterJenisPembayaran">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisPembayaran as $jp)
                            <option value="{{ $jp->id }}">{{ $jp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="lunas">Lunas</option>
                        <option value="belum_lunas">Belum Lunas</option>
                        <option value="cicilan">Cicilan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tanggal Dari</label>
                    <input type="date" class="form-control" id="filterDateFrom">
                </div>
                <div>
                    <label class="form-label">Tanggal Sampai</label>
                    <input type="date" class="form-control" id="filterDateTo">
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

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions">
            <div class="bulk-info">
                <span id="selectedCount">0</span> item dipilih
            </div>
            <div>
                <button class="btn btn-sm btn-danger" id="btnBulkDelete">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                    Hapus Terpilih
                </button>
            </div>
        </div>

        <!-- DataTable -->
        <div class="table-responsive">
            <table class="table table-hover" id="pembayaranTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th width="40">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Santri</th>
                        <th>Jenis Pembayaran</th>
                        <th>Periode</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('pembayaran/modal')

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let table;
    let selectedIds = [];
    const csrfToken = '{{ csrf_token() }}';

    // Initialize DataTable
    initDataTable();

    // Initialize Select2 for Santri
    $('#santri_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#pembayaranModal'),
        placeholder: 'Pilih Santri...',
        ajax: {
            url: '{{ route("pembayaran.search-santri") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return { results: data.results };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    // Auto-fill nominal when jenis pembayaran changes
    $('#jenis_pembayaran_id').on('change', function() {
        const selected = $(this).find(':selected');
        const nominal = selected.data('nominal');
        const kategori = selected.data('kategori');
        
        if (nominal) {
            $('#nominal').val(nominal);
        }

        // Show/hide bulan tahun fields for bulanan category
        if (kategori === 'bulanan') {
            $('#bulanTahunRow').show();
            // Set current month and year as default
            const now = new Date();
            $('#bulan').val(now.getMonth() + 1);
            $('#tahun').val(now.getFullYear());
        } else {
            $('#bulanTahunRow').hide();
            $('#bulan').val('');
            $('#tahun').val('');
        }

        calculateTotal();
    });

    // Calculate total on input changes
    $('#nominal, #potongan, #denda').on('input', calculateTotal);

    // Set today as default date
    $('#tanggal_pembayaran').val(new Date().toISOString().split('T')[0]);

    // Button: Create
    $('#btnCreate').on('click', function() {
        resetForm();
        $('#modalTitle').text('Tambah Pembayaran Baru');
        $('#pembayaranModal').modal('show');
    });

    // Button: Refresh
    $('#btnRefresh').on('click', function() {
        table.ajax.reload();
        showNotification('success', 'Data berhasil direfresh');
    });

    // Button: Apply Filters
    $('#btnApplyFilters').on('click', function() {
        table.ajax.reload();
    });

    // Form Submit
    $('#pembayaranForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#pembayaranId').val();
        const url = id ? `/pembayaran/${id}` : '{{ route("pembayaran.store") }}';
        const method = id ? 'PUT' : 'POST';
        
        const formData = {
            _token: csrfToken,
            _method: method,
            santri_id: $('#santri_id').val(),
            jenis_pembayaran_id: $('#jenis_pembayaran_id').val(),
            tahun_ajaran_id: $('#tahun_ajaran_id').val(),
            tanggal_pembayaran: $('#tanggal_pembayaran').val(),
            bulan: $('#bulan').val(),
            tahun: $('#tahun').val(),
            nominal: $('#nominal').val(),
            potongan: $('#potongan').val() || 0,
            denda: $('#denda').val() || 0,
            metode_pembayaran: $('#metode_pembayaran').val(),
            nomor_referensi: $('#nomor_referensi').val(),
            status: $('#status').val(),
            keterangan: $('#keterangan').val()
        };

        showLoading();

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                $('#pembayaranModal').modal('hide');
                table.ajax.reload();
                showNotification('success', response.message);
            },
            error: function(xhr) {
                hideLoading();
                handleAjaxError(xhr);
            }
        });
    });

    // Select All Checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
        updateSelectedIds();
    });

    // Row Checkbox
    $(document).on('change', '.row-checkbox', function() {
        updateSelectedIds();
    });

    // Bulk Delete
    $('#btnBulkDelete').on('click', function() {
        if (selectedIds.length === 0) {
            showNotification('warning', 'Pilih minimal satu item');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Anda yakin ingin menghapus <strong>${selectedIds.length} pembayaran</strong> yang dipilih?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                bulkDelete();
            }
        });
    });

    // Edit Button
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        editPembayaran(id);
    });

    // Delete Button
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        deletePembayaran(id);
    });

    // View Button
    $(document).on('click', '.btn-view', function() {
        const id = $(this).data('id');
        viewPembayaran(id);
    });

    // Functions
    function initDataTable() {
        table = $('#pembayaranTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("pembayaran.data") }}',
                data: function(d) {
                    d.santri_name = $('#filterSantriName').val();
                    d.jenis_pembayaran_id = $('#filterJenisPembayaran').val();
                    d.status = $('#filterStatus').val();
                    d.date_from = $('#filterDateFrom').val();
                    d.date_to = $('#filterDateTo').val();
                }
            },
            columns: [
                {
                    data: 'id',
                    orderable: false,
                    render: function(data) {
                        return `<div class="checkbox-wrapper">
                            <input type="checkbox" class="row-checkbox" value="${data}">
                        </div>`;
                    }
                },
                { data: 'tanggal_pembayaran' },
                { data: 'kode_pembayaran' },
                {
                    data: null,
                    render: function(data) {
                        return `<div style="line-height: 1.4;">
                            <strong>${data.santri_nama}</strong><br>
                            <small class="text-muted">NIS: ${data.santri_nis}</small>
                        </div>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        const badges = {
                            'bulanan': 'kategori-bulanan',
                            'tahunan': 'kategori-tahunan',
                            'pendaftaran': 'kategori-pendaftaran',
                            'kegiatan': 'kategori-kegiatan',
                            'lainnya': 'kategori-lainnya'
                        };
                        return `<div style="line-height: 1.6;">
                            ${data.jenis_pembayaran}<br>
                            <span class="kategori-badge ${badges[data.jenis_kategori]}">${data.jenis_kategori.toUpperCase()}</span>
                        </div>`;
                    }
                },
                { data: 'bulan_tahun' },
                {
                    data: 'total_bayar',
                    render: function(data) {
                        return `<strong style="color: var(--primary-color);">Rp ${data}</strong>`;
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        const statusMap = {
                            'lunas': 'status-lunas',
                            'belum_lunas': 'status-belum_lunas',
                            'cicilan': 'status-cicilan'
                        };
                        return `<span class="status-badge ${statusMap[data]}">${data.replace('_', ' ')}</span>`;
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
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${data}" title="Hapus">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                    </svg>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, 'desc']],
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

    function editPembayaran(id) {
        showLoading();
        
        $.ajax({
            url: `/pembayaran/${id}`,
            method: 'GET',
            success: function(response) {
                hideLoading();
                const data = response.data;
                
                $('#pembayaranId').val(data.id);
                $('#modalTitle').text('Edit Pembayaran');
                
                // Load santri option
                const santriOption = new Option(
                    `${data.santri_nis} - ${data.santri_nama}`,
                    data.santri_id,
                    true,
                    true
                );
                $('#santri_id').append(santriOption).trigger('change');
                
                $('#jenis_pembayaran_id').val(data.jenis_pembayaran_id).trigger('change');
                $('#tahun_ajaran_id').val(data.tahun_ajaran_id);
                $('#tanggal_pembayaran').val(data.tanggal_pembayaran);
                $('#bulan').val(data.bulan);
                $('#tahun').val(data.tahun);
                $('#nominal').val(data.nominal);
                $('#potongan').val(data.potongan);
                $('#denda').val(data.denda);
                $('#metode_pembayaran').val(data.metode_pembayaran);
                $('#nomor_referensi').val(data.nomor_referensi);
                $('#status').val(data.status);
                $('#keterangan').val(data.keterangan);
                
                calculateTotal();
                $('#pembayaranModal').modal('show');
            },
            error: function(xhr) {
                hideLoading();
                handleAjaxError(xhr);
            }
        });
    }

    function deletePembayaran(id) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Anda yakin ingin menghapus pembayaran ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                $.ajax({
                    url: `/pembayaran/${id}`,
                    method: 'DELETE',
                    data: { _token: csrfToken },
                    success: function(response) {
                        hideLoading();
                        table.ajax.reload();
                        showNotification('success', response.message);
                    },
                    error: function(xhr) {
                        hideLoading();
                        handleAjaxError(xhr);
                    }
                });
            }
        });
    }

    function bulkDelete() {
        showLoading();
        
        $.ajax({
            url: '{{ route("pembayaran.bulk-destroy") }}',
            method: 'POST',
            data: {
                _token: csrfToken,
                ids: selectedIds
            },
            success: function(response) {
                hideLoading();
                selectedIds = [];
                $('#selectAll').prop('checked', false);
                updateBulkActionsVisibility();
                table.ajax.reload();
                showNotification('success', response.message);
            },
            error: function(xhr) {
                hideLoading();
                handleAjaxError(xhr);
            }
        });
    }

    function updateSelectedIds() {
        selectedIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        $('#selectedCount').text(selectedIds.length);
        updateBulkActionsVisibility();
        
        // Update select all checkbox
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
    }

    function updateBulkActionsVisibility() {
        if (selectedIds.length > 0) {
            $('#bulkActions').addClass('show');
        } else {
            $('#bulkActions').removeClass('show');
        }
    }

    function calculateTotal() {
        const nominal = parseFloat($('#nominal').val()) || 0;
        const potongan = parseFloat($('#potongan').val()) || 0;
        const denda = parseFloat($('#denda').val()) || 0;
        const total = nominal - potongan + denda;
        
        $('#displayNominal').text(formatRupiah(nominal));
        $('#displayPotongan').text(formatRupiah(potongan));
        $('#displayDenda').text(formatRupiah(denda));
        $('#displayTotal').text(formatRupiah(total));
    }

    function formatRupiah(number) {
        return 'Rp ' + number.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function resetForm() {
        $('#pembayaranForm')[0].reset();
        $('#pembayaranId').val('');
        $('#santri_id').val(null).trigger('change');
        $('#bulanTahunRow').hide();
        calculateTotal();
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