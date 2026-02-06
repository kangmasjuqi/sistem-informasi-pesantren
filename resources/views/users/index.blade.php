@extends('layouts.dashboard')

@section('page-title', 'Manajemen User')

@section('extra-css')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    :root {
        --primary-color: #1a3a2e;
        --primary-hover: #265443;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --info-color: #06b6d4;
        --border-color: #e5e7eb;
        --text-muted: #6b7280;
        --bg-light: #f9fafb;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f8fafc;
        color: #1e293b;
    }

    .page-header {
        background: white;
        padding: 2rem;
        margin: -2rem -2rem 2rem -2rem;
        border-bottom: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }

    .page-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.025em;
    }

    .page-header p {
        color: var(--text-muted);
        margin: 0;
        font-size: 0.875rem;
    }

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        background: white;
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

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .btn-warning {
        background: var(--warning-color);
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

    .action-buttons {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    table.dataTable {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.875rem;
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
        vertical-align: middle;
    }

    table.dataTable tbody tr {
        transition: background 0.2s;
    }

    table.dataTable tbody tr:hover {
        background: var(--bg-light);
    }

    .status-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-block;
    }

    .status-aktif {
        background: #d1fae5;
        color: #065f46;
    }

    .status-tidak_aktif {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-banned {
        background: #1f1f1f;
        color: #ffffff;
    }

    .role-badge {
        padding: 0.25rem 0.625rem;
        border-radius: 4px;
        font-size: 0.6875rem;
        font-weight: 600;
        display: inline-block;
        margin: 0.125rem;
        background: #e0e7ff;
        color: #4338ca;
    }

    .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
    }

    .modal-header {
        background: linear-gradient(135deg, #1a3a2e 0%, #0f2419 100%);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 1.5rem 2rem;
        border: none;
    }

    .modal-header .modal-title {
        font-weight: 700;
        font-size: 1.25rem;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 1.5rem 2rem;
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

    .roles-checkboxes {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
        padding: 1rem;
        background: var(--bg-light);
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .role-checkbox-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .role-checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary-color);
    }

    .role-checkbox-item label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        margin: 0;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: var(--text-muted);
        user-select: none;
    }

    .password-wrapper {
        position: relative;
    }
</style>
@endsection

@section('header-actions')
<div class="action-buttons">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah User
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
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" id="filterName" placeholder="Cari nama...">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" id="filterEmail" placeholder="Cari email...">
                </div>
                <div>
                    <label class="form-label">Role</label>
                    <select class="form-select" id="filterRole">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                        <option value="banned">Banned</option>
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
            <table class="table table-hover" id="usersTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Telepon</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('users/modal')

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/users.js') }}"></script>

<script>
$(document).ready(function() {
    let table;
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || document.querySelector('meta[name="csrf-token"]')?.content;

    // Initialize DataTable
    initDataTable();

    // Button: Create
    $('#btnCreate').on('click', function() {
        resetForm();
        $('#modalTitle').text('Tambah User Baru');
        $('#password').attr('required', true);
        $('#password_confirmation').attr('required', true);
        $('#passwordBadge').text('WAJIB');
        $('#passwordConfirmBadge').text('WAJIB');
        $('#passwordHelp').text('Min. 8 karakter');
        $('#userModal').modal('show');
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
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#userId').val();
        const url = id ? `/users/${id}` : '/users';
        const method = id ? 'PUT' : 'POST';
        
        // Get checked roles
        const roles = [];
        $('input[name="roles[]"]:checked').each(function() {
            roles.push($(this).val());
        });

        if (roles.length === 0) {
            showNotification('warning', 'Minimal pilih 1 role');
            return;
        }
        
        const formData = {
            _token: csrfToken,
            _method: method,
            name: $('#name').val(),
            nama_lengkap: $('#nama_lengkap').val(),
            email: $('#email').val(),
            username: $('#username').val(),
            password: $('#password').val(),
            password_confirmation: $('#password_confirmation').val(),
            telepon: $('#telepon').val(),
            alamat: $('#alamat').val(),
            status: $('#status').val(),
            roles: roles
        };

        showLoading();

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                $('#userModal').modal('hide');
                table.ajax.reload();
                showNotification('success', response.message);
            },
            error: function(xhr) {
                hideLoading();
                handleAjaxError(xhr);
            }
        });
    });

    // Edit Button
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        editUser(id);
    });

    // Reset Password Button
    $(document).on('click', '.btn-reset-password', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        resetPassword(id, name);
    });

    // Functions
    function initDataTable() {
        table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/users/data',
                data: function(d) {
                    d.name = $('#filterName').val();
                    d.email = $('#filterEmail').val();
                    d.role_id = $('#filterRole').val();
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                {
                    data: null,
                    render: function(data) {
                        return `<div style="line-height: 1.4;">
                            <strong>${data.nama_lengkap}</strong><br>
                            <small class="text-muted">${data.name}</small>
                        </div>`;
                    }
                },
                { data: 'email' },
                { data: 'username' },
                { data: 'telepon' },
                {
                    data: 'roles',
                    orderable: false,
                    render: function(data) {
                        if (!data || data.length === 0) return '-';
                        return data.map(role => `<span class="role-badge">${role}</span>`).join(' ');
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        const statusMap = {
                            'aktif': 'status-aktif',
                            'tidak_aktif': 'status-tidak_aktif',
                            'banned': 'status-banned'
                        };
                        const displayText = {
                            'aktif': 'Aktif',
                            'tidak_aktif': 'Tidak Aktif',
                            'banned': 'Banned'
                        };
                        return `<span class="status-badge ${statusMap[data]}">${displayText[data]}</span>`;
                    }
                },
                { 
                    data: 'last_login_at',
                    render: function(data) {
                        return `<small>${data}</small>`;
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data) {
                        return `
                            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
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
            order: [[6, 'desc']],
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

    function editUser(id) {
        showLoading();
        
        $.ajax({
            url: `/users/${id}`,
            method: 'GET',
            success: function(response) {
                hideLoading();
                const data = response.data;
                
                $('#userId').val(data.id);
                $('#modalTitle').text('Edit User');
                $('#name').val(data.name);
                $('#nama_lengkap').val(data.nama_lengkap);
                $('#email').val(data.email);
                $('#username').val(data.username);
                $('#telepon').val(data.telepon);
                $('#alamat').val(data.alamat);
                $('#status').val(data.status);
                
                // Password not required for edit
                $('#password').attr('required', false);
                $('#password_confirmation').attr('required', false);
                $('#password').val('');
                $('#password_confirmation').val('');
                $('#passwordBadge').text('Opsional');
                $('#passwordBadge').removeClass('badge-required').addClass('badge-optional');
                $('#passwordConfirmBadge').text('Opsional');
                $('#passwordConfirmBadge').removeClass('badge-required').addClass('badge-optional');
                $('#passwordHelp').text('Min. 8 karakter. Kosongkan jika tidak ingin mengubah password.');
                
                // Check roles
                $('input[name="roles[]"]').prop('checked', false);
                if (data.roles && data.roles.length > 0) {
                    data.roles.forEach(function(roleId) {
                        $(`#role_${roleId}`).prop('checked', true);
                    });
                }
                
                $('#userModal').modal('show');
            },
            error: function(xhr) {
                hideLoading();
                handleAjaxError(xhr);
            }
        });
    }

    function resetPassword(id, name) {
        Swal.fire({
            title: 'Reset Password',
            html: `
                <p>Reset password untuk: <strong>${name}</strong></p>
                <input type="password" id="swal-password" class="swal2-input" placeholder="Password baru (min. 8 karakter)">
                <input type="password" id="swal-password-confirm" class="swal2-input" placeholder="Konfirmasi password">
            `,
            showCancelButton: true,
            confirmButtonText: 'Reset Password',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#1a3a2e',
            preConfirm: () => {
                const password = document.getElementById('swal-password').value;
                const passwordConfirm = document.getElementById('swal-password-confirm').value;
                
                if (!password || !passwordConfirm) {
                    Swal.showValidationMessage('Password dan konfirmasi harus diisi');
                    return false;
                }
                
                if (password.length < 8) {
                    Swal.showValidationMessage('Password minimal 8 karakter');
                    return false;
                }
                
                if (password !== passwordConfirm) {
                    Swal.showValidationMessage('Password dan konfirmasi tidak cocok');
                    return false;
                }
                
                return { password, password_confirmation: passwordConfirm };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                showLoading();
                
                $.ajax({
                    url: `/users/${id}/reset-password`,
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        password: result.value.password,
                        password_confirmation: result.value.password_confirmation
                    },
                    success: function(response) {
                        hideLoading();
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

    function resetForm() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('input[name="roles[]"]').prop('checked', false);
        $('#passwordBadge').text('WAJIB');
        $('#passwordBadge').removeClass('badge-optional').addClass('badge-required');
        $('#passwordConfirmBadge').text('WAJIB');
        $('#passwordConfirmBadge').removeClass('badge-optional').addClass('badge-required');
        $('#password').attr('required', true);
        $('#password_confirmation').attr('required', true);
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

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.type === 'password' ? 'text' : 'password';
    field.type = type;
}
</script>
@endsection