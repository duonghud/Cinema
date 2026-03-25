<div class="modal fade" id="editModal{{$movie->movieID}}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('movies.update',$movie->movieID) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa phim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Movie Title -->
                    <label>Tên phim</label>
                    <input type="text" name="movieTitle" class="form-control mb-2" value="{{$movie->movieTitle}}">

                    <!-- Poster -->
                    <label>Poster hiện tại</label><br>
                    <img src="{{ asset('posters/'.$movie->poster) }}" width="80" class="mb-2">

                    <label>Thay poster</label>
                    <input type="file" name="poster" class="form-control mb-2">

                    <!-- Trailer -->
                    <label>Trailer</label>
                    <input type="text" name="trailer" class="form-control mb-2" value="{{$movie->trailer}}">

                    <!-- Director -->
                    <label>Đạo diễn</label>
                    <input type="text" name="director" class="form-control mb-2" value="{{$movie->director}}">

                    <!-- Description -->
                    <label>Mô tả</label>
                    <textarea name="description" class="form-control mb-2">{{$movie->description}}</textarea>

                    <!-- Release Date -->
                    <label>Thời gian phát hành</label>
                    <input type="date" name="releaseDate" class="form-control mb-2" value="{{$movie->releaseDate}}">

                    <!-- Age Rating -->
                    <label>Kiểm duyệt</label>
                    <select name="ageRatingID" class="form-control mb-2">
                        @foreach($ageRatings as $age)
                            <option value="{{$age->ageRatingID}}"
                                @if($movie->ageRatingID == $age->ageRatingID) selected @endif>
                                {{$age->code}}
                            </option>
                        @endforeach
                    </select>

                    <!-- Studio -->
                    <label>Hãng sản xuất</label>
                    <select name="studioID" class="form-control">
                        @foreach($studios as $studio)
                            <option value="{{$studio->studioID}}"

                                @if($movie->studioID == $studio->studioID) selected @endif>
                                {{$studio->name}}
                            </option>
                        @endforeach
                    </select>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>

            </form>

        </div>
    </div>
</div>
