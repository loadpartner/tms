'use client';

import DatetimeDisplay from '@/Components/DatetimeDisplay';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Shipment } from '@/types';
import { Link } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { ArrowUpRightFromSquare } from 'lucide-react';

export const columns: ColumnDef<Shipment>[] = [
    {
        accessorKey: 'id',
        header: '#',
        cell: ({ row }) => {
            return (
                <Button variant="outline" asChild>
                    <Link
                        href={route('shipments.show', row.original.id)}
                        className="flex gap-1"
                        prefetch={true}
                    >
                        <span className="text-sm">
                            {row.original.shipment_number ?? row.original.id}
                        </span>
                        <ArrowUpRightFromSquare className="h-2 w-2" />
                    </Link>
                </Button>
            );
        },
    },
    {
        accessorKey: 'state',
        header: 'Status',
        cell: ({ row }) => {
            return <Badge variant="outline">{row.original.state_label}</Badge>;
        },
    },
    {
        accessorKey: 'customers',
        header: 'Customers',
        cell: ({ row }) => {
            const customers = row.original.customers;
            return (
                <div className="flex flex-col space-y-2">
                    {customers.map((customer) => (
                        <Badge
                            variant="outline"
                            className="w-fit"
                            key={customer.id}
                        >
                            {customer.name}
                        </Badge>
                    ))}
                </div>
            );
        },
    },
    {
        accessorKey: 'carrier',
        header: 'Carrier',
        cell: ({ row }) => {
            const carrier = row.original.carrier;

            return (
                <div className="flex flex-col space-y-2">
                    <span>{carrier?.name ?? '-'}</span>
                    <span className="text-muted-foreground">
                        {row.original.trailer_size?.name}{' '}
                        {row.original.trailer_type?.name}
                    </span>
                </div>
            );
        },
    },
    {
        accessorKey: 'lane',
        header: 'Lane',
        cell: ({ row }) => {
            return <span className="text-nowrap">{row.original.lane}</span>;
        },
    },
    {
        accessorKey: 'next_stop',
        header: 'Next Appointment',
        cell: ({ row }) => {
            return (
                <div className="flex flex-col space-y-2">
                    <DatetimeDisplay
                        datetime={row.original.next_stop?.appointment_at}
                    />
                    <span className="text-muted-foreground">
                        {row.original.next_stop?.stop_type}
                    </span>
                </div>
            );
        },
    },
    // {
    //     accessorKey: 'stops',
    //     header: 'Stops',
    // },
];
