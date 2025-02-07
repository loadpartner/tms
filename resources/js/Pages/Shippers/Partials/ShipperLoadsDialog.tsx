import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Shipment } from '@/types';
import { Switch } from '@/Components/ui/switch';
import { Label } from '@/Components/ui/label';
import axios from 'axios';
import { useCallback, useEffect, useState } from 'react';
import { DataTable } from '@/Components/ui/data-table';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { Badge } from '@/Components/ui/badge';
import { Link } from '@inertiajs/react';

interface Props {
    open: boolean;
    setOpen: (open: boolean) => void;
    shipperId: number;
}

export default function ShipperLoadsDialog({ open, setOpen, shipperId }: Props) {
    const [loads, setLoads] = useState<Shipment[]>([]);
    const [loading, setLoading] = useState(false);
    const [showActiveOnly, setShowActiveOnly] = useState(true);

    const fetchLoads = useCallback(() => {
        if (!shipperId) return;

        setLoading(true);
        axios
            .get(route('shippers.loads', shipperId), {
                params: {
                    active_only: showActiveOnly,
                },
            })
            .then((response) => {
                setLoads(response.data);
                setLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching loads:', error);
                setLoading(false);
            });
    }, [shipperId, showActiveOnly]);

    useEffect(() => {
        if (open) {
            fetchLoads();
        }
    }, [open, fetchLoads]);

    const columns: ColumnDef<Shipment>[] = [
        {
            accessorKey: 'reference_number',
            header: 'Reference #',
            cell: ({ row }) => (
                <Link
                    href={route('shipments.show', row.original.id)}
                    className="text-primary hover:underline"
                >
                    {row.original.reference_number}
                </Link>
            ),
        },
        {
            accessorKey: 'pickup_date',
            header: 'Pickup Date',
            cell: ({ row }) =>
                format(new Date(row.original.pickup_date), 'MM/dd/yyyy'),
        },
        {
            accessorKey: 'delivery_date',
            header: 'Delivery Date',
            cell: ({ row }) =>
                format(new Date(row.original.delivery_date), 'MM/dd/yyyy'),
        },
        {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => (
                <Badge variant="outline">{row.original.status}</Badge>
            ),
        },
        {
            accessorKey: 'carrier.name',
            header: 'Carrier',
        },
    ];

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogContent className="max-w-4xl">
                <DialogHeader>
                    <DialogTitle>Shipper Loads</DialogTitle>
                </DialogHeader>

                <div className="space-y-6">
                    <div className="flex items-center space-x-2">
                        <Switch
                            id="active-only"
                            checked={showActiveOnly}
                            onCheckedChange={setShowActiveOnly}
                        />
                        <Label htmlFor="active-only">Show active loads only</Label>
                    </div>

                    <DataTable
                        columns={columns}
                        data={loads}
                        loading={loading}
                    />
                </div>
            </DialogContent>
        </Dialog>
    );
} 