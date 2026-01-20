<div class="modal fade" id="receptPaymentModal" tabindex="-1" aria-labelledby="receptPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receptPaymentModalLabel">Recept Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="receptPaymentForm">
                    @csrf
                    <input type="hidden" name="recept_id" id="recept_id">
                    <div class="mb-3">
                        <label class="form-label">Due Amount</label>
                        <input type="text" class="form-control" id="recept_due" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Balance</label>
                        <input type="text" class="form-control" id="recept_balance" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pay Amount</label>
                        <input type="number" name="amount" id="pay_amount" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="pay_from_balance" name="pay_from_balance">
                        <label class="form-check-label" for="pay_from_balance">
                            Pay from balance
                        </label>
                    </div>
                    <div class="alert alert-danger d-none" id="receptPaymentError"></div>
                    <div class="alert alert-success d-none" id="receptPaymentSuccess"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="receptPaymentSubmit">Pay</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function($){
        'use strict';

        let paymentModal = $('#receptPaymentModal');

        paymentModal.on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget);
            let id = button.data('id');
            let due = button.data('due');
            let balance = button.data('balance');

            $('#recept_id').val(id);
            $('#recept_due').val(parseFloat(due).toFixed(2));
            $('#recept_balance').val(parseFloat(balance).toFixed(2));
            $('#pay_amount').val(parseFloat(due).toFixed(2));
            $('#pay_from_balance').prop('checked', false);
            $('#receptPaymentError').addClass('d-none').text('');
            $('#receptPaymentSuccess').addClass('d-none').text('');
        });

        $('#receptPaymentSubmit').on('click', function () {
            let id = $('#recept_id').val();
            let url = "{{ route('admin.recepts.pay', ':id') }}".replace(':id', id);
            let formData = $('#receptPaymentForm').serialize();

            $('#receptPaymentSubmit').prop('disabled', true);

            $.ajax({
                method: 'POST',
                url: url,
                data: formData,
                success: function (response) {
                    if (response.status === 200) {
                        $('#receptPaymentSuccess').removeClass('d-none').text(response.message || 'Payment successful');
                        $('#receptPaymentError').addClass('d-none').text('');
                        setTimeout(function(){
                            location.reload();
                        }, 800);
                    } else {
                        $('#receptPaymentError').removeClass('d-none').text(response.message || 'Something went wrong');
                        $('#receptPaymentSuccess').addClass('d-none').text('');
                    }
                },
                error: function (xhr) {
                    let msg = 'Something went wrong';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    $('#receptPaymentError').removeClass('d-none').text(msg);
                    $('#receptPaymentSuccess').addClass('d-none').text('');
                },
                complete: function(){
                    $('#receptPaymentSubmit').prop('disabled', false);
                }
            });
        });
    })(jQuery);
</script>
@endpush
