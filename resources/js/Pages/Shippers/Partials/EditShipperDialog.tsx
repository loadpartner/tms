import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Shipper } from '@/types';
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';

interface Props {
    open: boolean;
    setOpen: (open: boolean) => void;
    shipper: Shipper;
}

export default function EditShipperDialog({ open, setOpen, shipper }: Props) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        address: '',
        city: '',
        state: '',
        zip: '',
        contact_name: '',
        phone: '',
        email: '',
    });

    useEffect(() => {
        if (open && shipper) {
            setData({
                name: shipper.name,
                address: shipper.address || '',
                city: shipper.city || '',
                state: shipper.state || '',
                zip: shipper.zip || '',
                contact_name: shipper.contact_name || '',
                phone: shipper.phone || '',
                email: shipper.email || '',
            });
        } else {
            reset();
        }
    }, [open, shipper]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('shippers.update', shipper.id), {
            preserveScroll: true,
            onSuccess: () => setOpen(false),
        });
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Edit Shipper</DialogTitle>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <Label htmlFor="name">Name</Label>
                            <Input
                                id="name"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                            />
                            {errors.name && (
                                <p className="text-sm text-red-500">
                                    {errors.name}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="contact_name">Contact Name</Label>
                            <Input
                                id="contact_name"
                                value={data.contact_name}
                                onChange={(e) =>
                                    setData('contact_name', e.target.value)
                                }
                            />
                            {errors.contact_name && (
                                <p className="text-sm text-red-500">
                                    {errors.contact_name}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="phone">Phone</Label>
                            <Input
                                id="phone"
                                value={data.phone}
                                onChange={(e) =>
                                    setData('phone', e.target.value)
                                }
                            />
                            {errors.phone && (
                                <p className="text-sm text-red-500">
                                    {errors.phone}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                value={data.email}
                                onChange={(e) =>
                                    setData('email', e.target.value)
                                }
                            />
                            {errors.email && (
                                <p className="text-sm text-red-500">
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="address">Address</Label>
                            <Input
                                id="address"
                                value={data.address}
                                onChange={(e) =>
                                    setData('address', e.target.value)
                                }
                            />
                            {errors.address && (
                                <p className="text-sm text-red-500">
                                    {errors.address}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="city">City</Label>
                            <Input
                                id="city"
                                value={data.city}
                                onChange={(e) =>
                                    setData('city', e.target.value)
                                }
                            />
                            {errors.city && (
                                <p className="text-sm text-red-500">
                                    {errors.city}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="state">State</Label>
                            <Input
                                id="state"
                                value={data.state}
                                onChange={(e) =>
                                    setData('state', e.target.value)
                                }
                            />
                            {errors.state && (
                                <p className="text-sm text-red-500">
                                    {errors.state}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="zip">ZIP</Label>
                            <Input
                                id="zip"
                                value={data.zip}
                                onChange={(e) => setData('zip', e.target.value)}
                            />
                            {errors.zip && (
                                <p className="text-sm text-red-500">
                                    {errors.zip}
                                </p>
                            )}
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => setOpen(false)}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" disabled={processing}>
                            Save Changes
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
} 