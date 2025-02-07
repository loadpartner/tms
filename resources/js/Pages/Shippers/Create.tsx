import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import { Label } from '@/Components/ui/label';
import { router } from '@inertiajs/react';

interface Props extends PageProps {}

export default function Create({auth}: Props) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        city: '',
        state: '',
        // Add other fields as needed
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        router.post(route('shippers.store'), data);
    };

    return (
        <Authenticated auth={auth}>
            <Head title="Create Shipper" />
            <Card>
                <CardHeader>
                    <CardTitle>Create Shipper</CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div>
                            <Label htmlFor="name">Name</Label>
                            <Input
                                id="name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                required
                            />
                            {errors.name && <div className="text-red-500">{errors.name}</div>}
                        </div>
                        <div>
                            <Label htmlFor='city'>City</Label>
                            <Input id='city' value={data.city} onChange={e => setData('city', e.target.value)} />
                            {errors.city && <div className="text-red-500">{errors.city}</div>}
                        </div>
                        <div>
                            <Label htmlFor='state'>State</Label>
                            <Input id='state' value={data.state} onChange={e => setData('state', e.target.value)} />
                            {errors.state && <div className="text-red-500">{errors.state}</div>}
                        </div>

                        {/* Add other fields (address, contact info, etc.) */}
                        <Button type="submit" disabled={processing}>
                            Create
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </Authenticated>
    );
} 