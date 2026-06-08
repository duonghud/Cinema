@php
    $searchValue = request('search');
    $placeholder = $placeholder ?? 'Tìm kiếm...';
    $extraFields = $extraFields ?? [];
    $resetParams = $resetParams ?? [];
    $filters = $filters ?? [];
    $hasActiveFilter = collect($filters)->contains(function ($filter) {
        return filled(request($filter['name'] ?? ''));
    });
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

    @foreach($filters as $filter)
        <div class="col-md-3 col-lg-2">
            <select name="{{ $filter['name'] }}" class="form-select">
                <option value="">{{ $filter['all_label'] ?? 'Tất cả' }}</option>
                @foreach(($filter['options'] ?? []) as $value => $label)
                    <option value="{{ $value }}" @selected((string) request($filter['name']) === (string) $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    @endforeach

    <div class="col-auto">
        <button type="submit" class="btn btn-outline-dark">
            Tìm kiếm
        </button>
    </div>

    @if($searchValue || $hasActiveFilter || !empty($resetParams))
        <div class="col-auto">
            <a href="{{ url()->current() }}{{ !empty($resetParams) ? '?' . http_build_query($resetParams) : '' }}" class="btn btn-outline-secondary">
                Xóa lọc
            </a>
        </div>
    @endif
</form>
