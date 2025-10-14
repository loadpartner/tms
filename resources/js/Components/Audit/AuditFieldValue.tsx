import { Button } from '@/Components/ui/button';
import { ExternalLink } from 'lucide-react';
import { useState } from 'react';
import AuditLinkedDataModal from './AuditLinkedDataModal';

interface AuditFieldValueProps {
    fieldName: string;
    value: unknown;
    className?: string;
    auditUser?: {
        id: number;
        name: string;
        email: string;
    } | null;
}

// Map of field names to their corresponding model types
const FOREIGN_KEY_MAPPINGS: Record<string, string> = {
    billing_location_id: 'location',
    physical_location_id: 'location',
    location_id: 'location',
    billing_contact_id: 'contact',
    contact_id: 'contact',
    user_id: 'user',
    created_by: 'user',
    updated_by: 'user',
    carrier_id: 'carrier',
    customer_id: 'customer',
    facility_id: 'facility',
};

export default function AuditFieldValue({
    fieldName,
    value,
    className = '',
    auditUser,
}: AuditFieldValueProps) {
    const [modalOpen, setModalOpen] = useState(false);

    const formatValue = (value: unknown): string => {
        // Check for folder-related fields (both raw and formatted names)
        const isFolderField =
            fieldName === 'folder_name' || fieldName === 'Folder Name';
        const isPathField = fieldName === 'path' || fieldName === 'Path';

        if (value === null || value === undefined) {
            // For path fields, only show '/' if the value was explicitly set to null/undefined
            // but not when there was no previous value (which should show 'No value')
            return 'No value';
        }
        if (typeof value === 'boolean') {
            // Special handling for folder fields - don't convert to Yes/No
            if (isFolderField) {
                return value ? 'true' : '/';
            }
            // Special handling for path fields - show root folder when false
            if (isPathField) {
                return value ? 'true' : '/';
            }
            return value ? 'Yes' : 'No';
        }
        if (typeof value === 'object') {
            return JSON.stringify(value);
        }

        const stringValue = String(value);

        // Special handling for folder fields - show root folder when empty
        if (isFolderField && stringValue.trim() === '') {
            return '/';
        }

        // Special handling for file paths
        if (fieldName === 'path' && stringValue) {
            // Extract just the filename from the path
            const parts = stringValue.split('/');
            return parts[parts.length - 1] || stringValue;
        }

        // Special handling for uploaded_by - show the user name if it matches the audit user
        if (
            fieldName === 'uploaded_by' &&
            auditUser &&
            String(value) === String(auditUser.id)
        ) {
            return auditUser.name;
        }

        return stringValue;
    };

    // Check if this field is a foreign key
    const modelType = FOREIGN_KEY_MAPPINGS[fieldName.toLowerCase()];
    const isForeignKey = modelType && value && String(value).match(/^\d+$/);

    if (isForeignKey) {
        return (
            <>
                <div className={`flex items-center gap-2 ${className}`}>
                    <span>{formatValue(value)}</span>
                    <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => setModalOpen(true)}
                        className="h-6 w-6 p-0 text-blue-600 hover:text-blue-800"
                        title={`View ${modelType} details`}
                    >
                        <ExternalLink className="h-3 w-3" />
                    </Button>
                </div>

                <AuditLinkedDataModal
                    open={modalOpen}
                    onOpenChange={setModalOpen}
                    modelType={modelType}
                    modelId={String(value)}
                />
            </>
        );
    }

    return <span className={className}>{formatValue(value)}</span>;
}
