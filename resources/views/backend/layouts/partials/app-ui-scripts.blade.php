@php
    $appUiLabels = [
        'delete_title' => t('common.delete_confirm_title'),
        'delete_text' => t('common.delete_confirm_text'),
        'delete_yes' => t('common.delete_confirm_yes'),
        'delete_cancel' => t('common.delete_confirm_cancel'),
        'deleted_success' => t('common.deleted_success'),
        'delete_failed' => t('common.delete_failed'),
        'something_wrong' => t('common.something_wrong'),
        'tip_view' => t('common.action_tip_view'),
        'tip_edit' => t('common.action_tip_edit'),
        'tip_delete' => t('common.action_tip_delete'),
        'tip_print' => t('common.action_tip_print'),
        'tip_invoice' => t('common.action_tip_invoice'),
        'tip_pay' => t('common.action_tip_pay'),
    ];
@endphp
<script>
(function (window, $) {
    'use strict';

    var LABELS = @json($appUiLabels);

    var ICON_TIP_MAP = [
        { match: /fa-trash|fa-trash-alt/, label: LABELS.tip_delete },
        { match: /fa-pen|fa-pencil|fa-edit/, label: LABELS.tip_edit },
        { match: /fa-eye/, label: LABELS.tip_view },
        { match: /fa-print/, label: LABELS.tip_print },
        { match: /fa-file-invoice|fa-file-invoice-dollar/, label: LABELS.tip_invoice },
        { match: /fa-wallet|fa-money|fa-coins/, label: LABELS.tip_pay },
        { match: /fa-id-card/, label: @json(t('common.patient_profile')) },
        { match: /fa-flask/, label: @json(t('common.create_invoice')) },
        { match: /fa-bed/, label: @json(t('common.admit_patient')) },
        { match: /fa-list/, label: @json(t('common.admit_history')) },
    ];

    function toastMixin() {
        if (typeof Swal === 'undefined') {
            return null;
        }

        return Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3800,
            timerProgressBar: true,
            customClass: {
                popup: 'app-toast-popup',
            },
            didOpen: function (toast) {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
        });
    }

    function toastSuccess(message) {
        var Toast = toastMixin();
        if (!Toast) {
            return;
        }
        Toast.fire({ icon: 'success', title: message || LABELS.deleted_success });
    }

    function toastError(message) {
        var Toast = toastMixin();
        if (!Toast) {
            return;
        }
        Toast.fire({ icon: 'error', title: message || LABELS.something_wrong });
    }

    function confirmDelete(options) {
        options = options || {};

        if (typeof Swal === 'undefined') {
            if (window.confirm(options.title || LABELS.delete_title)) {
                if (typeof options.onConfirm === 'function') {
                    options.onConfirm();
                }
            }
            return;
        }

        Swal.fire({
            title: options.title || LABELS.delete_title,
            text: options.html ? undefined : (options.text || LABELS.delete_text),
            html: options.html ? (options.html + '<p class="mt-3 mb-0 text-center text-muted" style="font-size:0.9rem">' + (options.text || LABELS.delete_text) + '</p>') : undefined,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: options.confirmText || LABELS.delete_yes,
            cancelButtonText: options.cancelText || LABELS.delete_cancel,
            reverseButtons: true,
            focusCancel: true,
            width: options.html ? '520px' : undefined,
            customClass: {
                popup: 'app-delete-popup',
            },
        }).then(function (result) {
            if (result.isConfirmed && typeof options.onConfirm === 'function') {
                options.onConfirm();
            }
        });
    }

    function iconTipFromClass(className) {
        for (var i = 0; i < ICON_TIP_MAP.length; i++) {
            if (ICON_TIP_MAP[i].match.test(className)) {
                return ICON_TIP_MAP[i].label;
            }
        }
        return '';
    }

    function visibleText(el) {
        var clone = el.cloneNode(true);
        clone.querySelectorAll('i, svg, .crud-tip-bubble').forEach(function (node) {
            node.remove();
        });
        return (clone.textContent || '').replace(/\s+/g, ' ').trim();
    }

    function resolveTipLabel(el) {
        if (el.dataset.tip) {
            return el.dataset.tip;
        }
        if (el.getAttribute('aria-label')) {
            return el.getAttribute('aria-label');
        }
        if (el.title) {
            return el.title;
        }

        var text = visibleText(el);
        if (text) {
            return text.length > 28 ? text.slice(0, 28) + '…' : text;
        }

        var icon = el.querySelector('i[class*="fa"]');
        if (icon) {
            return iconTipFromClass(icon.className);
        }

        return '';
    }

    function attachTip(el, label) {
        if (!label || el.querySelector('.crud-tip-bubble')) {
            el.removeAttribute('title');
            return;
        }

        var tip = document.createElement('span');
        tip.className = 'crud-tip-bubble';
        tip.setAttribute('role', 'tooltip');
        tip.textContent = label;
        el.insertBefore(tip, el.firstChild);
        el.classList.add('has-action-tip');
        el.removeAttribute('title');
    }

    function initTooltips(root) {
        root = root || document;

        var selectors = [
            '.crud-btn-icon',
            '.crud-action-group a',
            '.crud-action-group button',
            '.crud-action-group .badge',
            '.inv-act',
            'table tbody td a.badge',
            'table tbody td button.badge',
            '[data-action-tip]',
        ];

        root.querySelectorAll(selectors.join(',')).forEach(function (el) {
            if (el.querySelector('.crud-tip-bubble')) {
                el.removeAttribute('title');
                return;
            }

            var label = el.dataset.actionTip || resolveTipLabel(el);
            if (!label) {
                return;
            }

            attachTip(el, label);
        });
    }

    function deleteRequest(options) {
        options = options || {};

        confirmDelete({
            title: options.title,
            text: options.text,
            html: options.previewHtml,
            onConfirm: function () {
                if (options.useFormSubmit) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = options.url;
                    form.innerHTML = '<input type="hidden" name="_token" value="' + (options.token || '') + '"><input type="hidden" name="_method" value="DELETE">';
                    document.body.appendChild(form);
                    form.submit();
                    return;
                }

                var token = options.token || $('meta[name="csrf-token"]').attr('content');
                var ajaxOptions = {
                    url: options.url,
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    success: function (response) {
                        var ok = response && (response.status === 200 || response.status === '200' || response.success === true);
                        if (options.removeRow !== false && ok) {
                            $('#table-data' + options.id).remove();
                        } else if (options.removeRow !== false && !response.status && options.treatNoStatusAsSuccess) {
                            $('#table-data' + options.id).remove();
                            ok = true;
                        }

                        if (ok) {
                            toastSuccess(options.successMessage || LABELS.deleted_success);
                            if (typeof options.onSuccess === 'function') {
                                options.onSuccess(response);
                            }
                            if (options.reloadOnSuccess) {
                                location.reload();
                            }
                        } else {
                            toastError(response && response.message ? response.message : LABELS.delete_failed);
                        }
                    },
                    error: function (xhr) {
                        var message = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : LABELS.something_wrong;
                        toastError(message);
                    },
                };

                if (options.method === 'GET') {
                    ajaxOptions.type = 'GET';
                    ajaxOptions.data = { _token: token };
                } else {
                    ajaxOptions.type = 'POST';
                    ajaxOptions.data = { _token: token, _method: 'DELETE' };
                }

                $.ajax(ajaxOptions);
            },
        });
    }

    window.AppUi = {
        labels: LABELS,
        toastMixin: toastMixin,
        toastSuccess: toastSuccess,
        toastError: toastError,
        confirmDelete: confirmDelete,
        initTooltips: initTooltips,
        deleteRequest: deleteRequest,
    };

    window.dataDelete = function (id, baseUrl, options) {
        options = options || {};
        deleteRequest({
            id: id,
            url: baseUrl + '/' + id,
            token: options.token || $('meta[name="csrf-token"]').attr('content'),
            title: options.title,
            text: options.text,
            successMessage: options.successMessage,
            onSuccess: options.onSuccess,
            reloadOnSuccess: options.reloadOnSuccess,
            useFormSubmit: options.useFormSubmit,
            treatNoStatusAsSuccess: options.treatNoStatusAsSuccess,
            removeRow: options.removeRow,
        });
    };

    window.deleteData = function (id, urlBaseName) {
        deleteRequest({
            id: id,
            url: urlBaseName + '/delete/' + id,
            method: 'GET',
            treatNoStatusAsSuccess: true,
        });
    };

    window.deleteDataWithDetails = function (id, urlBaseName, details) {
        deleteRequest({
            id: id,
            url: urlBaseName + '/delete/' + id,
            method: 'GET',
            title: LABELS.delete_title.replace('?', '') + ' ' + details + '?',
            treatNoStatusAsSuccess: true,
        });
    };

    window.costDataDelete = function (id, baseUrl) {
        dataDelete(id, baseUrl, {
            treatNoStatusAsSuccess: true,
            onSuccess: function (response) {
                if (response && response.employee_id && typeof window.updateEmployeeAfterCost === 'function') {
                    window.updateEmployeeAfterCost(response.employee_id);
                }
            },
        });
    };

    window.pharmSaleDelete = function (id, baseUrl) {
        dataDelete(id, baseUrl, {
            text: 'Stock will not auto-restore.',
            useFormSubmit: true,
            removeRow: false,
        });
    };

    window.invoiceDataDelete = function (id, baseUrl, previewUrl) {
        previewUrl = previewUrl || (baseUrl + '/delete-preview/' + id);

        $.ajax({
            url: previewUrl,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            success: function (response) {
                deleteRequest({
                    id: id,
                    url: baseUrl + '/' + id,
                    previewHtml: response.html || '',
                    title: LABELS.delete_title,
                    text: LABELS.delete_text,
                });
            },
            error: function () {
                dataDelete(id, baseUrl);
            },
        });
    };

    document.addEventListener('DOMContentLoaded', function () {
        initTooltips();
    });
})(window, jQuery);
</script>
