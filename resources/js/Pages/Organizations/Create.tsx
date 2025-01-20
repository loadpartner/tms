import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import GuestLayout from '@/Layouts/GuestLayout';
import { cn } from '@/lib/utils';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function CreateOrganization({ status }: { status?: string }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('organizations.store'), {
            onFinish: () => reset('name'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Create an organization" />

            <div className={cn('flex flex-col gap-6')}>
                <form onSubmit={submit}>
                    <div className="flex flex-col gap-6">
                        <h1 className="mb-8 text-xl font-bold">
                            Create a new organization
                        </h1>
                    </div>
                    <div className="flex flex-col gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="name">Name</Label>
                            <Input
                                id="name"
                                type="text"
                                placeholder="Acme Inc."
                                required
                                className="bg-white"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                                tabIndex={1}
                            />
                            <InputError
                                message={errors.name}
                                className="mt-2"
                            />
                        </div>
                        <Button
                            type="submit"
                            className="w-full"
                            disabled={processing}
                            tabIndex={2}
                        >
                            Create
                        </Button>
                    </div>
                </form>
            </div>
            {status && (
                <div className="mb-4 w-full text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}
        </GuestLayout>
    );
}
