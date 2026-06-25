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
                    <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-graph-up me-1"></i>Report
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.duplicates') }}">
                        <i class="bi bi-copy me-1"></i>Cek Duplikasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.users.index') }}">
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

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: var(--pink-100); color: var(--pink-600);">
                        <i class="bi bi-clipboard-data"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color: var(--pink-600);">{{ $totalFiltered }}</div>
                        <div class="stat-label">Total · {{ $filterLabel }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: #d1fae5; color: #059669;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value text-success">{{ $finishedFiltered }}</div>
                        <div class="stat-label">Selesai · {{ $filterLabel }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="stat-value text-danger">{{ $ongoingCount }}</div>
                        <div class="stat-label">Masih Berjalan (Semua)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="fw-bold mb-3">
        <i class="bi bi-journal-text me-2" style="color: var(--pink-600);"></i>Report Permasalahan
    </h4>

    <!-- Filter Form -->
    <div class="glass-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.dashboard') }}" id="filterForm" class="row g-3 align-items-end">
            <div class="col-12 col-md-auto">
                <label class="form-label fw-bold small text-muted text-uppercase mb-1" id="filterDateLabel">
                    <i class="bi bi-calendar-date me-1" id="filterDateIcon"></i><span id="filterDateText">Tanggal</span>
                </label>
                <div class="input-group" style="border-radius: 10px; overflow: hidden;">
                    <button type="button" id="prevDateBtn" class="btn btn-outline-secondary">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <input type="{{ request('month') ? 'month' : 'date' }}"
                        name="{{ request('month') ? 'month' : 'date' }}"
                        id="filterDateInput"
                        class="form-control text-center"
                        value="{{ request('month') ?? request('date') ?? now()->format('Y-m-d') }}"
                        style="border-radius: 0; min-width: 170px;">
                    <button type="button" id="nextDateBtn" class="btn btn-outline-secondary">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                    <button type="button" id="toggleDateType" class="btn btn-sm btn-pink-outline"
                        style="white-space: nowrap; border-radius: 0; padding: 0.4rem 0.75rem;">
                        {{ request('month') ? 'Date' : 'Month' }}
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-auto">
                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                    <i class="bi bi-tags me-1"></i>Kategori
                </label>
                <select name="kategori" class="form-select" style="border-radius: 10px; min-width: 170px;">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                    <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                        {{ $kat }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-pink me-1">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-pink-outline me-1">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </a>
                <a href="{{ route('admin.dashboard.export', request()->query()) }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="glass-card">
        <div class="table-responsive" style="max-height: 80vh; overflow-y: auto;">
            <table class="table table-premium mb-0" style="min-width: 1000px;">
                <thead style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>No Instruksi</th>
                        <th>Type Traktor</th>
                        <th>Kategori</th>
                        <th>Nama PIC</th>
                        <th>Keterangan</th>
                        <th>Jam Start</th>
                        <th>Jam Finish</th>
                        <th>Total Jam</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perbaikans as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($p->Photo_Path_Perbaikan)
                            <img src="{{ asset('storage/' . $p->Photo_Path_Perbaikan) }}"
                                alt="Foto"
                                class="rounded-2"
                                style="width: 48px; height: 48px; object-fit: cover; cursor: pointer; border: 2px solid var(--pink-200); transition: transform 0.2s;"
                                onmouseover="this.style.transform='scale(1.1)'"
                                onmouseout="this.style.transform='scale(1)'"
                                data-photo="{{ asset('storage/' . $p->Photo_Path_Perbaikan) }}"
                                data-no-instruksi="{{ $p->No_Instruksi ?? '-' }}"
                                data-type-traktor="{{ $p->Type_Traktor ?? '-' }}"
                                data-kategori="{{ $p->Kategori_Perbaikan ?? '-' }}"
                                onclick="openPhotoModal(this)">
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-pink">{{ $p->No_Instruksi ?? $p->Id_Traktor ?? '-' }}</span>
                        </td>
                        <td>{{ $p->Type_Traktor ?? '-' }}</td>
                        <td>
                            @if($p->Kategori_Perbaikan)
                            <span class="badge"
                                style="background: var(--pink-100); color: var(--pink-700); border-radius: 8px; font-size: 0.78rem; padding: 0.3rem 0.6rem;">
                                {{ $p->Kategori_Perbaikan }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($p->Nama_PIC)
                            <i class="bi bi-person-circle me-1 text-muted"></i>{{ $p->Nama_PIC }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-truncate" style="max-width: 200px;" title="{{ $p->Ket_Perbaikan }}">
                            {{ $p->Ket_Perbaikan }}
                        </td>
                        <td>
                            <small>{{ $p->Jam_Start }}</small>
                        </td>
                        <td>
                            @if($p->Jam_Finish)
                            <small>{{ $p->Jam_Finish }}</small>
                            @else
                            <span class="badge-warning-soft">
                                <i class="bi bi-hourglass-split me-1"></i>Proses
                            </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-success-soft">{{ $p->Total_Jam ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada data untuk filter yang dipilih.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Foto -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header border-0"
                style="background: linear-gradient(135deg, var(--pink-500), var(--pink-700)); color: white;">
                <div>
                    <h6 class="modal-title fw-bold mb-0" id="photoModalTitle">Foto Perbaikan</h6>
                    <small id="photoModalSub" class="opacity-75"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3" style="background: #fdf4ff;">
                <img id="photoModalImg" src="" alt="Foto Perbaikan"
                    class="img-fluid rounded-3 shadow"
                    style="max-height: 70vh; object-fit: contain;">
            </div>
            <div class="modal-footer border-0" style="background: #fdf4ff;">
                <a id="photoModalDownload" href="#" download
                    class="btn btn-sm btn-pink">
                    <i class="bi bi-download me-1"></i>Download
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openPhotoModal(el) {
        const src = el.getAttribute('data-photo');
        const noInstruksi = el.getAttribute('data-no-instruksi');
        const typeTraktor = el.getAttribute('data-type-traktor');
        const kategori = el.getAttribute('data-kategori');
        document.getElementById('photoModalImg').src = src;
        document.getElementById('photoModalDownload').href = src;
        document.getElementById('photoModalTitle').textContent = 'Foto — ' + noInstruksi + ' · ' + typeTraktor;
        document.getElementById('photoModalSub').textContent = 'Kategori: ' + kategori;
        new bootstrap.Modal(document.getElementById('photoModal')).show();
    }

    // Toggle Date / Month filter
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('filterDateInput');
        const toggleBtn = document.getElementById('toggleDateType');
        const labelText = document.getElementById('filterDateText');
        const labelIcon = document.getElementById('filterDateIcon');
        const form = document.getElementById('filterForm');

        function updateLabel() {
            if (input.type === 'month') {
                labelText.textContent = 'Bulan';
                labelIcon.className = 'bi bi-calendar-month me-1';
                toggleBtn.textContent = 'Date';
            } else {
                labelText.textContent = 'Tanggal';
                labelIcon.className = 'bi bi-calendar-date me-1';
                toggleBtn.textContent = 'Month';
            }
        }

        toggleBtn.addEventListener('click', function() {
            if (input.type === 'date') {
                input.type = 'month';
                input.name = 'month';
                input.value = '';
            } else {
                input.type = 'date';
                input.name = 'date';
                input.value = '';
            }
            updateLabel();
        });

        // Prev / Next navigation
        function shiftDate(delta) {
            if (!input.value) return;
            if (input.type === 'date') {
                const parts = input.value.split('-');
                const d = new Date(+parts[0], +parts[1] - 1, +parts[2]);
                d.setDate(d.getDate() + delta);
                input.value = d.getFullYear() + '-'
                    + String(d.getMonth() + 1).padStart(2, '0') + '-'
                    + String(d.getDate()).padStart(2, '0');
            } else if (input.type === 'month') {
                const parts = input.value.split('-');
                const d = new Date(+parts[0], +parts[1] - 1, 1);
                d.setMonth(d.getMonth() + delta);
                input.value = d.getFullYear() + '-'
                    + String(d.getMonth() + 1).padStart(2, '0');
            }
            form.submit();
        }

        document.getElementById('prevDateBtn').addEventListener('click', () => shiftDate(-1));
        document.getElementById('nextDateBtn').addEventListener('click', () => shiftDate(1));

        // Initialize label on page load
        updateLabel();
    });
</script>
@endsection