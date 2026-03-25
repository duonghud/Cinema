<!-- TRAILER MODAL -->
<div class="modal fade" id="trailerModal" tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content bg-dark">

            <div class="modal-header border-0">

                <h5 class="modal-title text-white">Trailer</h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body p-0">

                <iframe
                    id="trailerFrame"
                    width="100%"
                    height="500"
                    src=""
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>

            </div>

        </div>

    </div>

</div>


<script>
    function openTrailer(url) {

        const frame = document.getElementById("trailerFrame")

        // Tách ID video nếu truyền cả link
        let videoId = url

        if (url.includes("youtube.com/watch?v=")) {
            videoId = url.split("v=")[1].split("&")[0]
        }

        if (url.includes("youtu.be/")) {
            videoId = url.split("youtu.be/")[1]
        }

        frame.src = "https://www.youtube.com/embed/" + videoId + "?autoplay=1&rel=0"

        const modal = new bootstrap.Modal(
            document.getElementById("trailerModal")
        )

        modal.show()

    }


    // Khi đóng modal -> dừng video
    document.getElementById("trailerModal").addEventListener(
        "hidden.bs.modal",
        function() {

            document.getElementById("trailerFrame").src = ""

        }
    )
</script>
