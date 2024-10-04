<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRINT SLIPS</title>
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: 8.5in 11in;
            margin-top: 1%;
            margin-bottom: 1%;
        }

        body {
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 10px;
        }

        .column {
            float: left;
            width: 50%;
            padding: 1px;
            box-sizing: border-box;
            height: 5.5in;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .slip {
            border: 1px solid #000;
            padding: 10px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: auto;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            position: relative;
        }

        .logo-left,
        .logo-right {
            position: absolute;
            top: 0;
        }

        .logo-left {
            left: 10px;
        }

        .logo-right {
            right: 10px;
        }

        .header p {
            margin: 0;
        }

        .header h3 {
            margin-top: 10px;
            margin-bottom: 0;
        }

        h3 {
            text-decoration: underline;
            font-style: italic;
        }

        .order-info {
            font-size: 12px;
            margin: 5px 0;
            padding: 5px;
            border: 1px solid #000;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            align-items: center;
            gap: 5px;
        }

        .order-info p {
            margin: 0;
            line-height: 1.2;
        }

        .right-column {
            text-align: right;
        }

        .content {
            flex-grow: 1;
            overflow: hidden;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .content th,
        .content td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            box-sizing: border-box;
            font-size: 10px;
            word-wrap: break-word;
            height: 16px;
        }

        .content th {
            height: 0.3in;
            font-size: 10px;
        }

        .content th:nth-child(1),
        .content td:nth-child(1) {
            width: 10%;
        }

        .content th:nth-child(2),
        .content td:nth-child(2) {
            width: 15%;
        }

        .content th:nth-child(3),
        .content td:nth-child(3) {
            width: 45%;
        }

        .content th:nth-child(4),
        .content td:nth-child(4) {
            width: 15%;
        }

        .content th:nth-child(5),
        .content td:nth-child(5) {
            width: 15%;
        }

        hr {
            border: none;
            border-top: 2px solid #000;
            margin: 0;
            padding: 0;
        }

        .signature-section {
            margin-top: 20px;
            text-align: center;
        }

        .signature {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .signature p {
            margin: 5px;
            padding: 0;
            line-height: 1;
            text-align: center;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            text-align: center;
        }

        .signature+.signature {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="column">
            <div class="slip">
                <div class="header">
                    <img src="assets/images/nbsc-icon.png" alt="NBSC Logo" class="logo logo-left"
                        style="width: 50px; height: 50px;">
                    <div>
                        <p>Income Generation Office</p>
                        <p>Northern Bukidnon State College</p>
                        <p>Kihare, Manolo Fortich Bukidnon</p>
                        <p>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</p>
                        <h3>ORDER SLIP</h3>
                    </div>
                    <img src="assets/images/igp-icon.png" alt="IGP Logo" class="logo logo-right"
                        style="width: 50px; height: 60px;">
                </div>

                <div class="order-info">
                    <div class="left-column">
                        <p><strong>Order No:</strong> {{ $order->order_number }}</p>
                        <p><strong>Requested by:</strong> {{ $order->fullName }}</p>
                        <p><strong>Date:</strong> {{ $order->created_at->format('m/d/Y') }}</p>
                    </div>
                    <div class="right-column">
                        <p><strong>Official Receipt No:</strong> ___________</p>
                    </div>
                </div>

                <div class="content">
                    <table>
                        <thead>
                            <tr>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Item Description</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td>{{ $orderItems[$i]->quantity ?? '' }}</td>
                                    <td>{{ $orderItems[$i]->product->SKU ?? '' }}</td>
                                    <td>{{ $orderItems[$i]->product->name ?? '' }}</td>
                                    <td>{{ isset($orderItems[$i]) ? number_format($orderItems[$i]->price, 2) : '' }}
                                    </td>
                                    <td>{{ isset($orderItems[$i]) ? number_format($orderItems[$i]->price * $orderItems[$i]->quantity, 2) : '' }}
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                    <p style="text-align: right; font-size: 12px;"><strong>Grand Total:</strong>
                        {{ number_format($order->total, 2) }}</p>
                </div>

                <hr>

                <div class="signature-section">
                    <div class="signature authorized-signature">
                        <p class="signature-name">HISHAM P. DANLUYAN</p>
                        <p>Authorized Personnel Signature</p>
                    </div>
                    <div class="signature customer-signature">
                        <p style="text-transform: uppercase; text-decoration: underline; font-weight: bold;">
                            {{ $order->fullName }}</p>
                        <p>Customer Signature Over Printed Name</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="column">
            <div class="slip">
                <div class="header">
                    <img src="assets/images/nbsc-icon.png" alt="NBSC Logo" class="logo logo-left"
                        style="width: 50px; height: 50px;">
                    <div>
                        <p>Income Generation Office</p>
                        <p>Northern Bukidnon State College</p>
                        <p>Kihare, Manolo Fortich Bukidnon</p>
                        <p>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</p>
                        <h3>DELIVERY RECEIPT</h3>
                    </div>
                    <img src="assets/images/igp-icon.png" alt="IGP Logo" class="logo logo-right"
                        style="width: 50px; height: 60px;">
                </div>

                <div class="order-info">
                    <div class="left-column">
                        <p><strong>Order No:</strong> {{ $order->order_number }}</p>
                        <p><strong>Requested by:</strong> {{ $order->fullName }}</p>
                        <p><strong>Date:</strong> {{ $order->created_at->format('m/d/Y') }}</p>
                    </div>
                    <div class="right-column">
                        <p><strong>Official Receipt No:</strong> ___________</p>
                    </div>
                </div>

                <div class="content">
                    <table>
                        <thead>
                            <tr>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Item Description</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td>{{ $orderItems[$i]->quantity ?? '' }}</td>
                                    <td>{{ $orderItems[$i]->product->SKU ?? '' }}</td>
                                    <td>{{ $orderItems[$i]->product->name ?? '' }}</td>
                                    <td>{{ isset($orderItems[$i]) ? number_format($orderItems[$i]->price, 2) : '' }}
                                    </td>
                                    <td>{{ isset($orderItems[$i]) ? number_format($orderItems[$i]->price * $orderItems[$i]->quantity, 2) : '' }}
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                    <p style="text-align: right; font-size: 12px;"><strong>Grand Total:</strong>
                        {{ number_format($order->total, 2) }}</p>
                </div>

                <hr>

                <div class="signature-section">
                    <div class="signature authorized-signature">
                        <p class="signature-name">HISHAM P. DANLUYAN</p>
                        <p>Authorized Personnel Signature</p>
                    </div>
                    <div class="signature customer-signature">
                        <p style="text-transform: uppercase; text-decoration: underline; font-weight: bold;">
                            {{ $order->fullName }}</p>
                        <p>Customer Signature Over Printed Name</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
