<div class="modal fade" id="editModal{{$movie->movieID}}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow">

            <form action="{{ route('admin.movies.update',$movie->movieID) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Header -->
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Chỉnh sửa phim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body pt-0">

                    <div class="row">

                        <!-- Tên phim -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Tên phim</label>
                            <input type="text" name="movieTitle"
                                class="form-control"
                                value="{{$movie->movieTitle}}">
                        </div>

                        <!-- Đạo diễn -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Đạo diễn</label>
                            <input type="text" name="director"
                                class="form-control"
                                value="{{$movie->director}}">
                        </div>

                    </div>

                    <div class="row">

                        <!-- Poster -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Poster hiện tại</label><br>
                            <img src="{{ asset('posters/'.$movie->poster) }}"
                                width="80"
                                class="rounded shadow-sm mb-2">

                            <input type="file" name="poster" class="form-control">
                        </div>

                        <!-- Trailer -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Trailer</label><br>

                            @if($movie->trailer)
                            <video width="120" class="rounded shadow-sm mb-2" controls>
                                <source src="{{ asset($movie->trailer) }}">
                            </video>
                            @endif

                            <input type="file" name="trailer" class="form-control">
                        </div>

                        <!-- Thời lượng -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Thời lượng</label>
                            <input type="number" name="duration"
                                class="form-control"
                                value="{{$movie->duration}}"
                                placeholder="Nhập thời lượng (phút)"
                                min="0">
                        </div>
                    </div>

                    <div class="row">

                        <!-- Release -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Phát hành</label>
                            <input type="date" name="releaseDate"
                                class="form-control @error('releaseDate') is-invalid @enderror"
                                value="{{ old('releaseDate', $movie->releaseDate->format('Y-m-d')) }}">

                            @error('releaseDate')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Age -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Kiểm duyệt</label>
                            <select name="ageRatingID" class="form-select">
                                @foreach($ageRatings as $age)
                                <option value="{{$age->ageRatingID}}"
                                    @if($movie->ageRatingID == $age->ageRatingID) selected @endif>
                                    {{$age->code}}
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
                                        id="genre{{ $genre->genreID }}"
                                        {{-- tick sẵn nếu phim đã có thể loại này --}}
                                        {{ $movie->genres->contains('genreID', $genre->genreID) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="genre{{ $genre->genreID }}">
                                        {{ $genre->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>


                        <!-- Studio -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Hãng</label>
                            <select name="studioID" class="form-select">
                                @foreach($studios as $studio)
                                <option value="{{$studio->studioID}}"
                                    @if($movie->studioID == $studio->studioID) selected @endif>
                                    {{$studio->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Mô tả</label>
                        <textarea name="description"
                            rows="3"
                            class="form-control">{{$movie->description}}</textarea>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Đóng
                    </button>

                    <button type="submit" class="btn btn-dark">
                        Cập nhật
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>