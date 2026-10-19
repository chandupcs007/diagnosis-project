@extends('layouts.app')

@section('title', 'Book Appointment')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Book Appointment',
    'breadcrumb' => 'Book Appointment',
])
@endpush

@section('content')
<div class="d-sm-flex justify-content-end mb-2">
    <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left fa-sm me-1"></i> Back to Appointments
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('appointments.store') }}" id="appointmentForm">
    @csrf

    {{-- Step 1: Date & Department --}}
    <div class="sqa-form-card mb-4" id="step1">
        <h2 class="sqa-section-header"><span class="badge bg-primary me-2">1</span> Select Date & Department</h2>
        <div class="sqa-form-body">
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="appointment_date">Appointment Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                               value="{{ old('appointment_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="department">Department <span class="required">*</span></label>
                        <select class="form-control" id="department" name="department" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department }}" {{ old('department') == $department ? 'selected' : '' }}>
                                    {{ $department }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex align-items-end">
                    <button type="button" class="btn btn-primary" id="loadDoctorsBtn">
                        <i class="fas fa-search me-1"></i> Find Doctors
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 2: Doctor Selection --}}
    <div class="sqa-form-card mb-4 d-none" id="step2">
        <h2 class="sqa-section-header"><span class="badge bg-primary me-2">2</span> Select Doctor</h2>
        <div class="sqa-form-body">
            <div id="doctorsLoading" class="text-muted d-none"><i class="fas fa-spinner fa-spin me-1"></i> Loading doctors...</div>
            <div id="doctorsEmpty" class="alert alert-warning d-none">No doctors available for this department on the selected date.</div>
            <div class="row" id="doctorsList"></div>
            <input type="hidden" name="doctor_id" id="doctor_id" value="{{ old('doctor_id') }}">
        </div>
    </div>

    {{-- Step 3: Time Slot --}}
    <div class="sqa-form-card mb-4 d-none" id="step3">
        <h2 class="sqa-section-header"><span class="badge bg-primary me-2">3</span> Select Time Slot</h2>
        <div class="sqa-form-body">
            <p class="text-muted small mb-3" id="slotInfo"></p>
            <div id="slotsLoading" class="text-muted d-none"><i class="fas fa-spinner fa-spin me-1"></i> Loading slots...</div>
            <div id="slotsEmpty" class="alert alert-warning d-none">No available slots for this doctor on the selected date.</div>
            <div class="d-flex flex-wrap gap-2" id="slotsList"></div>
            <input type="hidden" name="slot_time" id="slot_time" value="{{ old('slot_time') }}">
        </div>
    </div>

    {{-- Step 4: Patient --}}
    <div class="sqa-form-card mb-4 d-none" id="step4">
        <h2 class="sqa-section-header"><span class="badge bg-primary me-2">4</span> Patient Details</h2>
        <div class="sqa-form-body">
            <div class="mb-3">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="patient_type" id="patient_existing" value="existing"
                           {{ old('patient_type', 'existing') === 'existing' ? 'checked' : '' }}>
                    <label class="form-check-label" for="patient_existing">Existing Patient (PID)</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="patient_type" id="patient_new" value="new"
                           {{ old('patient_type') === 'new' ? 'checked' : '' }}>
                    <label class="form-check-label" for="patient_new">New Patient (Temp Number)</label>
                </div>
            </div>

            <div id="existingPatientSection">
                <div class="row align-items-end">
                    <div class="col-xl-4 col-md-6">
                        <div class="form-group">
                            <label for="pid">Patient PID <span class="required">*</span></label>
                            <input type="text" class="form-control" id="pid" name="pid" value="{{ old('pid') }}"
                                   placeholder="e.g. PID2026000001">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <button type="button" class="btn btn-outline-primary mb-3" id="lookupPidBtn">
                            <i class="fas fa-search me-1"></i> Lookup
                        </button>
                    </div>
                </div>
                <div id="patientLookupResult" class="alert d-none"></div>
            </div>

            <div id="newPatientSection" class="d-none">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    Basic details will be saved and a <strong>Temp Number</strong> will be generated. When the patient arrives, use Check-In to assign the permanent PID.
                </div>
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group">
                            <label>Date of Birth <span class="required">*</span></label>
                            <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth') }}">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group">
                            <label>Gender <span class="required">*</span></label>
                            <select class="form-control" name="gender">
                                <option value="">Select</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group">
                            <label>Phone <span class="required">*</span></label>
                            <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-xl-6">
                    <div class="form-group">
                        <label for="notes">Notes (optional)</label>
                        <textarea class="form-control" name="notes" id="notes" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fas fa-calendar-check me-1"></i> Book Appointment
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || '{{ csrf_token() }}';

    let selectedDoctorId = null;
    let selectedSlotTime = null;

    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const step4 = document.getElementById('step4');
    const submitBtn = document.getElementById('submitBtn');

    function updateSubmitState() {
        const patientType = document.querySelector('input[name="patient_type"]:checked')?.value;
        const hasSlot = !!selectedSlotTime;
        let hasPatient = false;

        if (patientType === 'existing') {
            hasPatient = document.getElementById('patientLookupResult').classList.contains('alert-success');
        } else {
            hasPatient = true;
        }

        submitBtn.disabled = !(hasSlot && hasPatient);
    }

    document.getElementById('loadDoctorsBtn').addEventListener('click', function() {
        const date = document.getElementById('appointment_date').value;
        const department = document.getElementById('department').value;

        if (!date || !department) {
            alert('Please select both date and department.');
            return;
        }

        step2.classList.remove('d-none');
        step3.classList.add('d-none');
        step4.classList.add('d-none');
        document.getElementById('doctorsList').innerHTML = '';
        document.getElementById('doctorsEmpty').classList.add('d-none');
        document.getElementById('doctorsLoading').classList.remove('d-none');

        fetch(`{{ route('appointments.doctors') }}?department=${encodeURIComponent(department)}&date=${date}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('doctorsLoading').classList.add('d-none');
            const list = document.getElementById('doctorsList');
            list.innerHTML = '';

            if (!data.doctors || data.doctors.length === 0) {
                document.getElementById('doctorsEmpty').classList.remove('d-none');
                return;
            }

            data.doctors.forEach(doctor => {
                const col = document.createElement('div');
                col.className = 'col-xl-4 col-md-6 mb-3';
                col.innerHTML = `
                    <div class="card doctor-card h-100" data-doctor-id="${doctor.id}" style="cursor:pointer;">
                        <div class="card-body">
                            <h6 class="card-title mb-1">${doctor.name}</h6>
                            <p class="card-text small text-muted mb-1">${doctor.specialization || ''}</p>
                            <p class="card-text small mb-0">
                                <i class="fas fa-clock me-1"></i>
                                ${doctor.schedule ? doctor.schedule.start + ' - ' + doctor.schedule.end : 'N/A'}
                                &nbsp;|&nbsp; ${doctor.slot_duration_minutes} min/slot
                            </p>
                        </div>
                    </div>`;
                list.appendChild(col);
            });

            document.querySelectorAll('.doctor-card').forEach(card => {
                card.addEventListener('click', function() {
                    document.querySelectorAll('.doctor-card').forEach(c => c.classList.remove('border-primary', 'border-2'));
                    this.classList.add('border-primary', 'border-2');
                    selectedDoctorId = this.dataset.doctorId;
                    document.getElementById('doctor_id').value = selectedDoctorId;
                    loadSlots();
                });
            });
        });
    });

    function loadSlots() {
        const date = document.getElementById('appointment_date').value;
        step3.classList.remove('d-none');
        step4.classList.add('d-none');
        selectedSlotTime = null;
        document.getElementById('slot_time').value = '';
        document.getElementById('slotsList').innerHTML = '';
        document.getElementById('slotsEmpty').classList.add('d-none');
        document.getElementById('slotsLoading').classList.remove('d-none');

        fetch(`{{ route('appointments.slots') }}?doctor_id=${selectedDoctorId}&date=${date}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('slotsLoading').classList.add('d-none');
            const list = document.getElementById('slotsList');
            list.innerHTML = '';

            const available = (data.slots || []).filter(s => s.available);
            document.getElementById('slotInfo').textContent = available.length
                ? `${available.length} available slot(s) — each slot duration shown below`
                : '';

            if (!available.length) {
                document.getElementById('slotsEmpty').classList.remove('d-none');
                return;
            }

            data.slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm ' + (slot.available ? 'btn-outline-primary slot-btn' : 'btn-light text-muted');
                btn.disabled = !slot.available;
                btn.dataset.time = slot.time;
                btn.textContent = slot.display + (slot.available ? '' : ' (Booked)');
                if (slot.available) {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        selectedSlotTime = this.dataset.time;
                        document.getElementById('slot_time').value = selectedSlotTime;
                        step4.classList.remove('d-none');
                        updateSubmitState();
                    });
                }
                list.appendChild(btn);
            });
        });
    }

    document.querySelectorAll('input[name="patient_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const isExisting = this.value === 'existing';
            document.getElementById('existingPatientSection').classList.toggle('d-none', !isExisting);
            document.getElementById('newPatientSection').classList.toggle('d-none', isExisting);
            updateSubmitState();
        });
    });

    document.getElementById('lookupPidBtn').addEventListener('click', function() {
        const pid = document.getElementById('pid').value.trim();
        const result = document.getElementById('patientLookupResult');

        if (!pid) {
            alert('Enter a PID to lookup.');
            return;
        }

        fetch(`{{ route('appointments.lookup-patient') }}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ pid }),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            result.classList.remove('d-none', 'alert-success', 'alert-danger');
            if (ok && data.found) {
                result.classList.add('alert-success');
                result.innerHTML = `<i class="fas fa-check-circle me-1"></i> Found: <strong>${data.patient.name}</strong> | PID: ${data.patient.pid} | Phone: ${data.patient.phone}`;
            } else {
                result.classList.add('alert-danger');
                result.innerHTML = `<i class="fas fa-times-circle me-1"></i> ${data.message || 'Patient not found.'}`;
            }
            updateSubmitState();
        });
    });

    // Init patient type sections
    const initType = document.querySelector('input[name="patient_type"]:checked')?.value || 'existing';
    document.getElementById('existingPatientSection').classList.toggle('d-none', initType !== 'existing');
    document.getElementById('newPatientSection').classList.toggle('d-none', initType === 'existing');
});
</script>
@endpush
