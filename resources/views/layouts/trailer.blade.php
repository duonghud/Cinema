<!-- TRAILER MODAL -->
<div class="modal fade" id="trailerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">

            <div class="modal-header border-0">
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body p-0">

                <!-- YouTube iframe -->
                <iframe
                    id="trailerFrame"
                    width="100%"
                    height="500"
                    class="d-none"
                    frameborder="0"
                    allow="autoplay; encrypted-media"
                    allowfullscreen>
                </iframe>

                <!-- Video file -->
                <video id="trailerVideo"
                       class="w-100 d-none"
                       height="500"
                       controls
                       preload="none">
                </video>

            </div>
        </div>
    </div>
</div>

<script>
function openTrailer(url) {
    const frame = document.getElementById('trailerFrame')
    const video = document.getElementById('trailerVideo')

    // ✅ Pause trước, rồi mới reset
    video.pause()
    video.removeAttribute('src')
    video.load()

    frame.src = ''
    frame.classList.add('d-none')
    video.classList.add('d-none')

    if (url.includes('youtube.com') || url.includes('youtu.be')) {

        let videoId = ''

        if (url.includes('watch?v=')) {
            videoId = url.split('v=')[1].split('&')[0]
        } else if (url.includes('youtu.be/')) {
            videoId = url.split('youtu.be/')[1].split('?')[0]
        }

        frame.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1'
        frame.classList.remove('d-none')

    } else {

        // ✅ Set src trực tiếp trên <video>, KHÔNG dùng <source> tag
        video.src = url
        video.classList.remove('d-none')

        // ✅ Gọi load() sau khi src đã được gán
        video.load()

    }

    const modal = new bootstrap.Modal(document.getElementById('trailerModal'))
    modal.show()
}

// ✅ Cleanup khi đóng modal
document.getElementById('trailerModal')
    .addEventListener('hidden.bs.modal', function () {
        const frame = document.getElementById('trailerFrame')
        const video = document.getElementById('trailerVideo')

        video.pause()
        video.removeAttribute('src')
        video.load()

        frame.src = ''
        frame.classList.add('d-none')
        video.classList.add('d-none')
    })
</script>