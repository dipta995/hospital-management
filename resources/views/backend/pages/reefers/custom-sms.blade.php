@extends('backend.layouts.master')
@section('title')
    List of {{ $pageHeader['title'] }}'s
@endsection
@push('styles')

@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s List</h4>
                            <p>
                                <input type="checkbox" id="select-all"> Select All
                                <button class="btn btn-primary" id="send-message-btn">
                                    Send Message
                                </button>

                            </p>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Percent (%)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $item)
                                        <tr id="table-data{{ $item->id }}">
                                            <td>
                                                <input type="checkbox" class="row-checkbox" value="{{ $item->id }}">
                                            </td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->phone }}</td>
                                            <td>{{ $item->percent }} %</td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>No record Found <a href="{{ route($pageHeader['create_route']) }}"
                                                                   class="btn btn-info">Create</a></td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="messageForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="messageModalLabel">Send Message</h5>
                        <small id="charCount" class="text-muted d-block mt-1">0 characters</small>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea name="message" id="message"  oninput="updateCharCount()" class="form-control" placeholder="Write your message..." rows="4" required></textarea>
                        <input type="hidden" name="selected_ids" id="selected_ids">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="sendButton">Send</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- main-panel ends -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Select All Checkbox Logic (already added before)
            $('#select-all').on('change', function () {
                $('.row-checkbox').prop('checked', $(this).is(':checked'));
            });

            $('.row-checkbox').on('change', function () {
                if (!$(this).is(':checked')) {
                    $('#select-all').prop('checked', false);
                } else if ($('.row-checkbox:checked').length === $('.row-checkbox').length) {
                    $('#select-all').prop('checked', true);
                }
            });

            // Open modal and collect selected IDs
            $('#send-message-btn').on('click', function () {
                let selectedIds = [];
                $('.row-checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    alert('Please select at least one row.');
                    return;
                }

                $('#selected_ids').val(selectedIds.join(','));
                $('#messageModal').modal('show');
            });

            // Handle form submission
            $('#messageForm').on('submit', function (e) {
                $('#sendButton').prop('disabled', true).text('Send');
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('admin.reefers.custom-sms-send') }}", // Replace with your actual route
                    type: 'GET',
                    data: formData,
                    success: function (response) {
                        alert('Message sent successfully.');
                        $('#messageModal').modal('hide');
                        $('#messageForm')[0].reset();
                        location.reload();

                    },
                    error: function (xhr) {
                        $('#sendButton').prop('disabled', false).text('Send');
                        alert('Something went wrong.');
                    }
                });
            });
        });
    </script>
    <script>
        function updateCharCount() {
            const message = document.getElementById('message');
            const charCount = document.getElementById('charCount');
            charCount.textContent = `${message.value.length} characters`;
        }

        // Optional: reset count when modal opens
        const messageModal = document.getElementById('messageModal');
        messageModal.addEventListener('shown.bs.modal', function () {
            updateCharCount();
        });
    </script>


@endpush
