<?php

namespace App\Actions\DocumentTemplates;

use App\Enums\Documents\DocumentTemplateType;
use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDefaultTemplate
{
    use AsAction;

    public function handle(DocumentTemplateType $templateType): string
    {
        return match ($templateType) {
            DocumentTemplateType::CARRIER_RATE_CONFIRMATION => $this->getCarrierRateConfirmationTemplate(),
            DocumentTemplateType::CUSTOMER_INVOICE => $this->getCustomerInvoiceTemplate(),
            DocumentTemplateType::BILL_OF_LADING => $this->getBillOfLadingTemplate(),
        };
    }

    public function asController(ActionRequest $request, string $templateType)
    {
        $type = DocumentTemplateType::from($templateType);
        return response()->json([
            'template' => $this->handle($type)
        ]);
    }

    private function getCarrierRateConfirmationTemplate(): string
    {
        $path = resource_path('views/documents/carrier-rate-confirmation.blade.php');
        
        if (File::exists($path)) {
            return File::get($path);
        }
        
        return $this->getDefaultCarrierRateConfirmationTemplate();
    }

    private function getCustomerInvoiceTemplate(): string
    {
        // For now, return a basic template
        return $this->getDefaultCustomerInvoiceTemplate();
    }

    private function getBillOfLadingTemplate(): string
    {
        // For now, return a basic template
        return $this->getDefaultBillOfLadingTemplate();
    }

    private function getDefaultCarrierRateConfirmationTemplate(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrier Rate Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #555; }
        .doc-title { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; margin-bottom: 5px; background-color: #f0f0f0; padding: 5px; border-bottom: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <div class="header">
        <div class="doc-title">CARRIER RATE CONFIRMATION</div>
    </div>
    
    <div class="section">
        <div class="section-title">BROKER INFORMATION</div>
        <p>{{ $broker_company ?? "Company Name" }}</p>
    </div>
    
    <div class="section">
        <div class="section-title">CARRIER INFORMATION</div>
        <p>{{ $carrier_name ?? "Carrier Name" }}</p>
    </div>
    
    <div class="section">
        <div class="section-title">RATE INFORMATION</div>
        <p>Total Rate: ${{ $total_rate ?? "0.00" }}</p>
    </div>
</body>
</html>';
    }

    private function getDefaultCustomerInvoiceTemplate(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Invoice</title>
</head>
<body>
    <h1>Customer Invoice</h1>
    <p>Invoice Number: {{ $invoice_number ?? "INV-00000" }}</p>
    <p>Customer: {{ $customer_name ?? "Customer Name" }}</p>
    <p>Total: ${{ $total_amount ?? "0.00" }}</p>
</body>
</html>';
    }

    private function getDefaultBillOfLadingTemplate(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bill of Lading</title>
</head>
<body>
    <h1>Bill of Lading</h1>
    <p>BOL Number: {{ $bol_number ?? "BOL-00000" }}</p>
    <p>Shipper: {{ $shipper_name ?? "Shipper Name" }}</p>
    <p>Consignee: {{ $consignee_name ?? "Consignee Name" }}</p>
</body>
</html>';
    }
} 