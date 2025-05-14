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

        // TODO - company prefs fill in
        $data['company_name'] = "TODO";
        $data['company_address'] = "TODO";
        $data['company_city'] = "TODO";
        $data['company_state'] = "TODO";
        $data['company_zip'] = "TODO";
        $data['company_phone'] = "TODO";
        $data['company_email'] = "TODO";

        $data['shipment_number'] = $shipment->shipment_number;

        // TODO track invoice numbers
        $data['invoice_number'] = sprintf('%s-%s', substr($customer->name, 0, 3), $shipment->shipment_number);

        $data['customer_reference'] = '';// TODO

        $data['payment_terms'] = ''; //TODO - $customer->payment_terms;

        // TODO - Customer billing address
        $data['customer_name'] = $customer->name;
        $data['customer_address'] = 'customer address1';
        $data['customer_city'] = 'city';
        $data['customer_state'] = 'state';
        $data['customer_zip'] = '12345';

        // TODO set customer billing contact
        $data['customer_contact'] = 'contact name';
        $data['customer_email'] = 'email@test.com';

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