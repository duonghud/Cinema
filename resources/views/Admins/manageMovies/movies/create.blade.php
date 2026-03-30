<!-- Create Movie Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg"> <!-- rộng hơn -->
        <div class="modal-content shadow">

            <form action="{{ route('movies.store') }}" method="POST" enctype="multipart/form-data">
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
                            <input type="text" name="movieTitle" class="form-control" required>
                        </div>

                        <!-- Đạo diễn -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Đạo diễn</label>
                            <input type="text" name="director" class="form-control">
                        </div>

                    </div>

                    <div class="row">

                        <!-- Poster -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Poster</label>
                            <input type="file" name="poster" class="form-control">
                        </div>

                        <!-- Trailer -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Trailer</label>
                            <input type="text" name="trailer" class="form-control" placeholder="YouTube link">
                        </div>

                    </div>

                    <div class="row">

                        <!-- Release Date -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Phát hành</label>
                            <input type="date" name="releaseDate" class="form-control">
                        </div>

                        <!-- Age -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Độ tuổi</label>
                            <select name="ageRatingID" class="form-select">
                                <option value="">-- Chọn độ tuổi --</option>
                                @foreach($ageRatings as $age)
                                <option value="{{ $age->ageRatingID }}">
                                    {{ $age->code }}
                                </option>
                                @endforeach
                            </select>
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
                                        id="genre{{ $genre->genreID }}">
                                    <label class="form-check-label" for="genre{{ $genre->genreID }}">
                                        {{ $genre->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>


                        <!-- Studio -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Hãng phim</label>
                            <select name="studioID" class="form-select">
                                <option value="">-- Chọn hãng phim --</option>
                                @foreach($studios as $studio)
                                <option value="{{ $studio->studioID }}">
                                    {{ $studio->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <!-- Mô tả -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Mô tả</label>
                        <textarea name="description" rows="3" class="form-control"></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-dark">
                        + Lưu phim
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>