<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-inv-hscroll]').forEach(function (wrap) {
            var top = wrap.querySelector('.inv-hscroll-top');
            var body = wrap.querySelector('.inv-hscroll-body');
            var inner = wrap.querySelector('.inv-hscroll-top-inner');
            var table = wrap.querySelector('table');
            if (!top || !body || !inner || !table) {
                return;
            }

            function syncWidth() {
                inner.style.width = table.scrollWidth + 'px';
            }

            function syncScroll(source, target) {
                if (target.scrollLeft !== source.scrollLeft) {
                    target.scrollLeft = source.scrollLeft;
                }
            }

            top.addEventListener('scroll', function () {
                syncScroll(top, body);
            });

            body.addEventListener('scroll', function () {
                syncScroll(body, top);
            });

            syncWidth();
            window.addEventListener('resize', syncWidth);
        });
    });
</script>
