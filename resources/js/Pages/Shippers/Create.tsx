import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

export default function ShipperCreate() {
  const { data, setData, post, processing, errors } = useForm({
    name: '',
    mc_number: '',
    dot_number: '',
    status: 'active',
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    post(route('shippers.store'));
  };

  return (
    <AuthenticatedLayout>
      <Head title="Create Shipper" />
      <div className="p-6">
        <h1 className="text-2xl font-bold mb-6">Create New Shipper</h1>
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
          <div className="flex justify-end">
            <Button type="submit" disabled={processing}>
              Create Shipper
            </Button>
          </div>
        </form>
      </div>
    </AuthenticatedLayout>
  );
} 