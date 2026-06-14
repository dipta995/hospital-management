<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        const submitUrl = @json($submitUrl);
        const submitMethod = @json($submitMethod ?? 'POST');
        const redirectUrl = @json($redirectUrl);
        const submitLabel = @json($submitLabel ?? 'Save Purchase');
        const isEdit = @json($isEdit ?? false);

        const select2Opts = { width: '100%', placeholder: 'Search...', allowClear: true };

        $('#supplier_id').select2({ ...select2Opts, placeholder: 'Select supplier...' });
        $('#draft_item').select2({ ...select2Opts, placeholder: 'Select item...', minimumResultsForSearch: 0 });

        function lineTotal($row) {
            const qty = parseFloat($row.find('.quantity').val()) || 0;
            const price = parseFloat($row.find('.unit_price').val()) || 0;
            const discount = parseFloat($row.find('.discount_amount').val()) || 0;
            return Math.max(qty * price - discount, 0);
        }

        function recalcRow($row) {
            $row.find('.line-total').text('৳ ' + lineTotal($row).toFixed(2));
        }

        function toggleEmptyState() {
            $('#purchase-empty-state').toggleClass('d-none', $('#step1-items .item-row').length > 0);
        }

        function recalcPurchaseTotal() {
            let subtotal = 0, lines = 0;
            $('#step1-items .item-row').each(function () {
                const id = $(this).find('.item_id').val();
                if (!id) return;
                lines++;
                subtotal += lineTotal($(this));
            });
            subtotal = Math.round(subtotal * 100) / 100;
            $('#line-count').text(lines);
            $('#line-count-badge').text(lines + (lines === 1 ? ' item' : ' items'));
            $('#subtotal_display, #total_display').each(function () {
                $(this).text('৳ ' + subtotal.toFixed(2));
            });
            $('#total_cost').val(subtotal.toFixed(2));
            recalcDue();
        }

        function recalcDue() {
            const total = parseFloat($('#total_cost').val()) || 0;
            const paid = parseFloat($('#paid_amount').val()) || 0;
            const due = Math.max(total - paid, 0);
            $('#due_amount').val(due.toFixed(2));
            $('#due_display').text('৳ ' + due.toFixed(2)).toggleClass('zero-due', due <= 0);
        }

        function showErrors(message, errors) {
            const $box = $('#purchase-form-errors');
            let html = '<strong>' + message + '</strong>';
            if (errors) {
                html += '<ul class="mb-0 mt-2 ps-3">';
                Object.values(errors).forEach(function (msgs) {
                    msgs.forEach(function (m) { html += '<li>' + m + '</li>'; });
                });
                html += '</ul>';
            }
            $box.html(html).removeClass('d-none');
            $('html, body').animate({ scrollTop: $box.offset().top - 100 }, 200);
        }

        function appendLine(itemId, itemName, itemCode, qty, price, discount, expiry) {
            const $row = $($('#purchase-item-row-template').html().trim());
            $row.find('.item_id').val(itemId);
            $row.find('.product-name').text(itemName);
            if (itemCode) {
                $row.find('.item-code').html('<code>' + itemCode + '</code>').removeClass('d-none');
            }
            $row.find('.quantity').val(qty);
            $row.find('.unit_price').val(parseFloat(price).toFixed(2));
            $row.find('.discount_amount').val(discount || 0);
            $row.find('.expiry_date').val(expiry || '');
            recalcRow($row);
            $('#step1-items').append($row);
            toggleEmptyState();
            recalcPurchaseTotal();
        }

        $('#add-line-btn').click(function () {
            const $opt = $('#draft_item').find(':selected');
            const itemId = $('#draft_item').val();
            if (!itemId) { showErrors('Please select an item first.'); return; }
            const qty = $('#draft_qty').val();
            const price = $('#draft_price').val();
            if (!qty || parseFloat(qty) < 1) { showErrors('Quantity must be at least 1.'); return; }
            if (price === '' || parseFloat(price) < 0) { showErrors('Enter a valid unit price.'); return; }

            $('#purchase-form-errors').addClass('d-none');
            appendLine(itemId, $opt.data('name') || $opt.text(), $opt.data('code') || '', qty, price, $('#draft_discount').val(), $('#draft_expiry').val());

            $('#draft_item').val('').trigger('change');
            $('#draft_qty').val(1);
            $('#draft_price, #draft_expiry').val('');
            $('#draft_discount').val(0);
        });

        $(document).on('click', '.remove-item', function () {
            $(this).closest('.item-row').remove();
            toggleEmptyState();
            recalcPurchaseTotal();
        });

        $(document).on('input', '.quantity, .unit_price, .discount_amount', function () {
            recalcRow($(this).closest('.item-row'));
            recalcPurchaseTotal();
        });

        if (!isEdit) {
            $('#paid_amount').on('input', recalcDue);
        }

        $('#submit-form').click(function () {
            $('#purchase-form-errors').addClass('d-none');
            const items = [];
            $('#step1-items .item-row').each(function () {
                const itemId = $(this).find('.item_id').val();
                if (!itemId) return;
                items.push({
                    item_id: itemId,
                    quantity: $(this).find('.quantity').val(),
                    unit_price: $(this).find('.unit_price').val(),
                    discount_amount: $(this).find('.discount_amount').val() || 0,
                    expiry_date: $(this).find('.expiry_date').val(),
                });
            });

            if (!items.length) { showErrors('Add at least one item.'); return; }
            if (!$('#supplier_id').val()) { showErrors('Please select a supplier.'); return; }
            if (!isEdit && !$('#payment_method').val()) { showErrors('Please select a payment method.'); return; }

            const payload = {
                items: items,
                supplier_id: $('#supplier_id').val(),
                purchase_date: $('#purchase_date').val(),
                total_cost: $('#total_cost').val(),
                paid_amount: $('#paid_amount').val(),
                due_amount: $('#due_amount').val(),
            };
            if (!isEdit) payload.payment_method = $('#payment_method').val();
            if (submitMethod === 'PUT') payload._method = 'PUT';

            const $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: submitUrl,
                method: 'POST',
                data: payload,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                success: function (res) {
                    window.location.href = res.redirect || redirectUrl;
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i> ' + submitLabel);
                    const res = xhr.responseJSON || {};
                    showErrors(res.message || 'Something went wrong.', res.errors);
                }
            });
        });

        $('#step1-items .item-row').each(function () { recalcRow($(this)); });
        toggleEmptyState();
        recalcPurchaseTotal();
    });
</script>
