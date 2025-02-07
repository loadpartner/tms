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
import { useForm } from '@inertiajs/react';
import { Textarea } from '@/Components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';

interface Props {
    open: boolean;
    setOpen: (open: boolean) => void;
    shipperId: number;
}

export default function CreateLocationDialog({ open, setOpen, shipperId }: Props) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        address: '',
        city: '',
        state: '',
        zip: '',
        type: '',
        notes: '',
        shipper_id: shipperId,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('locations.store'), {
            preserveScroll: true,
            onSuccess: () => {
                setOpen(false);
                reset();
            },
        });
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Add Location</DialogTitle>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <Label htmlFor="name">Location Name</Label>
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
                            <Label htmlFor="type">Location Type</Label>
                            <Select
                                value={data.type}
                                onValueChange={(value) =>
                                    setData('type', value)
                                }
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Select type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="warehouse">
                                        Warehouse
                                    </SelectItem>
                                    <SelectItem value="distribution_center">
                                        Distribution Center
                                    </SelectItem>
                                    <SelectItem value="retail">
                                        Retail Location
                                    </SelectItem>
                                    <SelectItem value="other">Other</SelectItem>
                                </SelectContent>
                            </Select>
                            {errors.type && (
                                <p className="text-sm text-red-500">
                                    {errors.type}
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

                    <div className="space-y-2">
                        <Label htmlFor="notes">Notes</Label>
                        <Textarea
                            id="notes"
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                            rows={3}
                        />
                        {errors.notes && (
                            <p className="text-sm text-red-500">{errors.notes}</p>
                        )}
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
                            Add Location
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
} 