<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voice Calling System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light text-center py-4">
<style>
    strong { font-size: 40px; cursor: pointer; }
</style>

<div class="container">
    <h2 class="text-primary mb-4">Voice Calling System</h2>
    <p id="current-user" class="alert alert-info">Waiting for the first call...</p>
    <button id="start-call" class="btn btn-success mb-4">Start Calling</button>
    <div id="users-container" class="row"></div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusLabel">Update Serial Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <input type="hidden" id="serialId">
                    <p><strong>Serial Number:</strong> <span id="serialNumberText"></span></p>
                    <label for="statusSelect" class="form-label">Select New Status</label>
                    <select id="statusSelect" class="form-select">
                        @foreach(\App\Models\DoctorSerial::$statusArray as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveStatus" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let users = [];
        let roomNumber = null;

        $("#start-call").click(function () {
            fetchSerials();
        });

        function fetchSerials() {
            $.ajax({
                url: '/serials/list/' + {{ $reeferId }} + '/' + {{ $branchId }},
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (!data.serials || data.serials.length === 0) {
                        $("#current-user").text("No pending calls.");
                        return;
                    }

                    users = data.serials.slice(0, 10);
                    roomNumber = data.available_room;
                    renderUsers(users);
                    // Call the first two users after fetching data
                    if (users.length > 0) {
                        callUsers(users.slice(0, 2));
                    }
                }
            });
        }

        function renderUsers(data) {
            let container = $("#users-container");
            container.empty();
            data.forEach(user => {
                let userHtml = `
                    <div class="col-md-4">
                        <div class="card border-primary mb-3">
                            <div class="card-body">
                                <p class="list-group-item user-${user.id}">
                                    <strong class="serial-number" data-id="${user.id}" data-serial="${user.serial_number}" data-status="${user.status}">
                                        ${user.serial_number}
                                    </strong> - ${user.status}
                                </p>
                            </div>
                        </div>
                    </div>`;
                container.append(userHtml);
            });

            $(".serial-number").click(function () {
                let userId = $(this).data("id");
                let serialNumber = $(this).data("serial");
                let currentStatus = $(this).data("status");

                $("#serialId").val(userId);
                $("#serialNumberText").text(serialNumber);
                $("#statusSelect").val(currentStatus);
                $("#updateStatusModal").modal("show");
            });
        }

        $("#saveStatus").click(function () {
            let serialId = $("#serialId").val();
            let newStatus = $("#statusSelect").val();
            updateStatus(serialId, newStatus);
        });

        function updateStatus(userId, newStatus) {
            $.ajax({
                url: '/update-serial-status',
                method: 'POST',
                data: {
                    id: userId,
                    status: newStatus,
                    _token: "{{ csrf_token() }}"
                },
                success: function () {
                    toastr.success("Status updated successfully!");
                    $("#updateStatusModal").modal("hide");
                    fetchSerials();
                },
                error: function () {
                    toastr.error("Failed to update status.");
                }
            });
        }

        async function callUsers(userList) {
            for (let i = 0; i < userList.length; i++) {
                let user = userList[i];
                if (i < userList.length - 1) {
                    $(".user-" + user.id).addClass("bg-warning");
                if (roomNumber) {
                    await playAudio(`{{ asset('voice') }}/room.mp3`);
                    await playRoomNumber(roomNumber);
                }
                await playAudio("{{ asset('voice') }}/now.mp3");

                await playSerialNumber(user.serial_number);
                $(".user-" + user.id).removeClass("bg-warning").addClass("bg-success text-white");


                    await playAudio("{{ asset('voice') }}/next.mp3");
                }else{
                    $(".user-" + user.id).removeClass("bg-warning").addClass("bg-success text-white");

                    await playSerialNumber(user.serial_number);
                }
            }
        }

        async function playRoomNumber(roomNumber) {
            let digits = roomNumber.toString().split('');
            for (let digit of digits) {
                await playAudio(`{{ asset('voice') }}/${digit}.mp3`);
            }
        }

        async function playSerialNumber(serialNumber) {
            let digits = serialNumber.toString().split('');
            for (let digit of digits) {
                await playAudio(`{{ asset('voice') }}/${digit}.mp3`);
            }
        }

        function playAudio(filePath) {
            return new Promise(resolve => {
                let audio = new Audio(filePath);
                audio.play().then(() => {
                    audio.onended = resolve;
                    audio.onerror = resolve;
                }).catch(() => resolve());
            });
        }
    });
</script>

</body>
</html>
