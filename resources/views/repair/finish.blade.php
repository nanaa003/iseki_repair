@extends('layouts.app')

@section('content')
<div class="hero-banner" style="background: linear-gradient(135deg, #059669, #047857, #065f46);">
    <h1><i class="bi bi-check2-all me-2"></i>Selesaikan Perbaikan</h1>
    <p>Scan NIK PIC untuk menyelesaikan pekerjaan perbaikan traktor.</p>
</div>
<div class="container pb-5">
    <div class="mb-3">
        <a href="{{ route('repair.index') }}" class="btn btn-pink-outline">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Info Box -->
    <div class="glass-card mb-4 fade-in" style="border-left: 4px solid #059669;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-success mb-3"><i class="bi bi-info-circle me-1"></i>Detail Perbaikan</h6>
            <div class="row">
                <div class="col-12 col-md-3 mb-2">
                    <div class="small text-muted text-uppercase fw-bold">No Instruksi</div>
                    <div class="fs-5 fw-bold" style="color: var(--pink-600);">{{ $perbaikan->No_Instruksi ?? $perbaikan->Id_Traktor ?? '-' }}</div>
                </div>
                <div class="col-12 col-md-3 mb-2">
                    <div class="small text-muted text-uppercase fw-bold">Type Traktor</div>
                    <div class="fs-5 fw-bold" style="color: var(--pink-600);">{{ $perbaikan->Type_Traktor ?? '-' }}</div>
                </div>
                <div class="col-12 col-md-3 mb-2">
                    <div class="small text-muted text-uppercase fw-bold">Waktu Mulai</div>
                    <div class="fw-semibold">
                        <i class="bi bi-clock text-muted me-1"></i>
                        {{ \Carbon\Carbon::parse($perbaikan->Jam_Start)->format('d M Y, H:i:s') }}
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <div class="small text-muted text-uppercase fw-bold">Keterangan</div>
                    <div class="bg-white p-3 border rounded-3 mt-1">{{ $perbaikan->Ket_Perbaikan }}</div>
                </div>
            </div>
        </div>
    </div>

    <form id="finishForm">
        <!-- Step 3: NIK / PIC -->
        <div class="glass-card slide-up" id="step3">
            <div class="card-header-pink" style="background: linear-gradient(135deg, #059669, #047857);">
                <i class="bi bi-person-badge me-2"></i>Scan Barcode NIK Anggota (PIC)
            </div>
            <div class="card-body text-center p-4">
                
                <!-- Nav tabs for Scan / Manual -->
                <ul class="nav nav-pills nav-fill mb-4" id="scanNikTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="camera-nik-tab" data-bs-toggle="tab" data-bs-target="#camera-nik" type="button" role="tab">
                            <i class="bi bi-camera me-1"></i>Scan Kamera
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="manual-nik-tab" data-bs-toggle="tab" data-bs-target="#manual-nik" type="button" role="tab">
                            <i class="bi bi-keyboard me-1"></i>Scanner / Ketik Manual
                        </button>
                    </li>
                </ul>

                <style>
                    /* Custom style for active nav pills in finish page */
                    #scanNikTab .nav-link {
                        color: #059669;
                    }
                    #scanNikTab .nav-link.active {
                        background-color: #059669 !important;
                        color: white !important;
                    }
                </style>

                <div class="tab-content" id="scanNikTabContent">
                    {{-- Camera Scan Tab --}}
                    <div class="tab-pane fade show active" id="camera-nik" role="tabpanel">
                        <div id="reader-pic" style="width: 100%; max-width: 400px; margin: 0 auto;" class="mb-4 rounded-3 overflow-hidden shadow-sm"></div>
                    </div>

                    {{-- Manual / Scanner Tab --}}
                    <div class="tab-pane fade" id="manual-nik" role="tabpanel">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control form-control-lg" id="manual_nik_input" placeholder="Scan Barcode / Ketik Manual..." style="border-radius: 12px; border-color: #059669;" autofocus>
                            <label for="manual_nik_input"><i class="bi bi-upc-scan me-1"></i>Input NIK Barcode</label>
                        </div>
                        <button type="button" class="btn w-100 btn-lg text-white" id="btnProsesManualNik" style="background-color: #059669; border-radius: 12px;">
                            <i class="bi bi-search me-1"></i>Proses NIK
                        </button>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row text-start justify-content-center">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">
                            <i class="bi bi-person-vcard me-1"></i>ID Member (NIK)
                        </label>
                        <input type="text" id="Id_Member" name="Id_Member" class="form-control form-control-lg text-center fw-bold" style="color: var(--pink-600); font-size: 1.1rem;" readonly placeholder="Menunggu Scan...">
                    </div>
                </div>
                <div class="row text-start justify-content-center">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">
                            <i class="bi bi-person-circle me-1"></i>Nama PIC
                        </label>
                        <input type="text" id="Nama_Member" class="form-control form-control-lg text-center fw-bold text-success mb-2" readonly placeholder="—">
                        <button class="btn btn-outline-danger w-100 d-none" type="button" id="btnRescan" style="border-radius: 8px;">
                            <i class="bi bi-arrow-repeat me-1"></i>Pindai Ulang (Rescan)
                        </button>
                    </div>
                </div>
                <div class="row text-start justify-content-center">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label fw-bold small text-uppercase" style="color: #dc2626;">
                            <i class="bi bi-clock-history me-1"></i>Jam Finish (Selesai)
                        </label>
                        <input type="text" id="Jam_Finish" name="Jam_Finish" class="form-control form-control-lg text-center fw-bold text-danger" readonly placeholder="—">
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-lg px-5 shadow fw-bold w-100" id="btnSubmit" disabled
                        style="background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 12px; padding: 0.85rem;">
                        <i class="bi bi-check-circle me-2"></i>Konfirmasi Selesai
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const Id_Member = document.getElementById('Id_Member');
        const Nama_Member = document.getElementById('Nama_Member');
        const Jam_Finish = document.getElementById('Jam_Finish');

        const getNowStr = () => {
            const now = new Date();
            const pad = n => n < 10 ? '0' + n : n;
            return now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + ' ' +
                pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        };

        // QR Scanner for NIK
        const html5QrcodeScannerPIC = new Html5QrcodeScanner("reader-pic", {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            },
            rememberLastUsedCamera: true,
        }, false);

        function processBarcodeNIK(decodedText) {
            try {
                html5QrcodeScannerPIC.clear();
            } catch (e) {}

            Id_Member.value = decodedText;
            Nama_Member.value = "Mencari data...";
            Jam_Finish.value = getNowStr();
            Nama_Member.className = "form-control form-control-lg text-center fw-bold text-success";
            document.getElementById('btnRescan').classList.add('d-none');
            document.getElementById('btnSubmit').disabled = true;

            // Lookup nama member from rifa DB
            fetch("{{ route('repair.verifyNik') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nik: decodedText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Nama_Member.value = data.nama;
                        Id_Member.value = data.nik;
                        document.getElementById('btnSubmit').disabled = false;
                        document.getElementById('btnSubmit').click();
                    } else {
                        Nama_Member.value = "NIK TIDAK ADA (" + decodedText + ")";
                        Nama_Member.className = "form-control form-control-lg text-center fw-bold text-danger mb-2 border-danger";
                        Id_Member.value = "";
                        Jam_Finish.value = "";
                        document.getElementById('btnRescan').classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error verifying NIK:', error);
                    Nama_Member.value = "ERROR KONEKSI / SERVER";
                    Nama_Member.className = "form-control form-control-lg text-center fw-bold text-danger";
                    Id_Member.value = "";
                    Jam_Finish.value = "";
                    document.getElementById('btnRescan').classList.remove('d-none');
                });
        }

        function onScanSuccess(decodedText) {
            processBarcodeNIK(decodedText);
        }

        document.getElementById('btnProsesManualNik').addEventListener('click', function() {
            const val = document.getElementById('manual_nik_input').value.trim();
            if(val) {
                processBarcodeNIK(val);
            }
        });

        document.getElementById('manual_nik_input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('btnProsesManualNik').click();
            }
        });

        html5QrcodeScannerPIC.render(onScanSuccess);

        document.getElementById('btnRescan').addEventListener('click', function() {
            Id_Member.value = "";
            Nama_Member.value = "";
            Jam_Finish.value = "";
            targetFile = "";
            Nama_Member.className = "form-control form-control-lg text-center fw-bold text-success";
            this.classList.add('d-none');
            // Re-render scanner
            html5QrcodeScannerPIC.render(onScanSuccess);
        });

        // Submit
        document.getElementById('btnSubmit').addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            const formData = new FormData();
            formData.append('Id_Member', Id_Member.value);
            formData.append('Nama_PIC', Nama_Member.value);
            formData.append('Jam_Finish', Jam_Finish.value);
            formData.append('_token', "{{ csrf_token() }}");

            fetch("{{ route('repair.updateFinish', $perbaikan->Id_Perbaikan) }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(res => {
                    if (!res.ok) {
                        return res.json().then(err => {
                            const msg = err.message || (err.errors ? Object.values(err.errors).flat().join('\n') : 'Validation error');
                            throw new Error(msg);
                        }).catch(e => {
                            throw e.message ? e : new Error('Server error: ' + res.status);
                        });
                    }
                    return res.json();
                })
                .then(res => {
                    if (res.success) {
                        window.location.href = "{{ route('repair.index') }}";
                    } else {
                        alert(res.message || "Error saving data");
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Konfirmasi Selesai';
                    }
                }).catch(e => {
                    console.error(e);
                    alert("Error: " + e.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Konfirmasi Selesai';
                });
        });
    });
</script>
@endpush