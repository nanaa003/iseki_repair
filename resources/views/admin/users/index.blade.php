@extends('layouts.app')
@section('hide_navbar', true)

@section('content')
<!-- Admin Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-admin">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-wrench-adjustable-circle me-2"></i>Admin Iseki Repair
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-graph-up me-1"></i>Report
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.duplicates') }}">
                        <i class="bi bi-copy me-1"></i>Cek Duplikasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.work-schedules.index') }}">
                        <i class="bi bi-clock-history me-1"></i>Jam Kerja
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people me-1"></i>Manajemen User
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm text-white"
                            style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 0.4rem 1rem;">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4 pb-5 fade-in">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan pada inputan Anda.
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-people me-2" style="color: var(--pink-600);"></i>Manajemen User Admin
        </h4>
        <button type="button" class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus me-1"></i>Tambah User
        </button>
    </div>

    <!-- Data Table -->
    <div class="glass-card">
        <div class="table-responsive">
            <table class="table table-premium mb-0">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th style="width: 15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $user->Name_User }}</td>
                        <td>{{ $user->Username_User }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;"
                                data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->Id_User }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.users.destroy', $user->Id_User) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data user.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
            @csrf
            <div class="modal-header border-0" style="background: linear-gradient(135deg, var(--pink-500), var(--pink-700)); color: white;">
                <h5 class="modal-title fw-bold" id="addUserModalLabel">
                    <i class="bi bi-person-plus me-2"></i>Tambah User Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #fdf4ff;">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Nama</label>
                    <input type="text" class="form-control" name="Name_User" required style="border-radius: 10px;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Username</label>
                    <input type="text" class="form-control" name="Username_User" required style="border-radius: 10px;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Password</label>
                    <input type="password" class="form-control" name="Password_User" required style="border-radius: 10px;" minlength="6">
                </div>
            </div>
            <div class="modal-footer border-0" style="background: #fdf4ff;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                <button type="submit" class="btn btn-pink" style="border-radius: 10px;">Simpan</button>
            </div>
        </form>
    </div>
</div>

@foreach($users as $user)
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal{{ $user->Id_User }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->Id_User }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.update', $user->Id_User) }}" method="POST" class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
            @csrf
            @method('PUT')
            <div class="modal-header border-0" style="background: linear-gradient(135deg, var(--pink-500), var(--pink-700)); color: white;">
                <h5 class="modal-title fw-bold" id="editUserModalLabel{{ $user->Id_User }}">
                    <i class="bi bi-pencil-square me-2"></i>Edit User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #fdf4ff;">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Nama</label>
                    <input type="text" class="form-control" name="Name_User" value="{{ $user->Name_User }}" required style="border-radius: 10px;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Username</label>
                    <input type="text" class="form-control" name="Username_User" value="{{ $user->Username_User }}" required style="border-radius: 10px;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Password Baru <small class="text-danger fw-normal">(Kosongkan jika tidak ingin mengubah)</small></label>
                    <input type="password" class="form-control" name="Password_User" style="border-radius: 10px;" minlength="6">
                </div>
            </div>
            <div class="modal-footer border-0" style="background: #fdf4ff;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                <button type="submit" class="btn btn-pink" style="border-radius: 10px;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection
