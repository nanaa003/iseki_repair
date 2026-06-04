@extends('layouts.app')

@section('content')
<div class="hero-banner">
    <h1><i class="bi bi-wrench-adjustable me-2"></i>Dashboard Permasalahan</h1>
    <p>Monitor status permasalahan dan perbaikan yang sedang berlangsung.</p>
</div>

<div class="container pb-5 fade-in">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value text-danger">{{ $ongoing->count() }}</div>
                        <div class="stat-label">Sedang Proses</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: #d1fae5; color: #059669;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value text-success">{{ $recent->count() }}</div>
                        <div class="stat-label">Selesai</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="stat-card d-flex align-items-center justify-content-between h-100">
                <div>
                    <div class="stat-label mb-1">Aksi Cepat</div>
                    <p class="mb-0 text-muted small">Tambah data perbaikan traktor baru</p>
                </div>
                <a href="{{ route('repair.create') }}" class="btn btn-pink px-4 shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i>Permasalahan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Active Repairs -->
    <div class="glass-card mb-4 slide-up">
        <div class="card-header-pink d-flex align-items-center justify-content-between">
            <span><i class="bi bi-gear-wide-connected me-2"></i>Permasalahan Sedang Berlangsung</span>
            <span class="badge bg-white text-danger fw-bold">{{ $ongoing->count() }} aktif</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium mb-0">
                    <thead>
                        <tr class="text-nowrap">
                            <th>Traktor</th>
                            <th>Permasalahan</th>
                            <th>Jam Mulai</th>
                            <th class="text-center">Foto</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ongoing as $p)
                        @php
                        $start = \Carbon\Carbon::parse($p->Jam_Start);
                        $elapsed = $start->diffForHumans(null, true);
                        @endphp
                        <tr>
                            <td>
                                <div class="mb-1"><span class="badge-pink">{{ $p->No_Instruksi ?? $p->Id_Traktor ?? '-' }}</span></div>
                                <div class="small text-muted fw-semibold">{{ $p->Type_Traktor ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="mb-1">
                                    @if($p->Kategori_Perbaikan)
                                    <span class="badge" style="background: var(--pink-100); color: var(--pink-700); border-radius: 6px; font-size: 0.75rem; padding: 0.2rem 0.5rem;">{{ $p->Kategori_Perbaikan }}</span>
                                    @else
                                    <span class="text-muted small">-</span>
                                    @endif
                                </div>
                                <div class="text-wrap small" style="max-width: 250px;">{{ $p->Ket_Perbaikan }}</div>
                            </td>
                            <td>
                                <i class="bi bi-clock text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($p->Jam_Start)->format('H:i:s') }}
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($p->Jam_Start)->format('d M Y') }}</small>
                            </td>
                            <td class="text-center">
                                @if($p->Photo_Path_Perbaikan)
                                <img src="{{ asset('storage/' . $p->Photo_Path_Perbaikan) }}"
                                    alt="Foto"
                                    class="rounded-2"
                                    style="width: 48px; height: 48px; object-fit: cover; cursor: pointer; border: 2px solid var(--pink-200); transition: transform 0.2s;"
                                    onmouseover="this.style.transform='scale(1.1)'"
                                    onmouseout="this.style.transform='scale(1)'"
                                    data-photo="{{ asset('storage/' . $p->Photo_Path_Perbaikan) }}"
                                    data-no-instruksi="{{ $p->No_Instruksi ?? $p->Id_Traktor ?? '-' }}"
                                    data-type-traktor="{{ $p->Type_Traktor ?? '-' }}"
                                    data-kategori="{{ $p->Kategori_Perbaikan ?? '-' }}"
                                    onclick="openPhotoModal(this)">
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('repair.finishForm', $p->Id_Perbaikan) }}" class="btn btn-sm btn-success px-3 me-1 mb-1">
                                    <i class="bi bi-check2-circle me-1"></i>Selesaikan
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-primary mb-1 me-1" style="border-radius: 8px;"
                                    data-bs-toggle="modal" data-bs-target="#editRepairModal{{ $p->Id_Perbaikan }}" title="Edit Keterangan">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger mb-1" onclick="hapusData({{ $p->Id_Perbaikan }}, this)" title="Hapus data ini" style="border-radius: 8px;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-emoji-smile text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">Tidak ada perbaikan yang sedang berlangsung.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Filter for Recently Finished -->
    <div class="glass-card mb-4 p-3 slide-up" style="animation-delay: 0.1s;">
        <form method="GET" action="{{ route('repair.index') }}" class="row g-2 align-items-end" id="filterForm">
            <div class="col-auto">
                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                    <i class="bi bi-calendar-event me-1"></i>Filter Tanggal
                </label>
                <input type="date" name="date" id="filterDate" class="form-control" value="{{ request('date', now()->format('Y-m-d')) }}" style="border-radius: 10px;">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-pink">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
            @if(request('date') && request('date') != now()->format('Y-m-d'))
            <div class="col-auto">
                <a href="{{ route('repair.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Hari Ini
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Recently Finished Repairs -->
    <div class="glass-card slide-up" style="animation-delay: 0.15s;">
        <div class="card-header-pink" style="background: linear-gradient(135deg, #059669, #047857);">
            <i class="bi bi-clipboard-check me-2"></i>Perbaikan Selesai
            <span class="badge bg-white text-dark ms-2" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse(request('date', now()->format('Y-m-d')))->format('d M Y') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium mb-0">
                    <thead style="background: linear-gradient(135deg, #059669, #047857);">
                        <tr class="text-nowrap">
                            <th>No Instruksi</th>
                            <th>Type Traktor</th>
                            <th>Kategori</th>
                            <th>PIC</th>
                            <th>Keterangan</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Total Jam</th>
                            <th class="text-center">Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent as $r)
                        <tr>
                            <td><span class="badge-pink">{{ $r->No_Instruksi ?? $r->Id_Traktor ?? '-' }}</span></td>
                            <td>{{ $r->Type_Traktor ?? '-' }}</td>
                            <td>
                                @if($r->Kategori_Perbaikan)
                                <span class="badge" style="background: var(--pink-100); color: var(--pink-700); border-radius: 8px; font-size: 0.78rem; padding: 0.3rem 0.6rem;">{{ $r->Kategori_Perbaikan }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <i class="bi bi-person-circle text-muted me-1"></i>
                                {{ $r->Nama_PIC ?? $r->Id_Member }}
                            </td>
                            <td class="text-truncate" style="max-width: 200px;">{{ $r->Ket_Perbaikan }}</td>
                            <td>
                                <i class="bi bi-clock text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($r->Jam_Start)->format('H:i:s') }}
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($r->Jam_Start)->format('d M Y') }}</small>
                            </td>
                            <td>
                                <i class="bi bi-clock-history text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($r->Jam_Finish)->format('H:i:s') }}
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($r->Jam_Finish)->format('d M Y') }}</small>
                            </td>
                            <td><span class="badge-success-soft">{{ $r->Total_Jam }}</span></td>
                            <td class="text-center">
                                @if($r->Photo_Path_Perbaikan)
                                <img src="{{ asset('storage/' . $r->Photo_Path_Perbaikan) }}"
                                    alt="Foto"
                                    class="rounded-2"
                                    style="width: 48px; height: 48px; object-fit: cover; cursor: pointer; border: 2px solid var(--pink-200); transition: transform 0.2s;"
                                    onmouseover="this.style.transform='scale(1.1)'"
                                    onmouseout="this.style.transform='scale(1)'"
                                    data-photo="{{ asset('storage/' . $r->Photo_Path_Perbaikan) }}"
                                    data-no-instruksi="{{ $r->No_Instruksi ?? $r->Id_Traktor ?? '-' }}"
                                    data-type-traktor="{{ $r->Type_Traktor ?? '-' }}"
                                    data-kategori="{{ $r->Kategori_Perbaikan ?? '-' }}"
                                    onclick="openPhotoModal(this)">
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada riwayat perbaikan yang selesai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header border-0"
                style="background: linear-gradient(135deg, var(--pink-500, #ec4899), var(--pink-700, #be185d)); color: white;">
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

@foreach($ongoing as $p)
<!-- Edit Repair Modal -->
<div class="modal fade" id="editRepairModal{{ $p->Id_Perbaikan }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('repair.update', $p->Id_Perbaikan) }}" method="POST" class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
            @csrf
            @method('PUT')
            <div class="modal-header border-0" style="background: linear-gradient(135deg, var(--pink-500), var(--pink-700)); color: white;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Edit Permasalahan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #fdf4ff;">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Traktor</label>
                    <input type="text" class="form-control" value="{{ $p->No_Instruksi }} / {{ $p->Type_Traktor }}" readonly style="border-radius: 10px; background: #e9ecef;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Kategori Permasalahan</label>
                    <select name="Kategori_Perbaikan" class="form-select" required style="border-radius: 10px;">
                        @php
                        $kategoriList = [
                            'Lecet', 'Part Kurang Dst', 'Part Kurang Painting', 'Part Kurang Assembling',
                            'Part NG (di NG kan oleh Produksi)', 'NG Part (NG Dari Supplier)',
                            'Pengencangan', 'Penyetelan', 'Perakitan', 'Susah/Sulit Rakit', 'Checksheet', 'Lain-lain'
                        ];
                        // If it's a custom 'Lain-lain: xxx', we select 'Lain-lain' or keep it as custom
                        $isCustom = !in_array($p->Kategori_Perbaikan, $kategoriList);
                        @endphp
                        @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ ($p->Kategori_Perbaikan == $kat) || ($kat == 'Lain-lain' && $isCustom) ? 'selected' : '' }}>
                            {{ $kat }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Keterangan / Detail</label>
                    <textarea class="form-control" name="Ket_Perbaikan" required style="border-radius: 10px; height: 100px;">{{ $p->Ket_Perbaikan }}</textarea>
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

@push('scripts')
<script>
    function hapusData(id, btn) {
        if (!confirm('Yakin ingin menghapus data perbaikan ini?')) return;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('{{ url("repair") }}/' + id, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const row = btn.closest('tr');
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                } else {
                    alert(data.message || 'Gagal menghapus data.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-x-lg"></i>';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error koneksi server.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-x-lg"></i>';
            });
    }

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
</script>
@endpush
@endsection