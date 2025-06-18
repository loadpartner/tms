<?php

namespace App\Actions\Documents\Generators;

use App\Actions\Documents\CreateDocument;
use App\Enums\Documents\Documentable;
use App\Enums\Documents\DocumentFolder;
use App\Enums\Documents\DocumentTemplateType;
use App\Enums\StopType;
use App\Models\Documents\Document;
use App\Models\Shipments\Shipment;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateRateConfirmation
{
    use AsAction;

    public function handle(
        Shipment $shipment
    )
    {
        // Prepare data for the view
        $data = $this->prepareViewData($shipment);
        
        // Get the organization from the shipment
        $organization = $shipment->organization;
        
        // Check if organization has a custom template
        $customTemplate = $organization->documentTemplates()
            ->where('template_type', DocumentTemplateType::CARRIER_RATE_CONFIRMATION)
            ->first();
        
        if ($customTemplate) {
            // Use custom template with error handling
            try {
                $html = Blade::render($customTemplate->template, $data);
            } catch (Exception $e) {
                // Log the error and fall back to default template
                Log::error('Failed to render custom template for organization ' . $organization->id, [
                    'error' => $e->getMessage(),
                    'template_id' => $customTemplate->id
                ]);
                
                // Fall back to default template
                $html = View::make('documents.carrier-rate-confirmation', $data)->render();
            }
        } else {
            // Use default template
            $html = View::make('documents.carrier-rate-confirmation', $data)->render();
        }
        
        // Ensure directory exists
        $directory = 'rate-confirmations';
        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }
        
        // Generate PDF from HTML
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        
        // Save PDF file to local storage
        $fileName = $directory . '/carrier-rc-' . $shipment->id . '-' . time() . '.pdf';
        Storage::disk('local')->put($fileName, $pdf->output());

        // Create a File object from the saved PDF
        $filePath = Storage::disk('local')->path($fileName);
        $file = new File($filePath);

        $document = CreateDocument::run(
            Documentable::SHIPMENT->value,
            $shipment->id,
            "RateConfirmation.pdf",
            $file,
            DocumentFolder::RATECONS->value
        );

        // Delete the temp doc
        Storage::disk('local')->delete($fileName);

        return $document;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        return $this->handle($shipment);   
    }

    public function htmlResponse(Document $document)
    {
        return redirect()->back();
    }
    
    private function prepareViewData(Shipment $shipment): array
    {
        // Extract broker info (from organization config)
        $broker = [
            'broker_company' => config('company.name'),
            'broker_address' => config('company.address'),
            'broker_city' => config('company.city'),
            'broker_state' => config('company.state'),
            'broker_zip' => config('company.zip'),
            'broker_mc' => config('company.mc_number'),
            'broker_phone' => config('company.phone'),
            'broker_email' => config('company.email'),
            'company_logo' => config('company.logo_url'),

        ];
        
        // Extract rate confirmation details
        $rateConfirmation = [
            'date' => now()->format('m/d/Y'),
            'load_number' => $shipment->shipment_number,
        ];
        
        // Extract carrier information
        $carrier = [
            'carrier_name' => $shipment->carrier->name ?? '',
            'carrier_address' => $shipment->carrier->address ?? '',
            'carrier_phone' => $shipment->carrier->phone ?? '',
            'carrier_mc' => $shipment->carrier->mc_number ?? '',
            'carrier_dot' => $shipment->carrier->dot_number ?? '',
        ];
        
        // Process all stops
        $stops = $this->processStops($shipment);
        
        // Extract cargo details
        $cargo = [
            'weight' => $shipment->weight ?? '',
            'temperature' => $shipment->trailer_temperature ?? 'N/A',
            'trailer_type' => $shipment->trailer_type->name ?? '',
        ];
        
        // Process payables
        $payables = $this->processPayables($shipment);
        $totalRate = number_format($shipment->payables->sum('amount') ?? 0, 2);
        
        $payment = [
            'total_rate' => $totalRate,
            'invoice_email' => config('company.accounting_email'),
        ];
        
        return array_merge(
            $broker, 
            $rateConfirmation, 
            $carrier, 
            $cargo, 
            $payment, 
            ['stops' => $stops],
            ['payables' => $payables]
        );
    }

    private function processStops(Shipment $shipment): array
    {
        $stops = [];
        // Get all stops sorted by stop_number
        $shipmentStops = $shipment->stops->sortBy('stop_number');
        
        foreach ($shipmentStops as $stop) {
            $facility = $stop->facility;
            $location = $facility->location;
            $appointmentAt = $stop->appointment_at ? \Carbon\Carbon::parse($stop->appointment_at) : null;
            
            $stopType = '';
            if ($stop->stop_type == StopType::Pickup) {
                $stopType = 'PICKUP';
            } elseif ($stop->stop_type == StopType::Delivery) {
                $stopType = 'DELIVERY';
            }
            
            $stops[] = [
                'stop_number' => $stop->stop_number,
                'type' => $stopType,
                'company' => $facility->name ?? '',
                'address' => $location->address_line_1 . ($location->address_line_2 ? ', ' . $location->address_line_2 : ''),
                'city' => $location->address_city ?? '',
                'state' => $location->state_shorthand ?? '',
                'zip' => $location->address_zipcode ?? '',
                'date' => $appointmentAt?->format('m/d/Y') ?? '',
                'time' => $appointmentAt?->format('h:i A') ?? '',
                'contact' => $facility->contacts->first()->name ?? '',
                'phone' => $facility->contacts->first()->phone ?? '',
                'special_instructions' => $stop->special_instructions ?? '',
            ];
        }
        
        return $stops;
    }
    
    private function processPayables(Shipment $shipment): array
    {
        $payables = [];
        
        foreach ($shipment->payables as $payable) {
            $payables[] = [
                'description' => ucfirst(str_replace('_', ' ', $payable->rate_type->name ?? $payable->type ?? 'Charge')),
                'amount' => number_format($payable->amount ?? $payable->total ?? 0, 2)
            ];
        }
        
        return $payables;
    }
}