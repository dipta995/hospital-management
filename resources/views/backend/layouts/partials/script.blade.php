<!-- END Wrapper -->

<!-- Vendor Javascript (Require in all Page) -->
<script src="{{ asset('backend/assets/js/vendor.js') }}"></script>

<!-- App Javascript (Require in all Page) -->
<script src="{{ asset('backend/assets/js/app.js') }}"></script>

<!-- Vector Map Js -->
<script src="{{ asset('backend/assets/vendor/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('backend/assets/vendor/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('backend/assets/vendor/jsvectormap/maps/world.js') }}"></script>

<!-- Dashboard Js -->
<script src="{{ asset('backend/assets/js/pages/dashboard.js') }}"></script>
<script src="{{ asset('backend/assets/vendor/jsvectormap/jquery.min.js') }}"></script>
<script src="{{ asset('backend/assets/vendor/summernote/summernote-lite.min.js') }}"></script>

<script>
{{--    Full Screen Mode --}}
document.addEventListener('DOMContentLoaded', function () {
    const fullscreenButton = document.querySelector('[data-toggle="fullscreen"]');
    const fullscreenIcon = fullscreenButton.querySelector('.fullscreen');
    const quitFullscreenIcon = fullscreenButton.querySelector('.quit-fullscreen');

    // Hide the quit fullscreen icon initially
    quitFullscreenIcon.style.display = 'none';

    fullscreenButton.addEventListener('click', function () {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().then(() => {
                fullscreenIcon.style.display = 'none';
                quitFullscreenIcon.style.display = 'inline-block';
            }).catch((err) => {
                console.log(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
            });
        } else {
            document.exitFullscreen().then(() => {
                fullscreenIcon.style.display = 'inline-block';
                quitFullscreenIcon.style.display = 'none';
            }).catch((err) => {
                console.log(`Error attempting to disable full-screen mode: ${err.message} (${err.name})`);
            });
        }
    });

    // Handle escape key to exit full-screen mode
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            fullscreenIcon.style.display = 'inline-block';
            quitFullscreenIcon.style.display = 'none';

            // Create and append the div
            const offcanvasBackdrop = document.createElement('div');
            offcanvasBackdrop.className = 'offcanvas-backdrop fade show';

            // Append to the body
            document.body.appendChild(offcanvasBackdrop);
        }
    });


});
document.addEventListener('DOMContentLoaded', function () {
    const topbarButton = document.querySelector('.topbar-button');

    topbarButton.addEventListener('click', function () {
        // Toggle the "sidebar-enable" class on the <html> element
        document.documentElement.classList.toggle('sidebar-enable');
    });
});


function deleteData(id, url_base_name) {
    var sweet_loader = '<div class="sweet_loader"><svg viewBox="0 0 140 140" width="140" height="140"><g class="outline"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="rgba(0,0,0,0.1)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></g><g class="circle"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="#71BBFF" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="200" stroke-dasharray="300"></path></g></svg></div>';
    Swal.fire({
        title: "Sei sicuro di voler cancellare ?",
        text: "Questo dato potrebbe non essere recuperabile! ",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si cancella!'
    }).then((result) => {
        if (result.isConfirmed) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
            $.ajax({
                url: url_base_name + "/delete/" + id,
                type: "GET",
                data: {
                    _token: $("input[name=_token]").val()
                },

                success: function (response) {
                    if (response.status == 200) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Cancellato!'
                        })
                        $("#table-data" + id).remove();

                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Qualcosa non ha funzionato !'
                        })
                    }
                },
                error: function (response) {

                },
            });
        }
    })
}

function deleteDataWithDetails(id, url_base_name, details) {
    Swal.fire({
        title: "Sei sicuro di voler cancellare " + details + "?",
        text: "Questo dato potrebbe non essere recuperabile! ",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si cancella!'
    }).then((result) => {
        if (result.isConfirmed) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
            $.ajax({
                url: url_base_name + "/delete/" + id,
                type: "GET",
                data: {
                    _token: $("input[name=_token]").val()
                },
                success: function (response) {
                    if (response.status == 200) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Cancellato!'
                        })
                        $("#table-data" + id).remove();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Qualcosa non ha funzionato !'
                        })
                    }
                },
                error: function (response) {

                },
            });
        }
    })
}
function activeData(id, url_base_name) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    $.ajax({
        url: url_base_name + "/status/" + id,
        type: "GET",
        data: {
            _token: $("input[name=_token]").val()
        },
        success: function (response) {

            Toast.fire({
                icon: 'success',
                title: 'Successo !'
            })
            location.reload();
        },
        error: function (response) {
            Toast.fire({
                icon: 'error',
                title: 'Opps! Qualcosa non ha funzionato.'
            })
        },

    });


}
    function dataDelete(id, baseUrl) {
        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: baseUrl + '/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status === 200) {
                        $('#table-data' + id).remove();
                        alert('Deleted successfully!');
                    } else {
                        alert('Delete failed. Please try again.');
                    }
                },
                error: function () {
                    alert('Something went wrong!');
                }
            });
        }
    }
</script>
