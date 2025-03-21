'use client';

import { Customer } from '@/types';
import { ColumnDef } from '@tanstack/react-table';

export const columns: ColumnDef<Customer>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => {
            return (
                <div className="flex flex-col space-y-2">
                    {row.original.name}
                </div>
            );
        },
    },
];
