<!-- TRAILER MODAL -->
<div class="modal fade" id="trailerModal" tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content bg-dark">

            <!-- Header -->
            <div class="modal-header border-0">
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body p-0">

                <!-- YouTube -->
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
                       controls>
                    <source id="videoSource" src="">
                    Trình duyệt không hỗ trợ video.
                </video>

            </div>

        </div>

    </div>

</div>

<script>
function openTrailer(url) {

    const frame = document.getElementById("trailerFrame")
    const video = document.getElementById("trailerVideo")
    const source = document.getElementById("videoSource")

    // Reset
    frame.src = ""
    source.src = ""
    video.pause()
    video.currentTime = 0

    frame.classList.add("d-none")
    video.classList.add("d-none")

    // YouTube
    if (url.includes("youtube.com") || url.includes("youtu.be")) {

        let videoId = url

        if (url.includes("watch?v=")) {
            videoId = url.split("v=")[1].split("&")[0]
        }

        if (url.includes("youtu.be/")) {
            videoId = url.split("youtu.be/")[1]
        }

        frame.src = "https://www.youtube.com/embed/" + videoId + "?autoplay=1"
        frame.classList.remove("d-none")

    } 
    // video file
    else {

        source.src = url
        video.load()
        video.classList.remove("d-none")

    }

    const modal = new bootstrap.Modal(
        document.getElementById("trailerModal")
    )

    modal.show()
}


// Khi đóng modal
document.getElementById("trailerModal")
.addEventListener("hidden.bs.modal", function () {

    const frame = document.getElementById("trailerFrame")
    const video = document.getElementById("trailerVideo")

    frame.src = ""

    video.pause()
    video.currentTime = 0
})
</script>