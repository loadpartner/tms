<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .company-header {
            margin-bottom: 20px;
        }
        .invoice-title {
            color: #4a86e8;
            font-size: 28px;
            font-weight: bold;
            text-align: right;
        }
        .invoice-info {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-info td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        .load-info {
            margin-top: 5px;
            font-size: 11px;
        }
        .bill-to {
            margin-top: 20px;
            margin-bottom: 20px;
            background-color: #f0f0f0;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table th {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        .items-table td {
            border: 1px solid #ccc;
            padding: 8px;
            vertical-align: top;
        }
        .amount-column {
            text-align: right;
            width: 20%;
        }
        .total-row {
            font-weight: bold;
        }
        .thank-you {
            text-align: center;
            margin-top: 10px;
            padding: 10px;
            border-top: 1px solid #ccc;
        }
        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
        table {
            width: 100%;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td style="width: 60%;">
                <div class="company-header">
                    <div>{{ $company_name ?? '[Company Name]' }}</div>
                    <div>{{ $company_address ?? '[Street Address]' }}</div>
                    <div>{{ $company_city ?? '[City]' }}, {{ $company_state ?? 'ST' }} {{ $company_zip ?? 'ZIP' }}</div>
                    <div>Phone: {{ $company_phone ?? '(000) 000-0000' }}</div>
                </div>
            </td>
            <td style="width: 40%;">
                <div class="invoice-title">INVOICE</div>
            </td>
        </tr>
    </table>

    <table class="invoice-info">
        <tr>
            <td style="width: 50%;">INVOICE #</td>
            <td style="width: 50%;">DATE</td>
        </tr>
        <tr>
            <td>{{ $shipment_number }}-{{ strstr($invoice_number, '-', true) }}</td>
            <td>{{ $invoice_date ?? '' }}</td>
        </tr>
    </table>

    <div class="load-info">
        <strong>Load #:</strong> {{ $shipment_number ?? '' }} 
        @if(!empty($customer_reference))
        <strong>Reference:</strong> {{ $customer_reference ?? '' }}
        @endif
        <strong>Payment Terms:</strong> {{ $payment_terms ?? 'Net 30 Days' }}
    </div>

    <div class="bill-to">
        <div><strong>BILL TO</strong></div>
        <div>{{ $customer_name ?? '[Name]' }}</div>
        <div>{{ $customer_address ?? '[Street Address]' }}</div>
        <div>{{ $customer_city ?? '[City]' }}, {{ $customer_state ?? 'ST' }} {{ $customer_zip ?? 'ZIP' }}</div>
        <div>Contact: {{ $customer_contact ?? '' }}</div>
        <div>Email: {{ $customer_email ?? '[Email Address]' }}</div>
    </div>

    <table class="items-table">
        <tr>
            <th>DESCRIPTION</th>
            <th>AMOUNT</th>
        </tr>
        @if(isset($receivables) && count($receivables) > 0)
            @foreach($receivables as $receivable)
            <tr>
                <td>{{ $receivable['description'] }}</td>
                <td class="amount-column">{{ $receivable['amount'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" height="100"></td>
            </tr>
            <tr>
                <td class="thank-you">Thank you for your business!</td>
                <td class="amount-column total-row">TOTAL</td>
            </tr>
            <tr>
                <td></td>
                <td class="amount-column total-row">${{ $total_due }}</td>
            </tr>
        @else
            <tr>
                <td colspan="2" class="text-center">No charges found</td>
            </tr>
        @endif
    </table>

    <div class="footer-text">
        If you have any questions about this invoice, please contact
        <br>{{ $company_name ?? '' }}, {{ $company_phone ?? '' }}, {{ $company_email ?? '' }}
    </div>
</body>
</html>
