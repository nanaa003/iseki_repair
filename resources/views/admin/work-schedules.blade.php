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
                    <a class="nav-link active" href="{{ route('admin.work-schedules.index') }}">
                        <i class="bi bi-clock-history me-1"></i>Jam Kerja
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

    <h4 class="fw-bold mb-1">
        <i class="bi bi-clock-history me-2" style="color: var(--pink-600);"></i>Pengaturan Jam Kerja
    </h4>
    <p class="text-muted mb-4">Atur jam kerja per tanggal untuk perhitungan MTTR yang akurat. Tanggal tanpa pengaturan manual akan menggunakan jam default.</p>

    <!-- Legenda & Default Info -->
    <div class="glass-card mb-4 p-3">
        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1 text-primary"></i>Jam Kerja Default</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 0.82rem;">
                        <i class="bi bi-calendar-week me-1"></i>Sen-Kam: <strong>07:30 – 16:30</strong>
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 0.82rem;">
                        <i class="bi bi-calendar-week me-1"></i>Jumat: <strong>07:30 – 17:00</strong>
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 0.82rem;">
                        <i class="bi bi-calendar-x me-1"></i>Sab-Min: <strong>Libur (–)</strong>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold mb-2"><i class="bi bi-palette me-1 text-primary"></i>Keterangan Warna</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge px-3 py-2" style="background: #dbeafe; color: #1e40af; font-size: 0.82rem;">
                        <i class="bi bi-pencil-square me-1"></i>Override Manual
                    </span>
                    <span class="badge px-3 py-2" style="background: #fee2e2; color: #991b1b; font-size: 0.82rem;">
                        <i class="bi bi-calendar-x me-1"></i>Libur (Rifa)
                    </span>
                    <span class="badge px-3 py-2" style="background: #d1fae5; color: #065f46; font-size: 0.82rem;">
                        <i class="bi bi-calendar-check me-1"></i>Libur Masuk (Rifa)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Navigation -->
    <div class="glass-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.work-schedules.index') }}" id="monthForm" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                    <i class="bi bi-calendar-month me-1"></i>Bulan
                </label>
                <div class="input-group" style="border-radius: 10px; overflow: hidden;">
                    <button type="button" id="prevMonthBtn" class="btn btn-outline-secondary">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <input type="month" name="month" id="monthInput" class="form-control text-center"
                        value="{{ $month }}" style="min-width: 170px;">
                    <button type="button" id="nextMonthBtn" class="btn btn-outline-secondary">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-pink">
                    <i class="bi bi-search me-1"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Calendar Grid -->
    <div class="glass-card p-3">
        <div class="table-responsive">
            <table class="table table-bordered mb-0" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th class="text-center py-2" style="background: var(--pink-100); color: var(--pink-700); width: 5%;">Tgl</th>
                        <th class="text-center py-2" style="background: var(--pink-100); color: var(--pink-700); width: 9%;">Hari</th>
                        <th class="text-center py-2" style="background: var(--pink-100); color: var(--pink-700); width: 13%;">Jam Default</th>
                        <th class="text-center py-2" style="background: var(--pink-100); color: var(--pink-700); width: 13%;">Override</th>
                        <th class="text-center py-2" style="background: var(--pink-100); color: var(--pink-700); width: 18%;">Info Rifa</th>
                        <th class="text-center py-2" style="background: var(--pink-100); color: var(--pink-700); width: 20%;">Keterangan</th>
                        <th class="text-center py-2" style="background: var(--pink-100); color: var(--pink-700); width: 22%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($calendarDays as $day)
                    @php
                        $rowBg = '';
                        $rowClass = '';
                        if ($day['special_date'] && in_array($day['special_date']['jenis'], ['libur nasional', 'cuti perusahaan', 'libur pengganti'])) {
                            $rowBg = 'background: #fef2f2;'; // merah muda
                        } elseif ($day['special_date'] && $day['special_date']['jenis'] === 'libur masuk') {
                            $rowBg = 'background: #ecfdf5;'; // hijau muda
                        } elseif ($day['override']) {
                            $rowBg = 'background: #eff6ff;'; // biru muda
                        } elseif ($day['is_weekend']) {
                            $rowBg = 'background: #f9fafb;'; // abu-abu muda
                        }
                    @endphp
                    <tr style="{{ $rowBg }}" data-date="{{ $day['date'] }}">
                        <td class="text-center align-middle fw-bold">{{ $day['day'] }}</td>
                        <td class="text-center align-middle">
                            <span class="{{ $day['is_weekend'] ? 'text-danger fw-bold' : '' }}">
                                {{ $day['day_name'] }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            @if($day['default_mulai'] === '-')
                                <span class="text-muted">—</span>
                            @else
                                <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                                    {{ $day['default_mulai'] }} – {{ $day['default_selesai'] }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if($day['override'])
                                @if($day['override']['is_libur'])
                                    <span class="badge px-2 py-1" style="background: #fee2e2; color: #991b1b; font-size: 0.8rem;">
                                        <i class="bi bi-calendar-x me-1"></i>Libur (Manual)
                                    </span>
                                @else
                                    <span class="badge px-2 py-1" style="background: #dbeafe; color: #1e40af; font-size: 0.8rem;">
                                        <i class="bi bi-pencil-square me-1"></i>{{ $day['override']['jam_mulai'] }} – {{ $day['override']['jam_selesai'] }}
                                    </span>
                                @endif
                            @else
                                <span class="text-muted small">Default</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if($day['special_date'])
                                @php
                                    $jenis = $day['special_date']['jenis'];
                                    $badgeStyle = match(true) {
                                        $jenis === 'libur masuk' => 'background: #d1fae5; color: #065f46;',
                                        default => 'background: #fee2e2; color: #991b1b;',
                                    };
                                    $icon = match(true) {
                                        $jenis === 'libur masuk' => 'bi-calendar-check',
                                        default => 'bi-calendar-x',
                                    };
                                @endphp
                                <span class="badge px-2 py-1" style="{{ $badgeStyle }} font-size: 0.78rem;">
                                    <i class="bi {{ $icon }} me-1"></i>{{ ucwords($jenis) }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="align-middle">
                            @if($day['override'] && $day['override']['keterangan'])
                                <small class="text-muted">{{ $day['override']['keterangan'] }}</small>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-pink-outline me-1 btn-set-schedule"
                                data-date="{{ $day['date'] }}"
                                data-day-name="{{ $day['day_name'] }}"
                                data-current-is-libur="{{ $day['override'] && $day['override']['is_libur'] ? '1' : '0' }}"
                                data-current-mulai="{{ $day['override'] ? $day['override']['jam_mulai'] : ($day['default_mulai'] !== '-' ? $day['default_mulai'] : '07:30') }}"
                                data-current-selesai="{{ $day['override'] ? $day['override']['jam_selesai'] : ($day['default_selesai'] !== '-' ? $day['default_selesai'] : '16:30') }}"
                                data-current-ket="{{ $day['override'] ? $day['override']['keterangan'] : '' }}">
                                <i class="bi bi-pencil me-1"></i>Atur
                            </button>
                            @if($day['override'])
                            <button type="button" class="btn btn-sm btn-outline-danger btn-reset-schedule"
                                data-id="{{ $day['override']['id'] }}"
                                data-date="{{ $day['date'] }}">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Atur Jam Kerja -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header border-0"
                style="background: linear-gradient(135deg, var(--pink-500), var(--pink-700)); color: white;">
                <div>
                    <h6 class="modal-title fw-bold mb-0">
                        <i class="bi bi-clock me-2"></i>Atur Jam Kerja
                    </h6>
                    <small class="opacity-75" id="modalDateLabel"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background: #fdf4ff;">
                <form id="scheduleForm">
                    <input type="hidden" id="formTanggal" name="tanggal">
                    
                    <div class="form-check mb-3 form-switch">
                        <input class="form-check-input" type="checkbox" id="formIsLibur">
                        <label class="form-check-label fw-bold text-danger" for="formIsLibur">
                            Jadikan Hari Libur (Tidak dihitung)
                        </label>
                    </div>

                    <div class="row g-3 mb-3" id="timeInputsContainer">
                        <div class="col-6">
                            <label class="form-label fw-bold small">
                                <i class="bi bi-sunrise me-1 text-warning"></i>Jam Mulai
                            </label>
                            <input type="time" class="form-control" id="formJamMulai" name="jam_mulai" required
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">
                                <i class="bi bi-sunset me-1 text-danger"></i>Jam Selesai
                            </label>
                            <input type="time" class="form-control" id="formJamSelesai" name="jam_selesai" required
                                style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">
                            <i class="bi bi-chat-left-text me-1 text-info"></i>Keterangan (Opsional)
                        </label>
                        <input type="text" class="form-control" id="formKeterangan" name="keterangan"
                            placeholder="Misal: Lembur produksi unit 200" style="border-radius: 10px;">
                    </div>
                    <!-- Preview -->
                    <div class="alert alert-light border mb-0 small" id="previewBox">
                        <i class="bi bi-eye me-1"></i><strong>Preview:</strong>
                        <span id="previewText">–</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0" style="background: #fdf4ff;">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-pink" id="btnSaveSchedule">
                    <i class="bi bi-check-lg me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    <div id="scheduleToast" class="toast align-items-center text-white border-0" role="alert"
        style="border-radius: 12px; background: linear-gradient(135deg, var(--pink-500), var(--pink-700));">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));

    // ── Month Navigation ────────────────────────────────────────────────────
    const monthInput = document.getElementById('monthInput');
    const monthForm = document.getElementById('monthForm');

    function shiftMonth(delta) {
        if (!monthInput.value) return;
        const parts = monthInput.value.split('-');
        const d = new Date(+parts[0], +parts[1] - 1, 1);
        d.setMonth(d.getMonth() + delta);
        monthInput.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
        monthForm.submit();
    }

    document.getElementById('prevMonthBtn').addEventListener('click', () => shiftMonth(-1));
    document.getElementById('nextMonthBtn').addEventListener('click', () => shiftMonth(1));

    // ── Open Modal ──────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-set-schedule').forEach(btn => {
        btn.addEventListener('click', function () {
            const date = this.dataset.date;
            const dayName = this.dataset.dayName;
            const isLibur = this.dataset.currentIsLibur === '1';
            const mulai = this.dataset.currentMulai;
            const selesai = this.dataset.currentSelesai;
            const ket = this.dataset.currentKet;

            document.getElementById('formTanggal').value = date;
            document.getElementById('formIsLibur').checked = isLibur;
            document.getElementById('formJamMulai').value = mulai;
            document.getElementById('formJamSelesai').value = selesai;
            document.getElementById('formKeterangan').value = ket || '';
            document.getElementById('modalDateLabel').textContent = dayName + ', ' + formatDate(date);
            
            toggleTimeInputs();
            updatePreview();
            modal.show();
        });
    });

    // ── Toggle Libur Checkbox ───────────────────────────────────────────────
    function toggleTimeInputs() {
        const isLibur = document.getElementById('formIsLibur').checked;
        const timeContainer = document.getElementById('timeInputsContainer');
        const inputMulai = document.getElementById('formJamMulai');
        const inputSelesai = document.getElementById('formJamSelesai');
        
        if (isLibur) {
            timeContainer.style.opacity = '0.5';
            timeContainer.style.pointerEvents = 'none';
            inputMulai.required = false;
            inputSelesai.required = false;
        } else {
            timeContainer.style.opacity = '1';
            timeContainer.style.pointerEvents = 'auto';
            inputMulai.required = true;
            inputSelesai.required = true;
        }
        updatePreview();
    }
    
    document.getElementById('formIsLibur').addEventListener('change', toggleTimeInputs);

    // ── Preview ─────────────────────────────────────────────────────────────
    function updatePreview() {
        if (document.getElementById('formIsLibur').checked) {
            document.getElementById('previewText').innerHTML = '<span class="text-danger fw-bold">Libur (Tidak dihitung dalam MTTR)</span>';
            return;
        }

        const mulai = document.getElementById('formJamMulai').value;
        const selesai = document.getElementById('formJamSelesai').value;
        if (mulai && selesai) {
            const [mH, mM] = mulai.split(':').map(Number);
            const [sH, sM] = selesai.split(':').map(Number);
            const totalMins = (sH * 60 + sM) - (mH * 60 + mM);
            
            if (totalMins > 0) {
                const hours = Math.floor(totalMins / 60);
                const mins = totalMins % 60;
                document.getElementById('previewText').textContent =
                    mulai + ' – ' + selesai + ' (' + hours + ' jam ' + mins + ' menit kerja)';
            } else {
                document.getElementById('previewText').textContent = 'Jam tidak valid (selesai harus lebih besar)';
            }
        } else {
            document.getElementById('previewText').textContent = '–';
        }
    }

    document.getElementById('formJamMulai').addEventListener('change', updatePreview);
    document.getElementById('formJamSelesai').addEventListener('change', updatePreview);

    // ── Save Schedule ───────────────────────────────────────────────────────
    document.getElementById('btnSaveSchedule').addEventListener('click', function () {
        const tanggal = document.getElementById('formTanggal').value;
        const isLibur = document.getElementById('formIsLibur').checked;
        const jamMulai = document.getElementById('formJamMulai').value;
        const jamSelesai = document.getElementById('formJamSelesai').value;
        const ket = document.getElementById('formKeterangan').value;

        if (!isLibur) {
            if (!jamMulai || !jamSelesai) {
                showToast('Jam mulai dan selesai harus diisi!', true);
                return;
            }

            if (jamMulai >= jamSelesai) {
                showToast('Jam selesai harus lebih besar dari jam mulai!', true);
                return;
            }
        }

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

        fetch("{{ route('admin.work-schedules.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                tanggal: tanggal,
                is_libur: isLibur,
                jam_mulai: jamMulai,
                jam_selesai: jamSelesai,
                keterangan: ket || null,
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                modal.hide();
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(data.message || 'Gagal menyimpan', true);
            }
        })
        .catch(() => showToast('Terjadi kesalahan jaringan.', true))
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-check-lg me-1"></i>Simpan';
        });
    });

    // ── Reset (Delete) Schedule ─────────────────────────────────────────────
    document.querySelectorAll('.btn-reset-schedule').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const date = this.dataset.date;

            if (!confirm('Reset jam kerja tanggal ' + formatDate(date) + ' ke default?')) return;

            fetch("{{ url('admin/work-schedules') }}/" + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    setTimeout(() => location.reload(), 800);
                }
            })
            .catch(() => showToast('Gagal menghapus.', true));
        });
    });

    // ── Helpers ──────────────────────────────────────────────────────────────
    function formatDate(dateStr) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const parts = dateStr.split('-');
        return parseInt(parts[2]) + ' ' + months[parseInt(parts[1]) - 1] + ' ' + parts[0];
    }

    function showToast(message, isError = false) {
        const toast = document.getElementById('scheduleToast');
        const toastMsg = document.getElementById('toastMessage');
        toastMsg.textContent = message;
        if (isError) {
            toast.style.background = 'linear-gradient(135deg, #dc2626, #991b1b)';
        } else {
            toast.style.background = 'linear-gradient(135deg, var(--pink-500), var(--pink-700))';
        }
        new bootstrap.Toast(toast, { delay: 3000 }).show();
    }
});
</script>
@endsection
