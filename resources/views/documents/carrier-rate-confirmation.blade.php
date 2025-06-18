<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrier Rate Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #555;
        }
        .logo-placeholder {
            height: 80px;
            margin-bottom: 10px;
            text-align: left;
        }
        .doc-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .doc-subtitle {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            background-color: #f0f0f0;
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .box {
            border: 1px solid #ddd;
            padding: 5px;
            margin-bottom: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: separate;
        }
        .info-table td {
            width: 50%;
            vertical-align: top;
            border: none;
        }
        .signature-area {
            border-top: 1px solid #ddd;
            padding-top: 15px;
            margin-top: 30px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin-top: 50px;
            display: inline-block;
        }
        .signature-field {
            margin-bottom: 30px;
        }
        .small-text {
            font-size: 10px;
        }
        .important {
            font-weight: bold;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($company_logo)
        <div class="logo-placeholder">
            <img src="{{ $company_logo ?? '' }}" alt="Company Logo" height="80">
        </div>
        @endif
        <div class="doc-title">CARRIER RATE CONFIRMATION</div>
        <div class="doc-subtitle">AGREEMENT BETWEEN BROKER AND CARRIER</div>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <div class="box">
                    <strong>Broker Information:</strong><br>
                    <strong>{{ $broker_company ?? 'COMPANY NAME' }}</strong><br>
                    {{ $broker_address ?? 'Address Line 1' }}<br>
                    {{ $broker_city ?? 'City' }}, {{ $broker_state ?? 'State' }} {{ $broker_zip ?? 'ZIP' }}<br>
                    MC#: {{ $broker_mc ?? '-' }}<br>
                    Phone: {{ $broker_phone ?? '-' }}<br>
                    Email: {{ $broker_email ?? '-' }}
                </div>
            </td>
            <td>
                <div class="box">
                    <strong>Rate Confirmation Details:</strong><br>
                    Rate Con #: <strong>{{ $load_number ?? 'PO-00000' }}</strong><br>
                    Date: {{ $date ?? date('m/d/Y') }}<br>
                    Shipment #: {{ $load_number ?? 'PO-00000' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">CARRIER INFORMATION</div>
        <table>
            <tr>
                <td width="50%"><strong>Carrier Name:</strong> {{ $carrier_name ?? '' }}</td>
                <td width="50%"><strong>Phone:</strong> {{ $carrier_phone ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Address:</strong> {{ $carrier_address ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>MC#:</strong> {{ $carrier_mc ?? '' }}</td>
                <td><strong>DOT#:</strong> {{ $carrier_dot ?? '' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">SHIPMENT DETAILS</div>
        @foreach($stops as $stop)
        <table>
            <tr>
                <th colspan="2">{{ $stop['type'] }} #{{ $stop['stop_number'] }}</th>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>{{ $stop['company'] ?? '' }}</strong><br>
                    {{ $stop['address'] ?? '' }}<br>
                    {{ $stop['city'] ?? '' }}, {{ $stop['state'] ?? '' }} {{ $stop['zip'] ?? '' }}<br>
                    Date: {{ $stop['date'] ?? '' }}<br>
                    Time: {{ $stop['time'] ?? '' }}
                </td>
            </tr>
            <tr>
                <td width="50%"><strong>Contact:</strong> {{ $stop['contact'] ?? '' }}</td>
                <td width="50%"><strong>Phone:</strong> {{ $stop['phone'] ?? '' }}</td>
            </tr>
            @if(isset($stop['special_instructions']) && !empty($stop['special_instructions']))
            <tr>
                <td colspan="2"><strong>Special Instructions:</strong> {{ $stop['special_instructions'] ?? '' }}</td>
            </tr>
            @endif
        </table>
        @endforeach
    </div>

    <div class="section">
        <div class="section-title">CARGO DETAILS</div>
        <table>
            <tr>
                <th>Weight (lbs)</th>
                <th>Trailer Type</th>
                <th>Temperature</th>
            </tr>
            <tr>
                <td>{{ $weight ?? '' }}</td>
                <td>{{ $trailer_type ?? '' }}</td>
                <td>{{ $temperature ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">RATE AND PAYMENT INFORMATION</div>
        <table>
            <tr>
                <th width="70%">Description</th>
                <th width="30%">Amount</th>
            </tr>
            @if(isset($payables) && count($payables) > 0)
                @foreach($payables as $payable)
                <tr>
                    <td>{{ $payable['description'] }}</td>
                    <td>${{ $payable['amount'] }}</td>
                </tr>
                @endforeach
            @endif
            <tr>
                <th>TOTAL RATE</th>
                <th>${{ $total_rate ?? '0.00' }}</th>
            </tr>
        </table>

        <div class="box small-text">
            <p><strong>Required Documentation:</strong> Invoice, signed BOL, signed Rate Confirmation, and any additional delivery receipts</p>
            <p><strong>Send Invoice To:</strong> {{ $invoice_email ?? 'accounting@company.com' }}</p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">TERMS AND CONDITIONS</div>
        <div class="small-text">
            <ol>
                <li>Carrier agrees to transport the above shipment in accordance with the terms of this Rate Confirmation and all applicable federal, state, and local laws and regulations.</li>
                <li>Carrier must notify Broker immediately of any delays, accidents, or other issues affecting the shipment.</li>
                <li>Carrier warrants that it possesses all required operating authority, permits, insurance coverage, and is authorized to transport the shipment described.</li>
                <li>Carrier must maintain insurance coverage at all times including: Auto Liability ($1,000,000 min), Cargo ($100,000 min), Workers Compensation (as required by law), and General Liability.</li>
                <li>Carrier is responsible for ensuring the safe loading, proper securement, and delivery of the cargo.</li>
                <li>Double brokering is strictly prohibited. Loads may not be re-brokered, co-brokered, subcontracted, or transferred to any other carrier without prior written consent.</li>
                <li>This shipment is subject to the Broker-Carrier Agreement between the parties. In the absence of such agreement, this Rate Confirmation and terms constitutes the entire agreement.</li>
                <li>Carrier must provide proof of delivery documents to receive payment. No payment will be made without required documentation.</li>
                <li>Carrier waives any right to lien on cargo transported under this Rate Confirmation.</li>
                <li>By signing below, Carrier acknowledges and agrees to all terms and conditions.</li>
            </ol>
        </div>
    </div>

    <div class="signature-area">
        <table class="info-table">
            <tr>
                <td>
                    <div class="signature-field">
                        <div><strong>BROKER AUTHORIZED SIGNATURE</strong></div>
                        <div class="signature-line"></div>
                        <div>Name: {{ $broker_name ?? '____________________' }}</div>
                        <div>Title: {{ $broker_title ?? '____________________' }}</div>
                        <div>Date: {{ $broker_date ?? '____________________' }}</div>
                    </div>
                </td>
                <td>
                    <div class="signature-field">
                        <div><strong>CARRIER AUTHORIZED SIGNATURE</strong></div>
                        <div class="signature-line"></div>
                        <div>Name: ____________________</div>
                        <div>Title: ____________________</div>
                        <div>Date: ____________________</div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="small-text important">
            THIS RATE CONFIRMATION IS NOT VALID UNLESS SIGNED BY BOTH PARTIES AND RETURNED TO BROKER
        </div>
    </div>
</body>
</html>
