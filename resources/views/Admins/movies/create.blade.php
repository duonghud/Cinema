<!-- Create Movie Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('movies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Thêm phim mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Tên phim -->
                    <div class="mb-3">
                        <label class="form-label">Tên phim</label>
                        <input type="text" name="movieTitle" class="form-control" required>
                    </div>

                    <!-- Poster -->
                    <div class="mb-3">
                        <label class="form-label">Poster</label>
                        <input type="file" name="poster" class="form-control">
                    </div>

                    <!-- Trailer -->
                    <div class="mb-3">
                        <label class="form-label">Trailer</label>
                        <input type="text" name="trailer" class="form-control" placeholder="YouTube link">
                    </div>

                    <!-- Director -->
                    <div class="mb-3">
                        <label class="form-label">Đạo diễn</label>
                        <input type="text" name="director" class="form-control">
                    </div>

                    <!-- Release Date -->
                    <div class="mb-3">
                        <label class="form-label">Thời gian phát hành</label>
                        <input type="date" name="releaseDate" class="form-control">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <!-- Age Rating -->
                    <div class="mb-3">
                        <label class="form-label">Độ tuổi</label>
                        <select name="ageRatingID" class="form-select">
                            @foreach($ageRatings as $age)
                                <option value="{{ $age->ageRatingID }}">
                                    {{ $age->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Thể loại -->
                    <div class="mb-3">
                        <label class="form-label">Thể loại</label>
                        <select name="ageRatingID" class="form-select">
                            @foreach($ageRatings as $age)
                                <option value="{{ $age->ageRatingID }}">
                                    {{ $age->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    

                    <!-- Studio -->
                    <div class="mb-3">
                        <label class="form-label">Hãng phim</label>
                        <select name="studioID" class="form-select">
                            @foreach($studios as $studio)
                                <option value="{{ $studio->studioID }}">
                                    {{ $studio->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-success">
                        Lưu
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
