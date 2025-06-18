import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';
import { Organization } from '@/types/organization';
import { useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';

interface DocumentTemplate {
    id: number;
    template_type: string;
    template: string;
    created_at: string;
    updated_at: string;
}

const TEMPLATE_TYPES = [
    {
        value: 'carrier_rate_confirmation',
        label: 'Carrier Rate Confirmation',
        available: true,
    },
    { value: 'customer_invoice', label: 'Customer Invoice', available: false },
    { value: 'bill_of_lading', label: 'Bill of Lading', available: false },
];

export default function DocumentTemplatesForm({
    organization,
}: {
    organization: Organization;
}) {
    const [selectedTemplateType, setSelectedTemplateType] =
        useState<string>('');
    const [templates, setTemplates] = useState<DocumentTemplate[]>([]);
    const [loadingDefault, setLoadingDefault] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        template_type: '',
        template: '',
    });

    // Load existing templates
    useEffect(() => {
        fetch(route('organizations.document-templates.index', organization.id))
            .then((response) => response.json())
            .then((data) => {
                setTemplates(data);
            })
            .catch((error) => {
                console.error('Error loading templates:', error);
            });
    }, [organization.id]);

    // Load template when type is selected
    useEffect(() => {
        if (selectedTemplateType) {
            setData('template_type', selectedTemplateType);

            // Check if this template type is available
            const templateType = TEMPLATE_TYPES.find(
                (t) => t.value === selectedTemplateType,
            );
            if (!templateType?.available) {
                setData('template', '');
                return;
            }

            // Check if we have an existing template for this type
            const existingTemplate = templates.find(
                (t) => t.template_type === selectedTemplateType,
            );

            if (existingTemplate) {
                // Load existing template
                fetch(
                    route('organizations.document-templates.show', [
                        organization.id,
                        existingTemplate.id,
                    ]),
                )
                    .then((response) => response.json())
                    .then((data) => {
                        setData('template', data.template);
                    })
                    .catch((error) => {
                        console.error('Error loading template:', error);
                    });
            } else {
                // Load default template
                setLoadingDefault(true);
                fetch(route('document-templates.default', selectedTemplateType))
                    .then((response) => response.json())
                    .then((data) => {
                        setData('template', data.template);
                    })
                    .catch((error) => {
                        console.error('Error loading default template:', error);
                    })
                    .finally(() => {
                        setLoadingDefault(false);
                    });
            }
        }
    }, [
        selectedTemplateType,
        templates,
        organization.id,
        setLoadingDefault,
        setData,
    ]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('organizations.document-templates.store', organization.id), {
            onSuccess: () => {
                // Reload templates list
                fetch(
                    route(
                        'organizations.document-templates.index',
                        organization.id,
                    ),
                )
                    .then((response) => response.json())
                    .then((data) => {
                        setTemplates(data);
                    });
            },
        });
    };

    const loadDefaultTemplate = () => {
        if (selectedTemplateType) {
            setLoadingDefault(true);
            fetch(route('document-templates.default', selectedTemplateType))
                .then((response) => response.json())
                .then((data) => {
                    setData('template', data.template);
                })
                .catch((error) => {
                    console.error('Error loading default template:', error);
                })
                .finally(() => {
                    setLoadingDefault(false);
                });
        }
    };

    return (
        <div className="space-y-6">
            {/* Template Type Selection */}
            <Card>
                <CardHeader>
                    <CardTitle>Document Template Editor</CardTitle>
                    <CardDescription>
                        Create and customize document templates for your
                        organization. Select a template type to begin editing.
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                    <div>
                        <Label htmlFor="template_type">Template Type</Label>
                        <Select
                            value={selectedTemplateType}
                            onValueChange={setSelectedTemplateType}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select a template type" />
                            </SelectTrigger>
                            <SelectContent>
                                {TEMPLATE_TYPES.map((type) => (
                                    <SelectItem
                                        key={type.value}
                                        value={type.value}
                                        disabled={!type.available}
                                        className={
                                            !type.available
                                                ? 'cursor-not-allowed opacity-50'
                                                : ''
                                        }
                                    >
                                        <div className="flex w-full items-center justify-between">
                                            <span>{type.label}</span>
                                            <div className="flex items-center space-x-2">
                                                {templates.find(
                                                    (t) =>
                                                        t.template_type ===
                                                        type.value,
                                                ) &&
                                                    type.available && (
                                                        <span className="text-xs text-green-600">
                                                            (Custom)
                                                        </span>
                                                    )}
                                                {!type.available && (
                                                    <span className="text-xs text-muted-foreground">
                                                        (Coming Soon)
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {errors.template_type && (
                            <InputError message={errors.template_type} />
                        )}
                    </div>
                </CardContent>
            </Card>

            {/* Template Editor */}
            {selectedTemplateType && (
                <Card>
                    <CardHeader>
                        <CardTitle>
                            {
                                TEMPLATE_TYPES.find(
                                    (t) => t.value === selectedTemplateType,
                                )?.label
                            }{' '}
                            Template
                        </CardTitle>
                        <CardDescription>
                            {TEMPLATE_TYPES.find(
                                (t) => t.value === selectedTemplateType,
                            )?.available ? (
                                <>
                                    Edit the template content below. You can use
                                    Blade template syntax for dynamic content
                                    (e.g., {`{{ $variable_name }}`}).
                                </>
                            ) : (
                                'This template type is coming soon and is not yet available for editing.'
                            )}
                        </CardDescription>
                    </CardHeader>
                    {TEMPLATE_TYPES.find(
                        (t) => t.value === selectedTemplateType,
                    )?.available ? (
                        <CardContent className="space-y-4">
                            <form onSubmit={submit} className="space-y-4">
                                <div>
                                    <div className="mb-2 flex items-center justify-between">
                                        <Label htmlFor="template">
                                            Template Content
                                        </Label>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            onClick={loadDefaultTemplate}
                                            disabled={loadingDefault}
                                        >
                                            {loadingDefault
                                                ? 'Loading...'
                                                : 'Load Default Template'}
                                        </Button>
                                    </div>
                                    <Textarea
                                        id="template"
                                        value={data.template}
                                        onChange={(e) =>
                                            setData('template', e.target.value)
                                        }
                                        placeholder="Enter your template content here..."
                                        className="min-h-[400px] font-mono text-sm"
                                        required
                                    />
                                    {errors.template && (
                                        <InputError message={errors.template} />
                                    )}
                                </div>

                                <div className="flex justify-end space-x-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => {
                                            setSelectedTemplateType('');
                                            reset();
                                        }}
                                    >
                                        Cancel
                                    </Button>
                                    <Button type="submit" disabled={processing}>
                                        {processing
                                            ? 'Saving...'
                                            : 'Save Template'}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    ) : (
                        <CardContent>
                            <div className="py-12 text-center text-muted-foreground">
                                <p className="text-lg font-medium">
                                    Coming Soon
                                </p>
                                <p className="text-sm">
                                    This template type will be available in a
                                    future update.
                                </p>
                            </div>
                        </CardContent>
                    )}
                </Card>
            )}

            {/* Existing Templates List */}
            {templates.filter(
                (template) =>
                    TEMPLATE_TYPES.find(
                        (t) => t.value === template.template_type,
                    )?.available,
            ).length > 0 && (
                <Card>
                    <CardHeader>
                        <CardTitle>Existing Templates</CardTitle>
                        <CardDescription>
                            Your organization's custom document templates
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-2">
                            {templates
                                .filter(
                                    (template) =>
                                        TEMPLATE_TYPES.find(
                                            (t) =>
                                                t.value ===
                                                template.template_type,
                                        )?.available,
                                )
                                .map((template) => (
                                    <div
                                        key={template.id}
                                        className="flex items-center justify-between rounded-lg border p-3"
                                    >
                                        <div>
                                            <div className="font-medium">
                                                {
                                                    TEMPLATE_TYPES.find(
                                                        (t) =>
                                                            t.value ===
                                                            template.template_type,
                                                    )?.label
                                                }
                                            </div>
                                            <div className="text-sm text-muted-foreground">
                                                Last updated:{' '}
                                                {new Date(
                                                    template.updated_at,
                                                ).toLocaleDateString()}
                                            </div>
                                        </div>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() =>
                                                setSelectedTemplateType(
                                                    template.template_type,
                                                )
                                            }
                                        >
                                            Edit
                                        </Button>
                                    </div>
                                ))}
                        </div>
                    </CardContent>
                </Card>
            )}
        </div>
    );
}
