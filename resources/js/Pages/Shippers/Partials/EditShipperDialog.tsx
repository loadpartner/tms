import React from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from '@/Components/ui/dialog';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { useForm } from '@inertiajs/react';
import { Shipper } from '@/types';
import { router } from '@inertiajs/react';

interface Props {
    open: boolean;
    setOpen: (open: boolean) => void;
    shipper: Shipper | null | undefined;
}

const EditShipperDialog: React.FC<Props> = ({ open, setOpen, shipper }) => {
    const { data, setData, patch, processing, errors } = useForm({
        name: shipper?.name || '',
        city: shipper?.city || '',
        state: shipper?.state || '',
        // Add other fields
    });

    React.useEffect(() => {
        if (shipper) {
            setData({
                name: shipper.name,
                city: shipper.city,
                state: shipper.state
            })
        }
    }, [shipper])

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (shipper) {
            router.put(route('shippers.update', shipper.id), data);
        }
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Edit Shipper</DialogTitle>
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
                        <Label htmlFor='city'>City</Label>
                        <Input id='city' value={data.city} onChange={e => setData('city', e.target.value)} />
                        {errors.city && <div className="text-red-500">{errors.city}</div>}
                    </div>
                    <div>
                        <Label htmlFor='state'>State</Label>
                        <Input id='state' value={data.state} onChange={e => setData('state', e.target.value)} />
                        {errors.state && <div className="text-red-500">{errors.state}</div>}
                    </div>
                    {/* Add other fields */}
                    <DialogFooter>
                        <DialogClose asChild>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" disabled={processing}>Save</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
};

export default EditShipperDialog; 