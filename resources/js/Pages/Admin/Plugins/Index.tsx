import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage, InertiaLink, useForm } from '@inertiajs/react';
import { Plugin as PluginType } from '@/types/plugin';
import { Button } from '@/Components/ui/button';
import { useRef, useState } from 'react';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

interface Props {
    plugins: PluginType[];
}

export default function Index({ plugins }: Props) {
    const { auth } = usePage().props;

    const activateForm = useForm({});
    const deactivateForm = useForm({});
    const installForm = useForm({
        plugin_zip: null, // For file upload
    });
    const [isInstallDialogOpen, setInstallDialogOpen] = useState(false);

    const handleActivate = (pluginSlug: string) => {
        activateForm.post(route('admin.plugins.activate', { plugin: pluginSlug }), {
            preserveScroll: true,
            onSuccess: () => activateForm.reset(),
        });
    };

    const handleDeactivate = (pluginSlug: string) => {
        deactivateForm.post(route('admin.plugins.deactivate', { plugin: pluginSlug }), {
            preserveScroll: true,
            onSuccess: () => deactivateForm.reset(),
        });
    };

    const handleInstallPlugin = () => {
        installForm.post(route('admin.plugins.install'), {
            preserveScroll: true,
            onSuccess: () => {
                installForm.reset();
                setInstallDialogOpen(false); // Close dialog on success
            },
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Plugins</h2>}
        >
            <Head title="Plugins" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="flex justify-between items-center mb-4">
                                <h3 className="text-lg font-semibold">Plugin Management</h3>
                                <Dialog open={isInstallDialogOpen} onOpenChange={setInstallDialogOpen}>
                                    <DialogTrigger asChild>
                                        <Button>Install Plugin</Button>
                                    </DialogTrigger>
                                    <DialogContent className="sm:max-w-[425px]">
                                        <DialogHeader>
                                            <DialogTitle>Install New Plugin</DialogTitle>
                                        </DialogHeader>
                                        <div className="grid gap-4 py-4">
                                            <div className="grid grid-cols-4 items-center gap-4">
                                                <Label htmlFor="plugin_zip" className="text-right">
                                                    Plugin Zip File
                                                </Label>
                                                <Input
                                                    id="plugin_zip"
                                                    name="plugin_zip"
                                                    type="file"
                                                    className="col-span-3"
                                                    onChange={(e) => installForm.setData('plugin_zip', e.target.files?.[0] || null)}
                                                />
                                            </div>
                                        </div>
                                        <DialogFooter>
                                            <Button type="button" onClick={handleInstallPlugin} disabled={installForm.processing}>
                                                Install
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Description
                                            </th>
                                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Version
                                            </th>
                                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Author
                                            </th>
                                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" className="relative px-6 py-3">
                                                <span className="sr-only">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {plugins.map((plugin) => (
                                            <tr key={plugin.id}>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm font-medium text-gray-900">{plugin.name}</div>
                                                    <div className="text-sm text-gray-500">{plugin.slug}</div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-normal text-sm text-gray-500">{plugin.description}</td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{plugin.version}</td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{plugin.author}</td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {plugin.is_active ? (
                                                        <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Active
                                                        </span>
                                                    ) : (
                                                        <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Inactive
                                                        </span>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    {!plugin.is_active ? (
                                                        <Button onClick={() => handleActivate(plugin.slug)}>Activate</Button>
                                                    ) : (
                                                        <Button variant="destructive" onClick={() => handleDeactivate(plugin.slug)}>Deactivate</Button>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 