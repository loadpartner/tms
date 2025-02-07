import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

export default function ShipperEdit({ shipper }) {
    const { data, setData, put, processing, errors } = useForm({
        name: shipper.name,
        mc_number: shipper.mc_number,
        dot_number: shipper.dot_number,
        status: shipper.status,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('shippers.update', shipper.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${shipper.name}`} />
            <div className="p-6">
                <h1 className="text-2xl font-bold mb-6">Edit Shipper</h1>
                <form onSubmit={handleSubmit} className="space-y-4 max-w-2xl">
                    <div className="space-y-2">
                        <Label htmlFor="name">Name</Label>
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            error={errors.name}
                        />
                    </div>
                    <div className="space-y-2">
                        <Label htmlFor="mc_number">MC Number</Label>
                        <Input
                            id="mc_number"
                            value={data.mc_number}
                            onChange={(e) => setData('mc_number', e.target.value)}
                            error={errors.mc_number}
                        />
                    </div>
                    <div className="space-y-2">
                        <Label htmlFor="dot_number">DOT Number</Label>
                        <Input
                            id="dot_number"
                            value={data.dot_number}
                            onChange={(e) => setData('dot_number', e.target.value)}
                            error={errors.dot_number}
                        />
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="outline" asChild>
                            <Link href={route('shippers.index')}>Cancel</Link>
                        </Button>
                        <Button type="submit" disabled={processing}>
                            Save Changes
                        </Button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
} 