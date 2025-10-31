<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Serial List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        .serial-box {
            padding: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 10px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            width: 150px;
            display: inline-block;
        }
        .green { background-color: #28a745; color: white; }
        .red { background-color: #dc3545; color: white; }
        .yellow { background-color: #ffc107; color: black; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center">Doctor Serial List</h2>
    <div class="text-center mb-3">
        <strong>Room No: </strong><span id="roomNo">Loading...</span>
    </div>
    <div id="serialList" class="d-flex flex-wrap justify-content-center"></div>
</div>

<script>
    function fetchSerialList() {
        $.ajax({
            url: '/serials/list/' + {{ $reeferId }} + '/' + {{ $branchId }},
            type: 'GET',
            success: function(response) {
                let serials = response.serials;
                let roomNo = response.available_room;

                $('#roomNo').text(roomNo || 'N/A');
                let serialHtml = '';

                serials.forEach((serial, index) => {
                    let colorClass = index === 0 ? 'green' : index === 1 ? 'red' : 'yellow';
                    serialHtml += `<div class="serial-box ${colorClass}">Serial: ${serial.serial_number}</div>`;
                });

                $('#serialList').html(serialHtml);
            }
        });
    }

    $(document).ready(function() {
        fetchSerialList();
        setInterval(fetchSerialList, 2000); // Refresh every 2 seconds
    });
</script>
</body>
</html>
