@extends('layouts.app')

@section('title', 'Diagnosis')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Diagnosis Management</h1>
    <a href="{{ route('diagnosis.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> New Diagnosis
    </a>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Diagnosis List</h6>
            </div>
            <div class="card-body">
                <p>Diagnosis management system will be implemented here.</p>
                <div class="text-center py-4">
                    <i class="fas fa-diagnoses fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Diagnosis module is under development.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection