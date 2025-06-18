<?php

namespace App\Actions\Documents\Generators;

use App\Actions\Documents\CreateDocument;
use App\Enums\Documents\Documentable;
use App\Enums\Documents\DocumentFolder;
use App\Enums\StopType;
use App\Models\Customers\Customer;
use App\Models\Documents\Document;
use App\Models\Shipments\Shipment;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateCustomerInvoice
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        Customer $customer
    )
    {
        // Load the customer's billing relationships if not already loaded
        $customer->loadMissing(['billingLocation', 'billingContact']);
        
        // Prepare data for the view
        $data = $this->prepareViewData($shipment, $customer);
        
        // Generate HTML from the view
        $html = View::make('documents.customer-invoice', $data)->render();
        
        // Ensure directory exists
        $directory = 'customer-invoices';
        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }
        
        // Generate PDF from HTML
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        
        // Save PDF file to local storage
        $fileName = $directory . '/customer-invoice-' . $shipment->id . '-' . $customer->id . '-' . time() . '.pdf';
        Storage::disk('local')->put($fileName, $pdf->output());

        // Create a File object from the saved PDF
        $filePath = Storage::disk('local')->path($fileName);
        $file = new File($filePath);

        $document = CreateDocument::run(
            Documentable::SHIPMENT->value,
            $shipment->id,
            "CustomerInvoice-{$shipment->shipment_number}-{$customer->name}.pdf",
            $file,
            DocumentFolder::CUSTOMER_INVOICE->value
        );

        // Delete the temp doc
        Storage::disk('local')->delete($fileName);

        return $document;
    }

    public function asController(ActionRequest $request, Shipment $shipment, Customer $customer)
    {
        return $this->handle($shipment, $customer);   
    }

    public function htmlResponse(Document $document)
    {
        return redirect()->back();
    }
    
    private function prepareViewData(Shipment $shipment, Customer $customer): array
    {
        $data = [];

        // Get current organization
        $organization = current_organization();
        
        // Load the company location relationship if not already loaded
        $organization->loadMissing('companyLocation');

        // Company information from organization
        $data['company_name'] = $organization->company_name ?? '';
        
        // Company location information from companyLocation relationship
        if ($organization->companyLocation) {
            $data['company_address'] = $organization->companyLocation->address_line_1 ?? '';
            $data['company_city'] = $organization->companyLocation->address_city ?? '';
            $data['company_state'] = $organization->companyLocation->address_state ?? '';
            $data['company_zip'] = $organization->companyLocation->address_zipcode ?? '';
        } else {
            $data['company_address'] = '';
            $data['company_city'] = '';
            $data['company_state'] = '';
            $data['company_zip'] = '';
        }
        
        $data['company_phone'] = $organization->accounting_contact_phone ?? $organization->company_phone ?? '';
        $data['company_email'] = $organization->company_email ?? $organization->accounting_contact_email ?? '';
        
        $data['shipment_number'] = $shipment->shipment_number;

        // Use customer's invoice number schema if available, otherwise use default format
        $data['invoice_number'] = $customer->invoice_number_schema 
            ? sprintf($customer->invoice_number_schema, $shipment->shipment_number)
            : sprintf('%s-%s', substr($customer->name, 0, 3), $shipment->shipment_number);

        $data['customer_reference'] = ''; // TODO: Add customer reference field if needed

        // Payment terms from customer's net_pay_days
        $data['payment_terms'] = $customer->net_pay_days 
            ? "Net {$customer->net_pay_days} Days" 
            : 'Net 30 Days';

        // Customer billing address from billing location relationship
        $data['customer_name'] = $customer->dba_name ?? $customer->name;
        
        if ($customer->billingLocation) {
            $data['customer_address'] = $customer->billingLocation->address_line_1 ?? '';
            $data['customer_address_line_2'] = $customer->billingLocation->address_line_2 ?? '';
            $data['customer_city'] = $customer->billingLocation->address_city ?? '';
            $data['customer_state'] = $customer->billingLocation->address_state ?? '';
            $data['customer_zip'] = $customer->billingLocation->address_zipcode ?? '';
        } else {
            $data['customer_address'] = 'No billing address on file';
            $data['customer_address_line_2'] = '';
            $data['customer_city'] = '';
            $data['customer_state'] = '';
            $data['customer_zip'] = '';
        }

        // Customer billing contact
        if ($customer->billingContact) {
            $data['customer_contact'] = $customer->billingContact->name ?? '';
            $data['customer_email'] = $customer->billingContact->email ?? '';
            $data['customer_phone'] = $customer->billingContact->office_phone ?? $customer->billingContact->mobile_phone ?? '';
        } else {
            $data['customer_contact'] = 'No billing contact on file';
            $data['customer_email'] = '';
            $data['customer_phone'] = '';
        }

        $receivables = $shipment->receivables()->whereMorphedTo('payer', $customer)->get();
        $data['receivables'] = $receivables->map(function($receivable) {
            return [
                'description' => $receivable->rate_type->name ?? 'Freight Charge',
                'amount' => '$' . number_format($receivable->total, 2),
                'rate' => $receivable->rate,
                'quantity' => $receivable->quantity
            ];
        });
        $data['total_due'] = number_format($receivables->sum('total'), 2);

        return $data;
    }
}