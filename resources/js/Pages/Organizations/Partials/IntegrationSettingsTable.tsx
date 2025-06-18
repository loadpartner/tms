import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { useToast } from '@/hooks/UseToast';
import {
    GlobalIntegrationSetting,
    IntegrationSetting,
    Organization,
} from '@/types/organization';
import { useForm } from '@inertiajs/react';
import { Eye, EyeOff, Plus } from 'lucide-react';
import { useCallback, useEffect, useMemo, useState } from 'react';

interface Props {
    integrationSettings: IntegrationSetting[];
    organization: Organization;
    globalIntegrationSettings: GlobalIntegrationSetting[];
}

interface IntegrationSettingForm {
    key: string;
    value: string;
    provider?: string;
    expose_to_frontend: boolean;
    [key: string]: string | boolean | undefined;
}

interface ProviderGroup {
    name: string;
    settings: (IntegrationSetting | GlobalIntegrationSetting)[];
    isGlobal?: boolean;
}

export default function IntegrationSettingsTable({
    integrationSettings,
    organization,
    globalIntegrationSettings,
}: Props) {
    const { toast } = useToast();
    const {
        data,
        setData,
        post,
        processing,
        errors,
        reset,
        setError,
        delete: destroy,
    } = useForm<IntegrationSettingForm>({
        key: '',
        value: '',
        provider: '',
        expose_to_frontend: false,
    });
    const [open, setOpen] = useState(false);
    const [revealedId, setRevealedId] = useState<number | string | null>(null);
    const [activeSetting, setActiveSetting] =
        useState<IntegrationSetting | null>(null);
    const [selectedProvider, setSelectedProvider] = useState<string | null>(
        null,
    );
    const [activeGlobalSetting, setActiveGlobalSetting] =
        useState<GlobalIntegrationSetting | null>(null);

    // Group settings by provider
    const providerGroups = useMemo(() => {
        const groups: Record<string, ProviderGroup> = {};

        // Add global settings first
        globalIntegrationSettings.forEach((setting) => {
            const provider = setting.provider || 'Uncategorized';
            if (!groups[provider]) {
                groups[provider] = {
                    name: provider,
                    settings: [],
                    isGlobal: true,
                };
            }
            groups[provider].settings.push(setting);
        });

        // Add organization settings, marking those that override globals
        integrationSettings.forEach((setting) => {
            const provider = setting.provider || 'Other';
            if (!groups[provider]) {
                groups[provider] = { name: provider, settings: [] };
            }

            // Check if this setting already exists as a global
            const existsAsGlobal = groups[provider].settings.some(
                (s) => 'key' in s && s.key === setting.key,
            );

            if (!existsAsGlobal) {
                groups[provider].settings.push(setting);
            } else {
                // Replace the global setting with the organization setting
                groups[provider].settings = groups[provider].settings.map(
                    (s) => {
                        if ('key' in s && s.key === setting.key) {
                            return { ...setting, _overridesGlobal: true };
                        }
                        return s;
                    },
                );
            }
        });

        return Object.values(groups);
    }, [integrationSettings, globalIntegrationSettings]);

    // Set default selected provider
    useEffect(() => {
        if (providerGroups.length > 0 && !selectedProvider) {
            setSelectedProvider(providerGroups[0].name);
        }
    }, [providerGroups, selectedProvider]);

    const currentProviderSettings = useMemo(() => {
        if (!selectedProvider) return [];

        const group = providerGroups.find((g) => g.name === selectedProvider);
        return group ? group.settings : [];
    }, [selectedProvider, providerGroups]);

    const handleAddOrEdit = useCallback(
        (e: React.FormEvent) => {
            e.preventDefault();

            // If adding, check for duplicate key
            if (!activeSetting) {
                const keyExists = integrationSettings.some(
                    (setting) => setting.key === data.key,
                );
                if (keyExists) {
                    setError(
                        'key',
                        'This integration setting key already exists.',
                    );
                    return;
                }
            }

            post(
                route('organizations.integration-settings.store', {
                    organization: organization.id,
                }),
                {
                    preserveScroll: true,
                    replace: true,
                    onSuccess: () => {
                        reset();
                        setOpen(false);
                        setActiveSetting(null);
                        setActiveGlobalSetting(null);
                        toast({
                            description: activeSetting
                                ? 'Integration setting updated!'
                                : 'Integration setting added!',
                        });
                    },
                },
            );
        },
        [
            integrationSettings,
            data,
            reset,
            toast,
            setOpen,
            activeSetting,
            setError,
            post,
            organization.id,
        ],
    );

    const handleEdit = (setting: IntegrationSetting) => {
        setActiveSetting(setting);

        // Find if there's a corresponding global setting
        const globalSetting = globalIntegrationSettings.find(
            (global) => global.key === setting.key,
        );

        setActiveGlobalSetting(globalSetting || null);

        setData({
            key: setting.key,
            value: setting.value,
            provider: setting.provider || '',
            expose_to_frontend: setting.expose_to_frontend,
        });
        setOpen(true);
    };

    const handleAddFromGlobal = (globalSetting: GlobalIntegrationSetting) => {
        setActiveGlobalSetting(globalSetting);
        setActiveSetting(null);

        // Find if this global setting already has an organization override
        const existingSetting = integrationSettings.find(
            (setting) => setting.key === globalSetting.key,
        );

        if (existingSetting) {
            // If it exists, treat this as an edit
            handleEdit(existingSetting);
            return;
        }

        setData({
            key: globalSetting.key,
            value: globalSetting.value || '',
            provider: globalSetting.provider || '',
            expose_to_frontend: globalSetting.expose_to_frontend || false,
        });
        setOpen(true);
    };

    const handleDelete = (setting: IntegrationSetting) => {
        destroy(
            route('organizations.integration-settings.destroy', {
                organization: organization.id,
                setting: setting.id,
            }),
            {
                preserveScroll: true,
                replace: true,
                onSuccess: () => {
                    toast({ description: 'Integration setting deleted!' });
                },
                onError: () => {
                    toast({
                        description: 'Failed to delete integration setting',
                        variant: 'destructive',
                    });
                },
            },
        );
    };

    const handleAddCustom = () => {
        setOpen(true);
        setActiveSetting(null);
        setActiveGlobalSetting(null);
        setData({
            key: '',
            value: '',
            provider:
                selectedProvider && selectedProvider !== 'Other'
                    ? selectedProvider
                    : '',
            expose_to_frontend: false,
        });
    };

    return (
        <div>
            <Button onClick={handleAddCustom} className="mb-4">
                <Plus className="mr-2 h-4 w-4" />
                Add Custom Setting
            </Button>

            <Dialog
                open={open}
                onOpenChange={(val) => {
                    setOpen(val);
                    if (!val) {
                        setActiveSetting(null);
                        setActiveGlobalSetting(null);
                        reset();
                    }
                }}
            >
                <DialogContent>
                    <form onSubmit={handleAddOrEdit}>
                        <DialogHeader>
                            <DialogTitle className="mb-4">
                                {activeSetting
                                    ? 'Edit Setting'
                                    : activeGlobalSetting
                                      ? 'Override Global Setting'
                                      : 'Add Custom Setting'}
                            </DialogTitle>
                            <div className="flex flex-col gap-4">
                                <div className="space-y-2">
                                    <label
                                        htmlFor="key"
                                        className="text-sm font-medium"
                                    >
                                        Key
                                    </label>
                                    <Input
                                        id="key"
                                        name="key"
                                        placeholder="Key"
                                        value={data.key}
                                        onChange={(e) =>
                                            setData('key', e.target.value)
                                        }
                                        required
                                        disabled={
                                            !!activeSetting ||
                                            !!activeGlobalSetting
                                        }
                                    />
                                    <InputError message={errors.key} />
                                </div>

                                <div className="space-y-2">
                                    <label
                                        htmlFor="value"
                                        className="text-sm font-medium"
                                    >
                                        Value
                                    </label>
                                    <Input
                                        id="value"
                                        name="value"
                                        placeholder="Value"
                                        value={data.value}
                                        onChange={(e) =>
                                            setData('value', e.target.value)
                                        }
                                        required
                                    />
                                    <InputError message={errors.value} />
                                </div>

                                <div className="space-y-2">
                                    <label
                                        htmlFor="provider"
                                        className="text-sm font-medium"
                                    >
                                        Provider
                                    </label>
                                    <Input
                                        id="provider"
                                        name="provider"
                                        placeholder="Provider"
                                        value={data.provider}
                                        onChange={(e) =>
                                            setData('provider', e.target.value)
                                        }
                                        disabled={!!activeGlobalSetting}
                                    />
                                    <InputError message={errors.provider} />
                                </div>

                                <div className="space-y-2">
                                    <label className="flex items-center gap-2">
                                        <Checkbox
                                            id="expose_to_frontend"
                                            name="expose_to_frontend"
                                            checked={data.expose_to_frontend}
                                            onCheckedChange={(checked) =>
                                                setData(
                                                    'expose_to_frontend',
                                                    !!checked,
                                                )
                                            }
                                            disabled={!!activeGlobalSetting}
                                        />
                                        <span className="text-sm font-medium">
                                            Expose to Frontend
                                        </span>
                                    </label>
                                    <InputError
                                        message={errors.expose_to_frontend}
                                    />
                                </div>

                                {activeGlobalSetting && (
                                    <div className="rounded-md bg-muted p-3 text-sm text-muted-foreground">
                                        <p>
                                            This setting is derived from global
                                            configuration.
                                        </p>
                                        <p>Only the value can be overridden.</p>
                                    </div>
                                )}
                            </div>
                        </DialogHeader>
                        <DialogFooter>
                            <Button disabled={processing} type="submit">
                                Save
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <div className="grid grid-cols-4 gap-6">
                {/* Left Sidebar - Provider List */}
                <div className="col-span-1 border-r border-border pr-4">
                    <h3 className="mb-2 font-semibold">Providers</h3>
                    <ul className="space-y-1">
                        {providerGroups.map((group) => (
                            <li
                                key={group.name}
                                className={`cursor-pointer rounded-md p-2 hover:bg-muted ${
                                    selectedProvider === group.name
                                        ? 'bg-muted font-medium'
                                        : ''
                                }`}
                                onClick={() => setSelectedProvider(group.name)}
                            >
                                {group.name}
                            </li>
                        ))}
                    </ul>
                </div>

                {/* Right Content - Settings */}
                <div className="col-span-3">
                    <h3 className="mb-4 font-semibold">
                        {selectedProvider} Settings
                    </h3>

                    {currentProviderSettings.length === 0 ? (
                        <div className="py-8 text-center text-muted-foreground">
                            No settings found for this provider
                        </div>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Key</TableHead>
                                    <TableHead>Value</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Available on Frontend</TableHead>
                                    <TableHead className="text-right">
                                        Actions
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {currentProviderSettings.map((setting) => {
                                    const isGlobalSetting = !('id' in setting);
                                    const overridesGlobal =
                                        'id' in setting &&
                                        '_overridesGlobal' in setting;

                                    // Find the corresponding organization setting if this is a global setting
                                    const orgSetting = isGlobalSetting
                                        ? integrationSettings.find(
                                              (s) => s.key === setting.key,
                                          )
                                        : null;

                                    return (
                                        <TableRow
                                            key={
                                                isGlobalSetting
                                                    ? `global-${setting.key}`
                                                    : setting.id
                                            }
                                        >
                                            <TableCell>
                                                <div className="flex flex-col">
                                                    <span>{setting.key}</span>
                                                    {isGlobalSetting && (
                                                        <>
                                                            {setting.label && (
                                                                <span className="text-sm font-medium text-muted-foreground">
                                                                    {
                                                                        setting.label
                                                                    }
                                                                </span>
                                                            )}
                                                            {setting.description && (
                                                                <span className="text-xs italic text-muted-foreground/70">
                                                                    {
                                                                        setting.description
                                                                    }
                                                                </span>
                                                            )}
                                                        </>
                                                    )}
                                                    {!isGlobalSetting && (
                                                        <>
                                                            {globalIntegrationSettings.some(
                                                                (g) =>
                                                                    g.key ===
                                                                    setting.key,
                                                            ) && (
                                                                <>
                                                                    {globalIntegrationSettings.find(
                                                                        (g) =>
                                                                            g.key ===
                                                                            setting.key,
                                                                    )
                                                                        ?.label && (
                                                                        <span className="text-sm font-medium text-muted-foreground">
                                                                            {
                                                                                globalIntegrationSettings.find(
                                                                                    (
                                                                                        g,
                                                                                    ) =>
                                                                                        g.key ===
                                                                                        setting.key,
                                                                                )
                                                                                    ?.label
                                                                            }
                                                                        </span>
                                                                    )}
                                                                    {globalIntegrationSettings.find(
                                                                        (g) =>
                                                                            g.key ===
                                                                            setting.key,
                                                                    )
                                                                        ?.description && (
                                                                        <span className="text-xs italic text-muted-foreground/70">
                                                                            {
                                                                                globalIntegrationSettings.find(
                                                                                    (
                                                                                        g,
                                                                                    ) =>
                                                                                        g.key ===
                                                                                        setting.key,
                                                                                )
                                                                                    ?.description
                                                                            }
                                                                        </span>
                                                                    )}
                                                                </>
                                                            )}
                                                        </>
                                                    )}
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                {isGlobalSetting ? (
                                                    orgSetting ? (
                                                        <span className="flex items-center gap-2">
                                                            {revealedId ===
                                                            orgSetting.id ? (
                                                                orgSetting.value
                                                            ) : (
                                                                <span className="italic text-muted-foreground/70">
                                                                    ••••••••
                                                                </span>
                                                            )}
                                                            <Button
                                                                size="sm"
                                                                variant="ghost"
                                                                type="button"
                                                                onClick={() =>
                                                                    setRevealedId(
                                                                        revealedId ===
                                                                            orgSetting.id
                                                                            ? null
                                                                            : orgSetting.id,
                                                                    )
                                                                }
                                                            >
                                                                {revealedId ===
                                                                orgSetting.id ? (
                                                                    <EyeOff />
                                                                ) : (
                                                                    <Eye />
                                                                )}
                                                            </Button>
                                                        </span>
                                                    ) : (
                                                        <span className="flex items-center gap-2">
                                                            {revealedId ===
                                                            `global-${setting.key}` ? (
                                                                setting.value ||
                                                                '(Not set)'
                                                            ) : (
                                                                <span className="italic text-muted-foreground/70">
                                                                    ••••••••
                                                                </span>
                                                            )}
                                                            <Button
                                                                size="sm"
                                                                variant="ghost"
                                                                type="button"
                                                                onClick={() =>
                                                                    setRevealedId(
                                                                        revealedId ===
                                                                            `global-${setting.key}`
                                                                            ? null
                                                                            : `global-${setting.key}`,
                                                                    )
                                                                }
                                                            >
                                                                {revealedId ===
                                                                `global-${setting.key}` ? (
                                                                    <EyeOff />
                                                                ) : (
                                                                    <Eye />
                                                                )}
                                                            </Button>
                                                        </span>
                                                    )
                                                ) : (
                                                    <span className="flex items-center gap-2">
                                                        {revealedId ===
                                                        setting.id ? (
                                                            setting.value
                                                        ) : (
                                                            <span className="italic text-muted-foreground/70">
                                                                ••••••••
                                                            </span>
                                                        )}
                                                        <Button
                                                            size="sm"
                                                            variant="ghost"
                                                            type="button"
                                                            onClick={() =>
                                                                setRevealedId(
                                                                    revealedId ===
                                                                        setting.id
                                                                        ? null
                                                                        : setting.id,
                                                                )
                                                            }
                                                        >
                                                            {revealedId ===
                                                            setting.id ? (
                                                                <EyeOff />
                                                            ) : (
                                                                <Eye />
                                                            )}
                                                        </Button>
                                                    </span>
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                {isGlobalSetting ? (
                                                    orgSetting ? (
                                                        <span className="text-sm text-amber-500 dark:text-amber-400">
                                                            Overridden
                                                        </span>
                                                    ) : (
                                                        <span className="text-sm text-blue-500 dark:text-blue-400">
                                                            Global Default
                                                        </span>
                                                    )
                                                ) : overridesGlobal ? (
                                                    <span className="text-sm text-amber-500 dark:text-amber-400">
                                                        Overridden
                                                    </span>
                                                ) : (
                                                    <span className="text-sm text-green-500 dark:text-green-400">
                                                        Custom
                                                    </span>
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                {isGlobalSetting
                                                    ? orgSetting
                                                        ? orgSetting.expose_to_frontend
                                                            ? 'Yes'
                                                            : 'No'
                                                        : setting.expose_to_frontend
                                                          ? 'Yes'
                                                          : 'No'
                                                    : setting.expose_to_frontend
                                                      ? 'Yes'
                                                      : 'No'}
                                            </TableCell>
                                            <TableCell className="flex justify-end space-x-2">
                                                {isGlobalSetting ? (
                                                    orgSetting ? (
                                                        <>
                                                            <PrimaryButton
                                                                onClick={() =>
                                                                    handleEdit(
                                                                        orgSetting,
                                                                    )
                                                                }
                                                            >
                                                                Edit Override
                                                            </PrimaryButton>
                                                            <Button
                                                                variant="destructive"
                                                                onClick={() =>
                                                                    handleDelete(
                                                                        orgSetting,
                                                                    )
                                                                }
                                                            >
                                                                Remove Override
                                                            </Button>
                                                        </>
                                                    ) : (
                                                        <PrimaryButton
                                                            onClick={() =>
                                                                handleAddFromGlobal(
                                                                    setting as GlobalIntegrationSetting,
                                                                )
                                                            }
                                                        >
                                                            Override
                                                        </PrimaryButton>
                                                    )
                                                ) : (
                                                    <>
                                                        <PrimaryButton
                                                            onClick={() =>
                                                                handleEdit(
                                                                    setting as IntegrationSetting,
                                                                )
                                                            }
                                                        >
                                                            Edit
                                                        </PrimaryButton>
                                                        <Button
                                                            variant="destructive"
                                                            onClick={() =>
                                                                handleDelete(
                                                                    setting as IntegrationSetting,
                                                                )
                                                            }
                                                        >
                                                            Delete
                                                        </Button>
                                                    </>
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    )}
                </div>
            </div>
        </div>
    );
}
