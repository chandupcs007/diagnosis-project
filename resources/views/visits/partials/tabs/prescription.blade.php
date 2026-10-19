<div class="sqa-tab-section">
    <div class="sqa-tab-form-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-prescription me-2"></i>Doctor Prescription Pad</h6>

        @if($errors->any() && ($activeTab ?? request('tab')) === 'prescription')
            <div class="alert alert-danger py-2 mb-3">
                <ul class="mb-0 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('visits.prescriptions.store', $visit) }}" id="prescriptionForm">
            @csrf
            <input type="hidden" name="prescription_image" id="prescriptionImageInput">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Prescription Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Morning medicines" value="{{ old('title') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Notes (optional)</label>
                    <input type="text" name="notes" class="form-control" placeholder="Additional instructions..." value="{{ old('notes') }}">
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="sqa-scribble-toolbar">
                        <div class="sqa-scribble-tools">
                            <label class="sqa-scribble-tool-label">Pen</label>
                            <input type="color" id="penColor" value="#1a237e" title="Pen color">
                            <select id="penSize" class="form-select form-select-sm">
                                <option value="2">Thin</option>
                                <option value="3" selected>Medium</option>
                                <option value="5">Thick</option>
                                <option value="8">Bold</option>
                            </select>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="eraserBtn">
                                <i class="fas fa-eraser me-1"></i> Eraser
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="penBtn">
                                <i class="fas fa-pen me-1"></i> Pen
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="clearPadBtn">
                            <i class="fas fa-trash-alt me-1"></i> Clear Pad
                        </button>
                    </div>

                    <div class="sqa-scribble-pad-wrap">
                        <canvas id="prescriptionPad" class="sqa-scribble-pad" width="800" height="420"></canvas>
                        <div class="sqa-scribble-pad-hint">Write prescription here using mouse or touch</div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="sqa-scribble-preview-panel">
                        <h6 class="sqa-scribble-preview-title">Live Preview</h6>
                        <div class="sqa-scribble-preview-box">
                            <img id="prescriptionPreview" alt="Prescription preview" class="d-none">
                            <div id="prescriptionPreviewEmpty" class="sqa-scribble-preview-empty">
                                <i class="fas fa-file-prescription"></i>
                                <p>Your writing will appear here as you scribble on the pad.</p>
                            </div>
                        </div>
                        <p class="sqa-scribble-save-note">
                            <i class="fas fa-info-circle me-1"></i>
                            Data is stored only when you click <strong>Save Prescription</strong>.
                        </p>
                        <button type="submit" class="btn btn-primary btn-sm w-100" id="savePrescriptionBtn">
                            <i class="fas fa-save me-1"></i> Save Prescription
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="sqa-tab-records-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-list me-2"></i>Saved Prescriptions</h6>

        @if($visit->prescriptions->count() > 0 || $visit->prescription)
            <div class="sqa-prescription-gallery">
                @if($visit->prescription && $visit->prescriptions->isEmpty())
                    <div class="sqa-prescription-card">
                        <div class="sqa-prescription-card-header">
                            <div>
                                <strong>Initial Prescription</strong>
                                <small class="text-muted d-block">{{ $visit->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            <span class="badge bg-secondary">From visit registration</span>
                        </div>
                        <div class="sqa-prescription-text">{{ $visit->prescription }}</div>
                    </div>
                @endif

                @foreach($visit->prescriptions as $prescription)
                    <div class="sqa-prescription-card">
                        <div class="sqa-prescription-card-header">
                            <div>
                                <strong>{{ $prescription->title ?: 'Doctor Prescription' }}</strong>
                                <small class="text-muted d-block">
                                    {{ $prescription->created_at->format('M d, Y h:i A') }}
                                    @if($prescription->doctor)
                                        · Dr. {{ $prescription->doctor->name }}
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="sqa-prescription-image-wrap">
                            <img src="{{ $prescription->image_url }}" alt="Prescription" class="sqa-prescription-image">
                        </div>
                        @if($prescription->notes)
                            <div class="sqa-prescription-notes">
                                <span class="sqa-radiology-label">Notes</span>
                                <p>{{ $prescription->notes }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            @include('visits.partials.tabs.empty-state', ['message' => 'No prescriptions saved yet. Write on the pad above and click Save Prescription.'])
        @endif
    </div>
</div>

@push('scripts')
<script>
(function () {
    const canvas = document.getElementById('prescriptionPad');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const previewImg = document.getElementById('prescriptionPreview');
    const previewEmpty = document.getElementById('prescriptionPreviewEmpty');
    const imageInput = document.getElementById('prescriptionImageInput');
    const form = document.getElementById('prescriptionForm');
    const penColor = document.getElementById('penColor');
    const penSize = document.getElementById('penSize');
    const eraserBtn = document.getElementById('eraserBtn');
    const penBtn = document.getElementById('penBtn');
    const clearPadBtn = document.getElementById('clearPadBtn');

    let drawing = false;
    let hasDrawing = false;
    let isEraser = false;
    let lastX = 0;
    let lastY = 0;

    function resizeCanvas() {
        const wrap = canvas.parentElement;
        const ratio = window.devicePixelRatio || 1;
        const width = wrap.clientWidth;
        const height = Math.max(320, Math.min(420, width * 0.55));

        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        canvas.width = Math.floor(width * ratio);
        canvas.height = Math.floor(height * ratio);
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(ratio, ratio);
        fillBackground(width, height);
        hasDrawing = false;
        updatePreview();
    }

    function fillBackground(width, height) {
        const w = width || canvas.clientWidth;
        const h = height || canvas.clientHeight;

        ctx.fillStyle = '#fffef8';
        ctx.fillRect(0, 0, w, h);

        ctx.strokeStyle = '#eceff1';
        ctx.lineWidth = 1;
        for (let y = 28; y < h; y += 28) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(w, y);
            ctx.stroke();
        }
    }

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
            x: clientX - rect.left,
            y: clientY - rect.top,
        };
    }

    function startDraw(e) {
        e.preventDefault();
        drawing = true;
        const pos = getPos(e);
        lastX = pos.x;
        lastY = pos.y;
    }

    function draw(e) {
        if (!drawing) return;
        e.preventDefault();

        const pos = getPos(e);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.lineWidth = parseInt(penSize.value, 10);

        if (isEraser) {
            ctx.strokeStyle = '#fffef8';
            ctx.lineWidth = 16;
        } else {
            ctx.strokeStyle = penColor.value;
        }

        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();

        lastX = pos.x;
        lastY = pos.y;
        hasDrawing = true;
        updatePreview();
    }

    function stopDraw() {
        drawing = false;
    }

    function updatePreview() {
        if (!hasDrawing) {
            previewImg.classList.add('d-none');
            previewEmpty.classList.remove('d-none');
            return;
        }

        previewImg.src = canvas.toDataURL('image/png');
        previewImg.classList.remove('d-none');
        previewEmpty.classList.add('d-none');
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseleave', stopDraw);
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDraw);

    eraserBtn.addEventListener('click', function () {
        isEraser = true;
        eraserBtn.classList.add('active');
        penBtn.classList.remove('active');
    });

    penBtn.addEventListener('click', function () {
        isEraser = false;
        penBtn.classList.add('active');
        eraserBtn.classList.remove('active');
    });

    clearPadBtn.addEventListener('click', function () {
        fillBackground(canvas.clientWidth, canvas.clientHeight);
        hasDrawing = false;
        updatePreview();
    });

    form.addEventListener('submit', function (e) {
        if (!hasDrawing) {
            e.preventDefault();
            alert('Please write on the prescription pad before saving.');
            return;
        }
        imageInput.value = canvas.toDataURL('image/png');
    });

    penBtn.classList.add('active');
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
})();
</script>
@endpush
