@props(['title', 'breadcrumb' => null])

<div class="sqa-page-header d-sm-flex align-items-center justify-content-between">
    <div>
        <h1 class="sqa-page-title">
            {{ $title }}
            @if($breadcrumb)
                <span class="sqa-breadcrumb-inline" aria-label="breadcrumb">
                    (<a href="{{ route('dashboard') }}">Home</a>
                    <span class="separator">/</span>
                    <span>{{ $breadcrumb }}</span>)
                </span>
            @endif
        </h1>
    </div>
    @if(!empty($actions))
        <div class="flex-shrink-0">
            {!! $actions !!}
        </div>
    @endif
</div>
