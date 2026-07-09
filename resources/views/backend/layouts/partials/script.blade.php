<!-- END Wrapper -->

<!-- Vendor Javascript (Require in all Page) -->
<script src="{{ asset('backend/assets/js/vendor.js') }}"></script>

<!-- App Javascript (Require in all Page) -->
<script src="{{ asset('backend/assets/js/app.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var menuBtn = document.querySelector('.button-toggle-menu');
        if (!menuBtn) return;

        function closeSidebar() {
            document.body.classList.remove('sidebar-enable');
            document.documentElement.classList.remove('sidebar-enable');
            var backdrop = document.querySelector('.offcanvas-backdrop');
            if (backdrop) {
                backdrop.remove();
                document.body.style.overflow = null;
                document.body.style.paddingRight = null;
            }
        }

        function openSidebar() {
            document.body.classList.add('sidebar-enable');
            document.documentElement.classList.add('sidebar-enable');
            if (!document.querySelector('.offcanvas-backdrop')) {
                var backdrop = document.createElement('div');
                backdrop.className = 'offcanvas-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        }

        function syncSidebarClass() {
            var enabled = document.body.classList.contains('sidebar-enable');
            document.documentElement.classList.toggle('sidebar-enable', enabled);
        }

        function isMobileNav() {
            return window.innerWidth < 992;
        }

        menuBtn.addEventListener('click', function (event) {
            if (!isMobileNav()) {
                setTimeout(syncSidebarClass, 0);
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            if (document.body.classList.contains('sidebar-enable')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }, true);

        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('offcanvas-backdrop')) {
                closeSidebar();
            }
        });

        document.querySelectorAll('.main-nav a.nav-link, .main-nav a.sub-nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (isMobileNav()) {
                    closeSidebar();
                }
            });
        });

        window.addEventListener('resize', function () {
            if (!isMobileNav()) {
                closeSidebar();
            }
        });
    });
</script>

<!-- Vector Map Js -->
<script src="{{ asset('backend/assets/vendor/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('backend/assets/vendor/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('backend/assets/vendor/jsvectormap/maps/world.js') }}"></script>

<!-- Dashboard Js -->
<script src="{{ asset('backend/assets/js/pages/dashboard.js') }}"></script>
<script src="{{ asset('backend/assets/vendor/jsvectormap/jquery.min.js') }}"></script>
<script src="{{ asset('backend/assets/vendor/summernote/summernote-lite.min.js') }}"></script>
@include('backend.layouts.partials.app-ui-scripts')

<script>
{{--    Full Screen Mode --}}
document.addEventListener('DOMContentLoaded', function () {
    const fullscreenButton = document.querySelector('[data-toggle="fullscreen"]');
    if (!fullscreenButton) return;
    const fullscreenIcon = fullscreenButton.querySelector('.fullscreen');
    const quitFullscreenIcon = fullscreenButton.querySelector('.quit-fullscreen');
    if (!fullscreenIcon || !quitFullscreenIcon) return;

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


function activeData(id, url_base_name) {
    $.ajax({
        url: url_base_name + "/status/" + id,
        type: "GET",
        data: {
            _token: $("input[name=_token]").val()
        },
        success: function () {
            AppUi.toastSuccess('{{ t('common.success') }}');
            location.reload();
        },
        error: function () {
            AppUi.toastError('{{ t('common.error') }}');
        },
    });
}
</script>
