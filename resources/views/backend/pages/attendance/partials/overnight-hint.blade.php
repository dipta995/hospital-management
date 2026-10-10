<script>
    (function () {
        function refresh(form) {
            var inInput = form.querySelector('input[name="in_time"]');
            var outInput = form.querySelector('input[name="out_time"]');
            if (!inInput || !outInput) {
                return;
            }
            var hint = form.querySelector('.overnight-hint');
            if (!hint) {
                hint = document.createElement('small');
                hint.className = 'overnight-hint d-block fw-semibold text-primary mt-1';
                outInput.insertAdjacentElement('afterend', hint);
            }
            var nextDay = inInput.value && outInput.value && outInput.value < inInput.value;
            hint.textContent = nextDay ? '+1 day: OUT is on the next day (night duty)' : '';
        }

        document.addEventListener('input', function (event) {
            if (event.target.name === 'in_time' || event.target.name === 'out_time') {
                var form = event.target.closest('form');
                if (form) {
                    refresh(form);
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form').forEach(refresh);
        });
    })();
</script>
