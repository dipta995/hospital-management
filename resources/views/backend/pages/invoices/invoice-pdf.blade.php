    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Money Receipt</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1;
                width: 500px;
                margin: 0 auto;
                position: relative;
                /*transform: rotate(90deg);*/

            }

            .watermark {
                position: fixed;
                top: 30%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg); /* Centered and rotated */
                font-size: 150px; /* Increased size */
                color: rgba(0, 0, 0, 0.15); /* Adjusted transparency for a softer look */
                font-weight: bold;
                z-index: 11;
                pointer-events: none; /* Make it non-interactive */
            }

            .header {

                margin-top: -25px;
                text-align: center;
                border-bottom: 2px solid black;
                padding-bottom: 10px;
            }

            .header img {
                height: 80px;
            }

            .header h1 {
                margin: 0;
                font-size: 20px;
            }

            .header p {
                margin: 0;
                font-size: 10px;
            }

            .title {
                text-align: center;
                margin: 10px 0;
                font-size: 16px;
                font-weight: bold;
                text-decoration: underline;
            }

            .details, .tests {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            .details th, .details td, .tests th, .tests td {
                border: 1px solid black;
                padding: 4px;
                text-align: left;
                font-size: 10px;
            }

            .details th, .tests th {
                background-color: #f2f2f2;
                text-align: center;
            }

            .tests th {
                font-size: 12px;
            }

            .details td {
                /*font-weight: bold;*/
            }

            .financial-summary {
                text-align: right;
                font-size: 14px;
                margin-right: 20px;
            }

            .financial-summary span {
                font-weight: bold;
            }
        </style>
    </head>
    <body>
    <div class="header"
         style="display: table; width: 100%; border-bottom: 2px solid black; padding-bottom: 10px; border: 1px solid black;">
        <div style="display: table-cell; width: 40%; vertical-align: middle;">
            <img src="{{ public_path('images/'.\App\Models\Setting::get('logo')) }}" alt="Logo" style="height: 80px;">
        </div>
        <div style=" display: table-cell; width: 60%; text-align: left; vertical-align: middle; color:#000;">
            <h1 style="margin: 0; font-size: 16px;">{{ \App\Models\Setting::get('company_name') }}</h1>
            <p style="margin: 0; font-size: 10px;">{!! \App\Models\Setting::get('address') !!}</p>
            <p style="margin: 0; font-size: 10px;">Mobile: {{ \App\Models\Setting::get('phone_one') }}
                , {{ \App\Models\Setting::get('phone_two') }}</p>
            <p style="margin: 0; font-size: 10px;">Email: {{ \App\Models\Setting::get('email') }}</p>
        </div>
        <div style="display: table-cell; width: 25%; text-align: left; vertical-align: middle; color:#000;">
            <img style="width: 80px; float: right; margin-top: 10px;" src="data:image/png;base64,{{ $qrcode }}"
                 alt="QR Code">
        </div>
    </div>
    {{--<hr style="background-color: black; margin-top: 3px;">--}}


    <div class="title">Money Receipt</div>
    <table class="details">
        <tr>
            <th>Patient ID</th>
            <td>{{ $invoice->patient_no }}</td>
            <th>Date</th>
            <td>{{ $invoice->created_at }}</td>
        </tr>
        <tr>
            <th>Bill No</th>
            <td>{{ $invoice->invoice_number }}</td>
            <th>Discount By</th>
            <td>{{ $invoice->discount_by }}</td>
        </tr>
        <tr>
            <th>Patient's Name</th>
            <td>{{ $invoice->patient_name }}</td>
            <th>Mobile</th>
            <td>{{ $invoice->patient_phone }}</td>
        </tr>
        <tr>
            <th>Age</th>
            <td>{{ $invoice->patient_age_year }} </td>
            <th>B.Group</th>
            <td>{{ $invoice->patient_blood_group }}</td>
        </tr>
        <tr>
            <th>Gender</th>
            <td>{{ $invoice->patient_gender }}</td>
            <th>Address</th>
            <td>{{ $invoice->patient_address }}</td>
        </tr>
        <tr>
            <th>Doctor name</th>
            <td colspan="3">{{ $invoice->reeferDr->name ?? 'NA' }}</td>
        </tr>
    </table>
    <table class="tests">
        <thead>
        <tr>
            <th  style="text-align: left; width: 60px;">Code</th>
            <th>Test Name</th>
            <th  style="text-align: right; width: 70px;" >Amount (tk)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->invoiceList as $key=>$item)
            <tr>
                <td  style="text-align: left; width: 60px;">{{ $item->product->code }}</td>
                <td>{{ $item->product->name }}</td>
                <td style="text-align: right; width: 70px;">{{ $item->price }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot style="margin-top: 10px;">
        <tr>
            <td colspan="3" style="height: 5px; border: none;"></td>
        </tr>
        <tr>
            <td style="height: 5px; border: none;"></td>
            <th style="text-align: right;">Sub Total</th>
            <th style="text-align: right;">{{ $invoice->total_amount+$invoice->discount_amount }}</th>
        </tr>
        <tr>
            <td style="height: 5px; border: none;">
                <div style="width: 100px; float: right;">
                    <h1 style="text-align: center; border: 2px solid black; border-radius: 25%;"></h1>
                </div>
            </td>
            <th style="text-align: right;">Less [-]</th>
            <th style="text-align: right;">{{ $invoice->discount_amount }}</th>
        </tr>
        <tr>
            <td style="height: 5px; border: none;"></td>
            <th style="text-align: right;">Received Amount</th>
            <th style="text-align: right;"></th>
        </tr>
        <tr>
            <td style="height: 5px; border: none;"></td>
            <th style="text-align: right;">Due</th>
            <th style="text-align: right;"></th>
        </tr>

        </tfoot>
    </table>
    <p style="font-size: 12px;">
    <strong  style="text-align: left; float: left;">Posting By : {{ $invoice->admin->name ?? 'N/A' }}</strong>
    <span style="text-align: right; float:right;">Delivery At :
        <span>{{ \Carbon\Carbon::parse($invoice->delivery_at)->format('d-m-Y h:i A') }}</span>
    </span>
    </p>

    <br>

   <div style="width:100%; margin-top: -10px;">
       <span style="text-align: left;">Rooms :</span>
         <table class="tests" >
          <tr>
              <td style="width: 50px; float: left;">Collection</td>
              @foreach($invoice->invoiceList->pluck('product.category')->unique('room_no') as $category)
                <td style="width: 50px; float: left;"> {{ $category->room_name }}</td>
                <td style="width: 50px; float: left;">{{ $category->room_no }}</td>
            @endforeach
          </tr>

        </table>

    </div>

    <img src="{{ public_path('note.png') }}" alt="Logo" style="height: 20px;">

    {{--{!! \App\Models\Setting::get('footer_invoice') !!}--}}
    <span style="text-align: right; font-size: 10px;"> {{ $invoice->id }}</span>
    </body>
    </html>
