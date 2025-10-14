<?php

namespace App\Traits;

use App\Models\Contact;
use App\Models\Documents\Document;
use App\Models\Shipments\ShipmentStop;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;

trait HandlesAuditHistory
{
    protected function getAuditHistory(Model & Auditable $parentModel): \Illuminate\Support\Collection
    {
        $parentClass = get_class($parentModel);

        // Get parent model audits
        $parentAudits = $parentModel->audits()->with('user')->get();

        // Get document audits for documents belonging to this parent model
        $documentAudits = $this->getRelatedModelAudits(
            Document::class,
            $parentClass,
            $parentModel->getKey(),
            'documentable_type',
            'documentable_id'
        );

        // Get contact audits for contacts belonging to this parent model
        $contactAudits = $this->getRelatedModelAudits(
            Contact::class,
            $parentClass,
            $parentModel->getKey(),
            'contact_for_type',
            'contact_for_id'
        );

        // Get shipment stop audits if this is a shipment
        $stopAudits = collect();
        if ($parentClass === \App\Models\Shipments\Shipment::class) {
            $stopAudits = $this->getShipmentStopAudits(
                $parentModel->getKey()
            );
        }

        // Merge all audits and sort by created_at desc
        return $parentAudits->merge($documentAudits)->merge($contactAudits)->merge($stopAudits)
            ->sortByDesc('created_at')
            ->values();
    }

    private function getRelatedModelAudits(
        string $auditableType,
        string $parentClass,
        mixed $parentId,
        string $typeField,
        string $idField
    ): \Illuminate\Support\Collection {
        $audits = collect();
        
        // Get audits for models that currently exist and belong to this parent
        // We need to join with the actual model table to filter by the polymorphic relationship
        $modelTable = (new $auditableType)->getTable();
        $existingModelAudits = Audit::where('auditable_type', $auditableType)
            ->join($modelTable, function ($join) use ($modelTable) {
                $join->on('audits.auditable_id', '=', $modelTable . '.id');
            })
            ->where($modelTable . '.' . $typeField, $parentClass)
            ->where($modelTable . '.' . $idField, $parentId)
            ->select('audits.*')
            ->with('user', 'auditable')
            ->get();
        
        $audits = $audits->merge($existingModelAudits);
        
        // Get audits for deleted models - but be very precise to avoid cross-contamination
        $deletedModelAudits = Audit::where('auditable_type', $auditableType)
            ->whereDoesntHave('auditable')
            ->get()
            ->filter(function (Audit $audit) use ($parentClass, $parentId, $typeField, $idField) {
                $oldValues = $audit->getAttributeValue('old_values') ?? [];
                $newValues = $audit->getAttributeValue('new_values') ?? [];
                
                // Check if this audit belongs to our specific parent
                $matchesInOld = isset($oldValues[$typeField]) && isset($oldValues[$idField]) &&
                               $oldValues[$typeField] === $parentClass && 
                               (int)$oldValues[$idField] === (int)$parentId;
                
                $matchesInNew = isset($newValues[$typeField]) && isset($newValues[$idField]) &&
                               $newValues[$typeField] === $parentClass && 
                               (int)$newValues[$idField] === (int)$parentId;
                
                return $matchesInOld || $matchesInNew;
            });
        
        return $audits->merge($deletedModelAudits);
    }

    private function getShipmentStopAudits(mixed $shipmentId): \Illuminate\Support\Collection
    {
        $audits = collect();

        // Get audits for stops that currently exist and belong to this shipment
        $existingStopAudits = Audit::where('auditable_type', ShipmentStop::class)
            ->join('shipment_stops', function ($join) {
                $join->on('audits.auditable_id', '=', 'shipment_stops.id');
            })
            ->where('shipment_stops.shipment_id', $shipmentId)
            ->select('audits.*')
            ->with('user', 'auditable')
            ->get();

        $audits = $audits->merge($existingStopAudits);

        // Get audits for deleted stops
        $deletedStopAudits = Audit::where('auditable_type', ShipmentStop::class)
            ->whereDoesntHave('auditable')
            ->get()
            ->filter(function (Audit $audit) use ($shipmentId) {
                $oldValues = $audit->getAttributeValue('old_values') ?? [];
                $newValues = $audit->getAttributeValue('new_values') ?? [];

                // Check if this audit belongs to our specific shipment
                $matchesInOld = isset($oldValues['shipment_id']) &&
                               (int)$oldValues['shipment_id'] === (int)$shipmentId;

                $matchesInNew = isset($newValues['shipment_id']) &&
                               (int)$newValues['shipment_id'] === (int)$shipmentId;

                return $matchesInOld || $matchesInNew;
            });

        return $audits->merge($deletedStopAudits);
    }

    protected function formatAuditData(\Illuminate\Support\Collection $audits): \Illuminate\Support\Collection
    {
        return $audits->map(function (Audit $audit) {
            /** @var mixed $auditableType */
            $auditableType = $audit->getAttributeValue('auditable_type');
            /** @var mixed $oldValues */
            $oldValues = $audit->getAttributeValue('old_values') ?? [];
            /** @var mixed $newValues */
            $newValues = $audit->getAttributeValue('new_values') ?? [];

            return [
                'id' => $audit->getKey(),
                'event' => $audit->getAttributeValue('event'),
                'auditable_type' => $auditableType,
                'auditable_id' => $audit->getAttributeValue('auditable_id'),
                'entity_type' => $this->getEntityType($auditableType),
                'entity_name' => $this->getEntityName($audit),
                'user' => $audit->user ? [
                    'id' => $audit->user->getKey(),
                    'name' => $audit->user->getAttributeValue('name'),
                    'email' => $audit->user->getAttributeValue('email'),
                ] : null,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'url' => $audit->getAttributeValue('url'),
                'ip_address' => $audit->getAttributeValue('ip_address'),
                'user_agent' => $audit->getAttributeValue('user_agent'),
                'created_at' => $audit->getAttributeValue('created_at'),
                'created_at_human' => $audit->getAttributeValue('created_at')?->diffForHumans(),
                'changes' => $this->formatChanges($oldValues, $newValues, $auditableType),
            ];
        })->filter(function (array $auditData) {
            $event = $auditData['event'];

            // Filter out shipment.updated and shipment.state_changed events
            // The standard "updated" event already shows this information
            if ($event === 'shipment.updated' || $event === 'shipment.state_changed') {
                return false;
            }

            // Filter out audits that have no displayable changes after system field filtering
            // Keep audits for created/deleted events even if they have no changes
            if (str_contains($event, 'created') || str_contains($event, 'deleted') || str_contains($event, 'restored')) {
                return true;
            }

            // For update events, only keep if there are actual changes to display
            return count($auditData['changes']) > 0;
        })->values();
    }

    private function formatChanges(array $oldValues, array $newValues, string $auditableType): array
    {
        $changes = [];
        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        // Filter out system fields we don't want to show in the UI
        $systemFields = [
            'id',
            'organization_id',
            'contact_for_id',
            'contact_for_type',
            'documentable_id',
            'documentable_type',
            'auditable_id',
            'auditable_type',
            'entity_id',
            'entity_type',
            'shipment_id',
            'shipment_number',
            'event_id',
            'occurred_at',
            'metadata',
            'changed_attributes',
            'previous_attributes',
            'current_state',
            'created_at',
            'updated_at'
        ];

        foreach ($allKeys as $key) {
            // Skip system fields
            if (in_array($key, $systemFields)) {
                continue;
            }

            $oldValue = $oldValues[$key] ?? null;
            $newValue = $newValues[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[] = [
                    'field' => $this->formatFieldName($key, $auditableType),
                    'field_name' => $key,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ];
            }
        }

        return $changes;
    }

    private function formatFieldName(string $field, string $auditableType): string
    {
        // Get the specific field mappings for the parent model
        $specificMappings = $this->getModelSpecificFieldMappings($auditableType);
        
        if (isset($specificMappings[$field])) {
            return $specificMappings[$field];
        }

        // Handle document-specific fields
        if ($auditableType === Document::class) {
            return match ($field) {
                'uploaded_by' => 'Uploaded By',
                'folder_name' => 'Folder Name',
                default => ucwords(str_replace('_', ' ', $field)),
            };
        }

        // Handle contact-specific fields
        if ($auditableType === Contact::class) {
            return match ($field) {
                'contact_type' => 'Contact Type',
                'mobile_phone' => 'Mobile Phone',
                'office_phone' => 'Office Phone',
                'office_phone_extension' => 'Office Phone Extension',
                default => ucwords(str_replace('_', ' ', $field)),
            };
        }

        return ucwords(str_replace('_', ' ', $field));
    }

    private function getEntityType(string $auditableType): string
    {
        return match ($auditableType) {
            \App\Models\Customers\Customer::class => 'Customer',
            \App\Models\Facility::class => 'Facility',
            \App\Models\Carriers\Carrier::class => 'Carrier',
            \App\Models\Shipments\Shipment::class => 'Shipment',
            ShipmentStop::class => 'Stop',
            Document::class => 'Document',
            Contact::class => 'Contact',
            default => class_basename($auditableType),
        };
    }

    private function getEntityName(Audit $audit): string
    {
        $auditable = $audit->auditable;
        /** @var mixed $auditableType */
        $auditableType = $audit->getAttributeValue('auditable_type');
        /** @var mixed $oldValues */
        $oldValues = $audit->getAttributeValue('old_values') ?? [];
        /** @var mixed $newValues */
        $newValues = $audit->getAttributeValue('new_values') ?? [];
        
        if (!$auditable) {
            // For deleted entities, try to get name from old_values or new_values
            $name = $oldValues['name'] ?? $newValues['name'] ?? null;
            $shipmentNumber = $oldValues['shipment_number'] ?? $newValues['shipment_number'] ?? null;
            
            $stopNumber = $oldValues['stop_number'] ?? $newValues['stop_number'] ?? null;
            $stopType = $oldValues['stop_type'] ?? $newValues['stop_type'] ?? null;

            return match ($auditableType) {
                \App\Models\Customers\Customer::class => $name ? $name . ' (deleted)' : 'Deleted Customer',
                \App\Models\Facility::class => $name ? $name . ' (deleted)' : 'Deleted Facility',
                \App\Models\Carriers\Carrier::class => $name ? $name . ' (deleted)' : 'Deleted Carrier',
                \App\Models\Shipments\Shipment::class => $shipmentNumber ? 'Shipment ' . $shipmentNumber . ' (deleted)' : 'Deleted Shipment',
                ShipmentStop::class => $stopNumber ? 'Stop #' . $stopNumber . ' (deleted)' : 'Deleted Stop',
                Document::class => $name ? $name . ' (deleted)' : 'Deleted Document',
                Contact::class => $name ? $name . ' (deleted)' : 'Deleted Contact',
                default => 'Deleted Entity',
            };
        }

        return match ($auditableType) {
            \App\Models\Customers\Customer::class => $auditable->getAttributeValue('name') ?? 'Unknown Customer',
            \App\Models\Facility::class => $auditable->getAttributeValue('name') ?? 'Unknown Facility',
            \App\Models\Carriers\Carrier::class => $auditable->getAttributeValue('name') ?? 'Unknown Carrier',
            \App\Models\Shipments\Shipment::class => $auditable->getAttributeValue('shipment_number') ? 'Shipment ' . $auditable->getAttributeValue('shipment_number') : 'Shipment #' . $auditable->getKey(),
            ShipmentStop::class => 'Stop #' . $auditable->getAttributeValue('stop_number'),
            Document::class => $auditable->getAttributeValue('name') ?? 'Unknown Document',
            Contact::class => $auditable->getAttributeValue('name') ?? 'Unknown Contact',
            default => 'Unknown Entity',
        };
    }

    // Abstract method to be implemented by each audit action class
    abstract protected function getModelSpecificFieldMappings(string $auditableType): array;
}