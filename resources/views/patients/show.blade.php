@extends('layouts.app')

@section('title', 'Patient Details')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Patient Details</h1>
    <div>
        <a href="{{ route('patients.edit', $patient->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit Patient
        </a>
        <a href="{{ route('patients.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Patients
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Patient ID:</th>
                                <td>{{ $patient->id }}</td>
                            </tr>
                            <tr>
                                <th>Full Name:</th>
                                <td>{{ $patient->first_name }} {{ $patient->last_name }}</td>
                            </tr>
                            <tr>
                                <th>Date of Birth:</th>
                                <td>{{ $patient->date_of_birth->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Age:</th>
                                <td>{{ $patient->date_of_birth->age }} years</td>
                            </tr>
                            <tr>
                                <th>Gender:</th>
                                <td>{{ ucfirst($patient->gender) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Phone:</th>
                                <td>{{ $patient->phone }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $patient->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Emergency Contact:</th>
                                <td>
                                    @if($patient->emergency_contact_name)
                                        {{ $patient->emergency_contact_name }}<br>
                                        {{ $patient->emergency_contact_phone }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($patient->address)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Address</h6>
                        <p class="mb-0">{{ $patient->address }}</p>
                        @if($patient->city || $patient->state || $patient->zip_code)
                        <p class="mb-0">
                            {{ $patient->city }}{{ $patient->city && $patient->state ? ', ' : '' }}
                            {{ $patient->state }}
                            {{ $patient->zip_code }}
                        </p>
                        @endif
                    </div>
                </div>
                @endif

                @if($patient->medical_history)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Medical History</h6>
                        <p class="text-justify">{{ $patient->medical_history }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Registration Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="50%">Registered On:</th>
                        <td>{{ $patient->created_at->format('F d, Y \a\t h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>{{ $patient->updated_at->format('F d, Y \a\t h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit Patient
                </a>
                <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this patient? This action cannot be undone.')">
                        <i class="fas fa-trash me-2"></i>Delete Patient
                    </button>
                </form>
                <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection