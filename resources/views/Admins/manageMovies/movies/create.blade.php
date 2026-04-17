<!-- Create Movie Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow">

            <form action="{{ route('admin.movies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Header -->
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Thêm phim mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body pt-0">

                    <div class="row">

                        <!-- Tên phim -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Tên phim</label>
                            <input type="text"
                                name="movieTitle"
                                class="form-control @error('movieTitle') is-invalid @enderror"
                                value="{{ old('movieTitle') }}"
                                required>

                            @error('movieTitle')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Đạo diễn -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Đạo diễn</label>
                            <input type="text"
                                name="director"
                                class="form-control @error('director') is-invalid @enderror"
                                value="{{ old('director') }}">

                            @error('director')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                    </div>

                    <div class="row">

                        <!-- Poster -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Poster</label>
                            <input type="file"
                                name="poster"
                                class="form-control @error('poster') is-invalid @enderror">

                            @error('poster')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Trailer -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Trailer</label>
                            <input type="file"
                                name="trailer"
                                accept="video/*"
                                class="form-control @error('trailer') is-invalid @enderror">

                            @error('trailer')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                    </div>

                    <div class="row">

                        <!-- Release Date -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Phát hành</label>
                            <input type="date"
                                name="releaseDate"
                                class="form-control @error('releaseDate') is-invalid @enderror"
                                value="{{ old('releaseDate') }}">

                            @error('releaseDate')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Age -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Độ tuổi</label>
                            <select name="ageRatingID"
                                class="form-select @error('ageRatingID') is-invalid @enderror">

                                <option value="">-- Chọn độ tuổi --</option>

                                @foreach($ageRatings as $age)
                                <option value="{{ $age->ageRatingID }}"
                                    {{ old('ageRatingID') == $age->ageRatingID ? 'selected' : '' }}>
                                    {{ $age->code }}
                                </option>
                                @endforeach
                            </select>

                            @error('ageRatingID')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Studio -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Hãng phim</label>
                            <select name="studioID"
                                class="form-select @error('studioID') is-invalid @enderror">

                                <option value="">-- Chọn hãng phim --</option>

                                @foreach($studios as $studio)
                                <option value="{{ $studio->studioID }}"
                                    {{ old('studioID') == $studio->studioID ? 'selected' : '' }}>
                                    {{ $studio->name }}
                                </option>
                                @endforeach
                            </select>

                            @error('studioID')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                    </div>

                    <!-- Genre -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Thể loại</label>
                        <div class="d-flex flex-wrap">

                            @foreach($genres as $genre)
                            <div class="form-check me-3">

                                <input class="form-check-input"
                                    type="checkbox"
                                    name="genreID[]"
                                    value="{{ $genre->genreID }}"
                                    id="genre{{ $genre->genreID }}"
                                    {{ (collect(old('genreID'))->contains($genre->genreID)) ? 'checked' : '' }}>

                                <label class="form-check-label"
                                    for="genre{{ $genre->genreID }}">
                                    {{ $genre->name }}
                                </label>

                            </div>
                            @endforeach

                        </div>

                        @error('genreID')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Mô tả -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Mô tả</label>

                        <textarea name="description"
                            rows="3"
                            class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>

                        @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0">
                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Hủy
                    </button>

                    <button type="submit"
                        class="btn btn-dark">
                        + Lưu phim
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Tự mở modal khi có lỗi -->
@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var createModal = new bootstrap.Modal(document.getElementById('createModal'));
        createModal.show();
    });
</script>
@endif