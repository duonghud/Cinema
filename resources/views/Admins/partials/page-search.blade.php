@php
    $searchValue = request('search');
    $placeholder = $placeholder ?? 'Tìm kiếm...';
    $extraFields = $extraFields ?? [];
    $resetParams = $resetParams ?? [];
@endphp

<form method="GET" action="{{ url()->current() }}" class="row g-2 mb-3">
    @foreach($extraFields as $fieldName => $fieldValue)
        <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValue }}">
    @endforeach

    <div class="col-md-6 col-lg-5">
        <input
            type="text"
            name="search"
            value="{{ $searchValue }}"
            class="form-control"
            placeholder="{{ $placeholder }}">
    </div>

    <div class="col-auto">
        <button type="submit" class="btn btn-outline-dark">
            Tìm kiếm
        </button>
    </div>

    @if($searchValue || !empty($resetParams))
        <div class="col-auto">
            <a href="{{ url()->current() }}{{ !empty($resetParams) ? '?' . http_build_query($resetParams) : '' }}" class="btn btn-outline-secondary">
                Xóa lọc
            </a>
        </div>
    @endif
</form>
