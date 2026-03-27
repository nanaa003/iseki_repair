@extends('layouts.app')

<!-- CSS TUI Image Editor -->
<link href="{{ asset('assets/css/tui-image-editor.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/tui-color-picker.css') }}" rel="stylesheet">

<style>
    /* Styling TUI Editor UI agar pas */
    #tui-editor-container {
        height: calc(100vh - 120px);
    }

    .tie-btn-history,
    .tie-btn-reset,
    .tie-btn-deleteAll,
    .tie-color-fill,
    .triangle,
    .circle,
    .tie-icon-add-button,
    .tui-image-editor-partition {
        display: none !important;
    }
</style>

@section('content')
<div class="hero-banner">
    <h1><i class="bi bi-qr-code-scan me-2"></i>Input Permasalahan Baru</h1>
    <p>Scan QR Traktor dan isi keterangan kerusakan.</p>
</div>
<div class="container pb-5">
    <div class="mb-3">
        <a href="{{ route('repair.index') }}" class="btn btn-pink-outline">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <form id="repairForm" enctype="multipart/form-data">

        {{-- ===================== STEP 1: SCAN TRAKTOR ===================== --}}
        <div class="glass-card mb-4 fade-in" id="step1">
            <div class="card-header-pink">
                <i class="bi bi-1-circle-fill me-2"></i>Step 1: Scan Traktor
            </div>
            <div class="card-body text-center p-4">
                <div id="reader-tractor" style="width: 100%; max-width: 400px; margin: 0 auto;" class="mb-3 rounded-3 overflow-hidden shadow-sm"></div>

                <div class="row text-start justify-content-center mt-3">
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">
                            <i class="bi bi-hash me-1"></i>No Instruksi
                        </label>
                        <input type="text" id="No_Instruksi" name="No_Instruksi"
                            class="form-control form-control-lg text-center fw-bold"
                            style="color: var(--pink-600); font-size: 1.25rem;"
                            readonly placeholder="Menunggu Scan...">
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">
                            <i class="bi bi-truck me-1"></i>Type Traktor
                        </label>
                        <input type="text" id="Type_Traktor" name="Type_Traktor"
                            class="form-control form-control-lg text-center fw-bold"
                            style="color: var(--pink-600); font-size: 1.25rem;"
                            readonly placeholder="Menunggu Scan...">
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">
                            <i class="bi bi-clock me-1"></i>Waktu Mulai
                        </label>
                        <input type="text" id="Jam_Start" name="Jam_Start"
                            class="form-control form-control-lg text-center"
                            readonly placeholder="Menunggu Scan...">
                    </div>
                </div>

                <button type="button" class="btn btn-outline-danger btn-lg w-100 mt-2 d-none" id="btnRescanTractor" style="border-radius: 12px;">
                    <i class="bi bi-arrow-repeat me-2"></i>Pindai Ulang Barcode Traktor
                </button>

                <button type="button" class="btn btn-pink btn-lg w-100 mt-2" id="btnNext1" disabled>
                    Lanjut ke Foto <i class="bi bi-arrow-right-circle ms-2"></i>
                </button>
            </div>
        </div>

        {{-- ===================== STEP 2: FOTO ===================== --}}
        <div class="glass-card mb-4 d-none slide-up" id="step2">
            <div class="card-header-pink">
                <i class="bi bi-2-circle-fill me-2"></i>Step 2: Foto Bukti Permasalahan
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Ambil atau upload foto kondisi traktor / kerusakan sebagai dokumentasi.
                </p>

                <div id="photoUploadArea"
                    class="border rounded-3 p-4 text-center mb-3"
                    style="border: 2px dashed var(--pink-300) !important; background: var(--pink-50); cursor: pointer; border-radius: 16px !important; transition: all 0.2s;"
                    onclick="document.getElementById('photoInput').click()">
                    <i class="bi bi-camera-fill" style="font-size: 2.5rem; color: var(--pink-400);"></i>
                    <p class="mt-2 mb-0 fw-semibold" style="color: var(--pink-600);">Tap untuk ambil/pilih foto</p>
                    <small class="text-muted">Format JPG, PNG</small>
                </div>

                <input type="file" id="photoInput" name="photo" accept="image/*" capture="environment" class="d-none">

                <div id="photoPreviewContainer" class="d-none mb-3 text-center">
                    <img id="photoPreview" src="" alt="Preview"
                        class="img-fluid rounded-3 shadow-sm"
                        style="max-height: 300px; object-fit: contain; border: 2px solid var(--pink-200);">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary me-2" id="btnEditPhoto" style="border-radius: 8px;">
                            <i class="bi bi-pencil-square me-1"></i>Edit Foto
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnRemovePhoto" style="border-radius: 8px;">
                            <i class="bi bi-trash me-1"></i>Hapus Foto
                        </button>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-outline-secondary flex-fill" id="btnBack1" style="border-radius: 12px;">
                        <i class="bi bi-arrow-left-circle me-1"></i>Kembali
                    </button>
                    <button type="button" class="btn btn-pink flex-fill" id="btnNext2">
                        Lanjut ke Keterangan <i class="bi bi-arrow-right-circle ms-2"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===================== STEP 3: KETERANGAN ===================== --}}
        <div class="glass-card mb-4 d-none slide-up" id="step3">
            <div class="card-header-pink">
                <i class="bi bi-3-circle-fill me-2"></i>Step 3: Keterangan Permasalahan
            </div>
            <div class="card-body p-4">
                <ul class="nav nav-pills nav-fill nav-pills-pink mb-4" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="ocr-tab" data-bs-toggle="tab" data-bs-target="#ocr" type="button" role="tab">
                            <i class="bi bi-camera me-1"></i>Foto / OCR Text
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab">
                            <i class="bi bi-keyboard me-1"></i>Ketik Manual
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    {{-- Manual Tab --}}
                    <div class="tab-pane fade" id="manual" role="tabpanel">
                        <div class="form-floating">
                            <textarea class="form-control" id="Ket_Perbaikan_Manual"
                                placeholder="Ketik keterangan di sini..."
                                style="height: 140px; border-radius: 12px;"></textarea>
                            <label for="Ket_Perbaikan_Manual"><i class="bi bi-pencil-square me-1"></i>Keterangan Kerusakan</label>
                        </div>
                    </div>

                    {{-- OCR Tab --}}
                    <div class="tab-pane fade show active" id="ocr" role="tabpanel">
                        <div class="alert py-2 mb-3" style="background: var(--pink-50); border: 1px solid var(--pink-200); color: var(--pink-700); border-radius: 12px;">
                            <i class="bi bi-info-circle me-1"></i>
                            <small>Upload foto sparepart/catatan kerusakan → pilih area yang ingin di-OCR → ekstrak teks otomatis.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">
                                <i class="bi bi-image me-1"></i>Pilih Foto
                            </label>
                            <input type="file" id="imageInput" class="form-control" accept="image/*" capture="environment" style="border-radius: 12px;">
                        </div>

                        <div style="max-height: 400px; overflow: hidden; display: none;" id="cropperContainer" class="rounded-3 shadow-sm mb-3 text-center bg-light p-2 border">
                            <img id="imageToCrop" style="max-width: 100%;">
                        </div>

                        <button type="button" class="btn btn-pink w-100 mb-3 d-none" id="btnExtractText">
                            <i class="bi bi-magic me-2"></i>Ekstrak Teks dari Area Terpilih
                        </button>

                        <div id="ocrProgressContainer" class="mb-3 d-none">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="fw-bold text-muted">Memproses OCR...</small>
                                <small class="fw-bold" id="ocrPercent" style="color: var(--pink-600);">0%</small>
                            </div>
                            <div class="ocr-progress">
                                <div class="ocr-progress-bar" id="ocrProgressBar" style="width: 0%"></div>
                            </div>
                        </div>

                        <div class="form-floating">
                            <textarea class="form-control" id="Ket_Perbaikan_OCR"
                                placeholder="Hasil OCR..."
                                style="height: 140px; border-radius: 12px; border-color: var(--pink-300);"></textarea>
                            <label for="Ket_Perbaikan_OCR"><i class="bi bi-file-text me-1"></i>Hasil Ekstrak OCR</label>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="Ket_Perbaikan" name="Ket_Perbaikan">

                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-outline-secondary flex-fill" id="btnBack2" style="border-radius: 12px;">
                        <i class="bi bi-arrow-left-circle me-1"></i>Kembali
                    </button>
                    <button type="button" class="btn btn-pink flex-fill" id="btnNext3">
                        Lanjut ke Kategori <i class="bi bi-arrow-right-circle ms-2"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===================== STEP 4: KATEGORI ===================== --}}
        <div class="glass-card mb-4 d-none slide-up" id="step4">
            <div class="card-header-pink">
                <i class="bi bi-4-circle-fill me-2"></i>Step 4: Pilih Kategori Permasalahan
            </div>
            <div class="card-body p-4">

                <label class="form-label fw-bold text-muted small text-uppercase mb-3">
                    <i class="bi bi-tags me-1"></i>Pilih Kategori
                </label>

                <div class="row g-2 mb-4">
                    @php
                    $kategoriList = [
                    ['label' => 'Lecet', 'icon' => 'bi-bandage'],
                    ['label' => 'Part Kurang Dst', 'icon' => 'bi-box-seam'],
                    ['label' => 'Part Kurang Painting', 'icon' => 'bi-box-seam'],
                    ['label' => 'Part Kurang Assembling', 'icon' => 'bi-box-seam'],
                    ['label' => 'Part NG (di NG kan oleh Produksi)', 'icon' => 'bi-x-octagon'],
                    ['label' => 'NG Part (NG Dari Supplier)', 'icon' => 'bi-x-octagon'],
                    ['label' => 'Pengencangan', 'icon' => 'bi-wrench'],
                    ['label' => 'Penyetelan', 'icon' => 'bi-sliders'],
                    ['label' => 'Perakitan', 'icon' => 'bi-gear'],
                    ['label' => 'Susah/Sulit Rakit', 'icon' => 'bi-exclamation-triangle'],
                    ['label' => 'Checksheet', 'icon' => 'bi-clipboard-check'],
                    ['label' => 'Lain-lain', 'icon' => 'bi-three-dots'],
                    ];
                    @endphp

                    @foreach($kategoriList as $kat)
                    <div class="col-6 col-md-4">
                        <button type="button"
                            class="btn w-100 kategori-btn py-3"
                            data-kategori="{{ $kat['label'] }}"
                            style="border: 2px solid var(--pink-200); border-radius: 14px; background: white; transition: all 0.2s; text-align: left; padding-left: 1rem;">
                            <i class="bi {{ $kat['icon'] }} me-2" style="color: var(--pink-500);"></i>
                            <span class="fw-semibold" style="font-size: 0.9rem;">{{ $kat['label'] }}</span>
                        </button>
                    </div>
                    @endforeach
                </div>

                <input type="hidden" id="Kategori_Perbaikan" name="Kategori_Perbaikan">

                {{-- Textbox Lain-lain — hanya muncul kalau pilih Lain-lain --}}
                <div id="ketLainLainWrapper" class="d-none mb-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">
                        <i class="bi bi-pencil-square me-1"></i>Keterangan Tambahan (Lain-lain)
                    </label>
                    <div class="form-floating">
                        <textarea class="form-control" id="Ket_LainLain"
                            placeholder="Jelaskan permasalahan..."
                            style="height: 120px; border-radius: 12px;"></textarea>
                        <label for="Ket_LainLain">Jelaskan permasalahan secara detail...</label>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-outline-secondary flex-fill" id="btnBack3" style="border-radius: 12px;">
                        <i class="bi bi-arrow-left-circle me-1"></i>Kembali
                    </button>
                    <button type="button" class="btn btn-success btn-lg flex-fill p-3 shadow" id="btnSubmit" disabled>
                        <i class="bi bi-save me-2"></i>Simpan Laporan
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<!-- TUI Image Editor Modal -->
<div class="modal fade" id="tuiEditorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--pink-500), var(--pink-700)); color: white; border: none;">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Foto Permasalahan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 d-flex flex-column" style="min-height:0; background: #fdf4ff;">
                <div id="custom-tui-toolbar"
                    class="p-2 border-bottom d-flex flex-wrap justify-content-start align-items-center gap-2 bg-light shadow-sm">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" data-tool="draw" title="Gambar Bebas"><i class="bi bi-pencil"></i></button>
                        <button type="button" class="btn btn-outline-primary" data-tool="rect" title="Kotak (Highlight Area)"><i class="bi bi-square"></i></button>
                        <button type="button" class="btn btn-outline-primary" data-tool="arrow" title="Tanda Panah"><i class="bi bi-arrow-right"></i></button>
                        <button type="button" class="btn btn-outline-primary" data-tool="rotate" title="Putar 90°"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>
                    <div class="vr mx-3 d-none d-md-block"></div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-tool="undo" title="Batal (Undo)"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" class="btn btn-outline-secondary" data-tool="redo" title="Ulangi (Redo)"><i class="bi bi-arrow-clockwise"></i></button>
                        <button type="button" class="btn btn-outline-danger" data-tool="delete" title="Hapus Objek Terpilih"><i class="bi bi-trash"></i></button>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-success" id="tui-save-btn"><i class="bi bi-check-circle me-1"></i>Simpan Hasil Edit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
                <div id="tui-editor-container" class="d-flex justify-content-center align-items-center bg-dark"
                    style="flex:1; overflow:hidden;">
                    <div id="tui-image-editor" style="width:96%; height:96%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/tui-code-snippet.js') }}"></script>
<script src="{{ asset('assets/js/tui-color-picker.js') }}"></script>
<script src="{{ asset('assets/js/fabric.min.js') }}"></script>
<script src="{{ asset('assets/js/tui-image-editor.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const elNoInstruksi = document.getElementById('No_Instruksi');
        const elTypeTraktor = document.getElementById('Type_Traktor');
        const elJamStart = document.getElementById('Jam_Start');
        const elFinalKet = document.getElementById('Ket_Perbaikan');
        const elKategori = document.getElementById('Kategori_Perbaikan');
        const elKetLainLain = document.getElementById('Ket_LainLain');
        const elBtnSubmit = document.getElementById('btnSubmit');

        const getNowStr = () => {
            const now = new Date();
            const pad = n => n < 10 ? '0' + n : n;
            return now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + ' ' +
                pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        };

        const goTo = (from, to) => {
            document.getElementById(from).classList.add('d-none');
            document.getElementById(to).classList.remove('d-none');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };

        // ── STEP 1: QR Scanner ────────────────────────────────────────────────────
        const scanner = new Html5QrcodeScanner("reader-tractor", {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            },
            rememberLastUsedCamera: true
        }, false);

        function onScanSuccess(decodedText) {
            scanner.clear();
            elNoInstruksi.value = 'Memproses...';
            elTypeTraktor.value = 'Memproses...';

            fetch("{{ route('repair.verifyTractor') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        qr_data: decodedText
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        elNoInstruksi.value = data.no_instruksi;
                        elTypeTraktor.value = data.type_traktor;
                        elJamStart.value = getNowStr();
                        document.getElementById('btnNext1').disabled = false;
                        setTimeout(() => goTo('step1', 'step2'), 500);
                    } else {
                        elNoInstruksi.value = 'DATA TIDAK DITEMUKAN';
                        elNoInstruksi.className = 'form-control form-control-lg text-center fw-bold text-danger border-danger';
                        elNoInstruksi.style.color = '';
                        elTypeTraktor.value = data.message || 'Barcode tidak dikenali';
                        elTypeTraktor.className = 'form-control form-control-lg text-center fw-bold text-danger border-danger';
                        elTypeTraktor.style.color = '';
                        elJamStart.value = '';
                        document.getElementById('btnRescanTractor').classList.remove('d-none');
                        document.getElementById('btnNext1').disabled = true;
                    }
                })
                .catch(() => {
                    elNoInstruksi.value = 'ERROR KONEKSI / SERVER';
                    elNoInstruksi.className = 'form-control form-control-lg text-center fw-bold text-danger border-danger';
                    elNoInstruksi.style.color = '';
                    document.getElementById('btnRescanTractor').classList.remove('d-none');
                    document.getElementById('btnNext1').disabled = true;
                });
        }

        scanner.render(onScanSuccess, () => {});

        document.getElementById('btnRescanTractor').addEventListener('click', function() {
            elNoInstruksi.value = '';
            elTypeTraktor.value = '';
            elJamStart.value = '';
            elNoInstruksi.className = 'form-control form-control-lg text-center fw-bold';
            elNoInstruksi.style.color = 'var(--pink-600)';
            elTypeTraktor.className = 'form-control form-control-lg text-center fw-bold';
            elTypeTraktor.style.color = 'var(--pink-600)';
            elNoInstruksi.placeholder = 'Menunggu Scan...';
            elTypeTraktor.placeholder = 'Menunggu Scan...';
            this.classList.add('d-none');
            scanner.render(onScanSuccess, () => {});
        });

        document.getElementById('btnNext1').addEventListener('click', () => goTo('step1', 'step2'));

        // ── STEP 2: Foto & Editor ───────────────────────────────────────────────────
        const photoInput = document.getElementById('photoInput');
        const photoPreview = document.getElementById('photoPreview');
        const previewBox = document.getElementById('photoPreviewContainer');
        const uploadArea = document.getElementById('photoUploadArea');

        let tuiEditor = null;
        let editedImageData = null; // Menyimpan data gambar base64 hasil editan / original

        photoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    editedImageData = e.target.result;
                    photoPreview.src = e.target.result;
                    previewBox.classList.remove('d-none');
                    uploadArea.classList.add('d-none');
                };
                reader.onerror = function() {
                    alert('Gagal memproses gambar.');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        document.getElementById('btnRemovePhoto').addEventListener('click', function() {
            photoInput.value = '';
            photoPreview.src = '';
            editedImageData = null;
            previewBox.classList.add('d-none');
            uploadArea.classList.remove('d-none');
        });

        // ── Inisiasi TUI Editor ──
        function openTuiEditor(imageUrl) {
            const modalEl = document.getElementById('tuiEditorModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            modalEl.addEventListener('shown.bs.modal', () => {
                const container = document.getElementById('tui-image-editor');
                container.innerHTML = '';
                tuiEditor = new tui.ImageEditor(container, {
                    usageStatistics: false,
                    cssMaxWidth: 2000,
                    cssMaxHeight: 2000,
                });

                tuiEditor.loadImageFromURL(imageUrl, 'uploaded').then(() => {
                    const canvas = tuiEditor._graphics.getCanvas();
                    const img = canvas.getObjects()[0];
                    if (img) {
                        img.set({
                            originX: 'center',
                            originY: 'center',
                            left: canvas.getWidth() / 2,
                            top: canvas.getHeight() / 2
                        });
                        canvas.centerObject(img);
                        // Add shadow to free-drawing brush if it exists
                        if (canvas.freeDrawingBrush) {
                            canvas.freeDrawingBrush.shadow = new fabric.Shadow({
                                blur: 0,
                                offsetX: 0,
                                offsetY: 0,
                                affectStroke: true,
                                color: 'transparent',
                            });
                        }
                        canvas.renderAll();
                    }
                });
            }, {
                once: true
            });
        }

        document.getElementById('btnEditPhoto').addEventListener('click', function() {
            if (editedImageData) {
                openTuiEditor(editedImageData);
            }
        });

        // Toolbar aksi TUI Editor
        document.addEventListener('click', function(e) {
            const toolBtn = e.target.closest('[data-tool]');
            if (!toolBtn || !tuiEditor) return;

            const action = toolBtn.dataset.tool;
            tuiEditor.stopDrawingMode();

            if (action === 'draw') {
                tuiEditor.startDrawingMode('FREE_DRAWING');
                tuiEditor.setBrush({
                    width: 10,
                    color: '#e91e63'
                }); // Warna pink
            } else if (action === 'rect') {
                const canvas = tuiEditor._graphics.getCanvas();
                const rectWhite = new fabric.Rect({
                    left: canvas.getWidth() / 2,
                    top: canvas.getHeight() / 2,
                    width: 200,
                    height: 100,
                    fill: 'transparent',
                    stroke: 'white',
                    strokeWidth: 12,
                    originX: 'center',
                    originY: 'center',
                    shadow: {
                        color: 'black',
                        blur: 15,
                        offsetX: 5,
                        offsetY: 5
                    }
                });
                const rectPink = new fabric.Rect({
                    left: canvas.getWidth() / 2,
                    top: canvas.getHeight() / 2,
                    width: 200,
                    height: 100,
                    fill: 'transparent',
                    stroke: '#e91e63',
                    strokeWidth: 6,
                    originX: 'center',
                    originY: 'center'
                });
                const group = new fabric.Group([rectWhite, rectPink], {
                    left: canvas.getWidth() / 2,
                    top: canvas.getHeight() / 2,
                });
                canvas.add(group);
                canvas.setActiveObject(group);
            } else if (action === 'arrow') {
                const canvas = tuiEditor._graphics.getCanvas();
                tuiEditor.addIcon('arrow', {
                    fill: '#e91e63',
                    stroke: 'white',
                    strokeWidth: 2,
                    left: canvas.getWidth() / 2,
                    top: canvas.getHeight() / 2,
                    shadow: {
                        color: 'black',
                        blur: 10,
                        offsetX: 5,
                        offsetY: 5
                    },
                    originX: 'center',
                    originY: 'center'
                });
            } else if (action === 'rotate') {
                tuiEditor.rotate(90);
            } else if (action === 'undo') {
                tuiEditor.undo();
            } else if (action === 'redo') {
                tuiEditor.redo();
            } else if (action === 'delete') {
                const canvas = tuiEditor._graphics.getCanvas();
                const active = canvas.getActiveObject();
                if (active) {
                    canvas.remove(active);
                    canvas.renderAll();
                }
            }
        });

        // Simpan hasil dari TUI Editor
        document.getElementById('tui-save-btn').addEventListener('click', async function() {
            if (tuiEditor) {
                const dataURL = tuiEditor.toDataURL({
                    format: 'jpeg',
                    quality: 0.85
                });
                editedImageData = dataURL;
                photoPreview.src = dataURL;
                bootstrap.Modal.getInstance(document.getElementById('tuiEditorModal')).hide();
            }
        });

        document.getElementById('btnBack1').addEventListener('click', () => goTo('step2', 'step1'));
        document.getElementById('btnNext2').addEventListener('click', () => goTo('step2', 'step3'));

        // ── STEP 3: Keterangan (OCR / Manual) ─────────────────────────────────────
        let cropper;

        document.getElementById('imageInput').addEventListener('change', function(e) {
            if (e.target.files && e.target.files.length > 0) {
                const img = document.getElementById('imageToCrop');
                img.src = URL.createObjectURL(e.target.files[0]);
                document.getElementById('cropperContainer').style.display = 'block';
                if (cropper) cropper.destroy();
                img.onload = function() {
                    cropper = new Cropper(img, {
                        viewMode: 1,
                        autoCropArea: 0.8,
                        responsive: true
                    });
                };
                document.getElementById('btnExtractText').classList.remove('d-none');
            }
        });

        document.getElementById('btnExtractText').addEventListener('click', async function() {
            if (!cropper) return;
            const dataUrl = cropper.getCroppedCanvas().toDataURL('image/jpeg', 0.9);
            const btn = this;
            const progressContainer = document.getElementById('ocrProgressContainer');
            const progressBar = document.getElementById('ocrProgressBar');
            const percentLabel = document.getElementById('ocrPercent');

            btn.classList.add('d-none');
            progressContainer.classList.remove('d-none');
            progressBar.style.width = '0%';
            percentLabel.textContent = '0%';

            try {
                const worker = await Tesseract.createWorker('ind+eng', 1, {
                    workerPath: '{{ asset("assets/js/tesseract/worker.min.js") }}',
                    corePath: '{{ asset("assets/js/tesseract/tesseract-core.wasm.js") }}',
                    langPath: '{{ asset("assets/lang-data") }}',
                    logger: m => {
                        if (m.status === 'recognizing text') {
                            const pct = Math.round(m.progress * 100);
                            progressBar.style.width = pct + '%';
                            percentLabel.textContent = pct + '%';
                        }
                    }
                });
                const result = await worker.recognize(dataUrl);
                await worker.terminate();
                const ocrEl = document.getElementById('Ket_Perbaikan_OCR');
                ocrEl.value += (ocrEl.value ? '\n' : '') + result.data.text.trim();
            } catch (err) {
                console.error(err);
                alert('Error saat ekstrak teks. Coba lagi.');
            }

            progressContainer.classList.add('d-none');
            btn.classList.remove('d-none');
        });

        document.getElementById('btnBack2').addEventListener('click', () => goTo('step3', 'step2'));

        document.getElementById('btnNext3').addEventListener('click', function() {
            const isManualActive = document.getElementById('manual-tab').classList.contains('active');
            const ket = isManualActive ?
                document.getElementById('Ket_Perbaikan_Manual').value.trim() :
                document.getElementById('Ket_Perbaikan_OCR').value.trim();

            if (!ket) {
                alert('Keterangan permasalahan tidak boleh kosong.');
                return;
            }

            elFinalKet.value = ket;
            goTo('step3', 'step4');
        });

        // ── STEP 4: Kategori ──────────────────────────────────────────────────────
        document.getElementById('btnBack3').addEventListener('click', () => goTo('step4', 'step3'));

        document.querySelectorAll('.kategori-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.kategori-btn').forEach(b => {
                    b.style.background = 'white';
                    b.style.borderColor = 'var(--pink-200)';
                    b.style.color = '';
                    b.querySelector('i').style.color = 'var(--pink-500)';
                });

                this.style.background = 'var(--pink-600)';
                this.style.borderColor = 'var(--pink-600)';
                this.style.color = 'white';
                this.querySelector('i').style.color = 'white';

                const selected = this.dataset.kategori;
                elKategori.value = selected;

                const wrapper = document.getElementById('ketLainLainWrapper');
                if (selected === 'Lain-lain') {
                    wrapper.classList.remove('d-none');
                    elBtnSubmit.disabled = elKetLainLain.value.trim() === '';
                } else {
                    wrapper.classList.add('d-none');
                    elBtnSubmit.disabled = false;
                }
            });
        });

        elKetLainLain.addEventListener('input', function() {
            if (elKategori.value === 'Lain-lain') {
                elBtnSubmit.disabled = this.value.trim() === '';
            }
        });

        elBtnSubmit.addEventListener('click', function() {
            // Kita kirimkan elKetLainLain ke controller jika kategori adalah "Lain-lain"
            let finalKategori = elKategori.value;
            if (finalKategori === 'Lain-lain' && elKetLainLain.value.trim()) {
                finalKategori = 'Lain-lain: ' + elKetLainLain.value.trim();
            }

            if (!elFinalKet.value.trim()) {
                alert('Keterangan perbaikan tidak boleh kosong.');
                return;
            }
            if (!elKategori.value) {
                alert('Pilih kategori permasalahan terlebih dahulu.');
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            const formData = new FormData();
            formData.append('No_Instruksi', elNoInstruksi.value);
            formData.append('Type_Traktor', elTypeTraktor.value);
            formData.append('Ket_Perbaikan', elFinalKet.value);
            formData.append('Jam_Start', elJamStart.value);
            formData.append('Kategori_Perbaikan', finalKategori);
            formData.append('_token', "{{ csrf_token() }}");

            const photoFile = document.getElementById('photoInput').files[0];
            // Karena kita pakai resized base64, kita kirim string base64 ke server via ajax (jika ada file / sudah diubah)
            if (editedImageData) {
                formData.append('photoData', editedImageData);
            } else if (photoFile) {
                // fall back ke file murni jika logic base64 error (seharusnya sudah di-handle)
                formData.append('photo', photoFile);
            }

            fetch("{{ route('repair.store') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => {
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
                        alert('Perbaikan Berhasil Disimpan!');
                        window.location.href = "{{ route('repair.index') }}";
                    } else {
                        alert(res.message || 'Error saving data');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Laporan';
                    }
                })
                .catch(e => {
                    console.error(e);
                    alert('Error: ' + e.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Laporan';
                });
        });

    });
</script>
@endpush