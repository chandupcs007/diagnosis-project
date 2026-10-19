<form method="GET" action="{{ $filterAction }}" class="row g-2 align-items-end mb-3">
    <div class="col-md-3">
        <label class="form-label small mb-0">Search</label>
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Request #, sample #, patient..." value="{{ request('search') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label small mb-0">Department</label>
        <select name="department" class="form-control form-control-sm">
            <option value="">All Departments</option>
            @foreach(config('diagnosis.lab_departments') as $key => $dept)
                <option value="{{ $key }}" {{ request('department') === $key ? 'selected' : '' }}>
                    {{ $dept['name'] }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-auto">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i> Filter</button>
        <a href="{{ $filterAction }}" class="btn btn-secondary btn-sm">Clear</a>
    </div>
</form>
