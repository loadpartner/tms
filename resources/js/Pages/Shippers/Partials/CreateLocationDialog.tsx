import React from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from '@/Components/ui/dialog';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { useForm } from '@inertiajs/react';
import { router } from '@inertiajs/react';

interface Props {
    open: boolean;
    setOpen: (open: boolean) => void;
    shipperId: number | null | undefined;
}

const CreateLocationDialog: React.FC<Props> = ({ open, setOpen, shipperId }) => {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        address: '',
        city: '',
        state: '',
        zip: '',
        // Add other fields as needed
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (shipperId) {
            router.post(route('shippers.locations.store', { shipper: shipperId }), data);
        }
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Add Location to Shipper</DialogTitle>
                </DialogHeader>
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
                        <Label htmlFor="address">Address</Label>
                        <Input
                            id="address"
                            value={data.address}
                            onChange={(e) => setData('address', e.target.value)}
                        />
                        {errors.address && <div className="text-red-500">{errors.address}</div>}
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
                    <div>
                        <Label htmlFor='zip'>Zip Code</Label>
                        <Input id='zip' value={data.zip} onChange={e => setData('zip', e.target.value)} />
                        {errors.zip && <div className="text-red-500">{errors.zip}</div>}
                    </div>

                    {/* Add other fields */}
                    <DialogFooter>
                        <DialogClose asChild>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" disabled={processing}>Add Location</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
};

export default CreateLocationDialog; 