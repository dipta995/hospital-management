@if(session('success') || session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swal === 'undefined') {
            return;
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: function (toast) {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        function playAuthSound(type) {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) {
                    return;
                }

                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);

                if (type === 'login') {
                    osc.frequency.setValueAtTime(523.25, ctx.currentTime);
                    osc.frequency.setValueAtTime(659.25, ctx.currentTime + 0.1);
                    osc.frequency.setValueAtTime(783.99, ctx.currentTime + 0.2);
                    gain.gain.setValueAtTime(0.15, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.45);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.45);
                } else if (type === 'logout') {
                    osc.frequency.setValueAtTime(440, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(330, ctx.currentTime + 0.25);
                    gain.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.35);
                } else if (type === 'error') {
                    osc.type = 'square';
                    osc.frequency.setValueAtTime(220, ctx.currentTime);
                    osc.frequency.setValueAtTime(180, ctx.currentTime + 0.12);
                    gain.gain.setValueAtTime(0.1, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.3);
                } else {
                    osc.frequency.setValueAtTime(523.25, ctx.currentTime);
                    gain.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.25);
                }
            } catch (e) {
                // Ignore if browser blocks audio.
            }
        }

        @if(session('success'))
        Toast.fire({
            icon: 'success',
            title: @json(session('success'))
        });
        @endif

        @if(session('error'))
        Toast.fire({
            icon: 'error',
            title: @json(session('error'))
        });
        @endif

        @if(session('auth_event'))
        playAuthSound(@json(session('auth_event')));
        @elseif(session('success'))
        playAuthSound('success');
        @elseif(session('error'))
        playAuthSound('error');
        @endif
    });
</script>
@endif
