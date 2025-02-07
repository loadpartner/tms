import React, { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogClose } from '@/Components/ui/dialog';
import { Button } from '@/Components/ui/button';
import { Skeleton } from '@/Components/ui/skeleton';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/Components/ui/table"
import { Input } from '@/Components/ui/input';
import { router } from '@inertiajs/react';

interface Props {
    open: boolean;
    setOpen: (open: boolean) => void;
    shipperId: number | null | undefined;
}

interface Load {
    id: number;
    // Define other load properties
    status: string;
}

const ShipperLoadsDialog: React.FC<Props> = ({ open, setOpen, shipperId }) => {

    const [loads, setLoads] = useState<Load[]>([]);
    const [loading, setLoading] = useState(false);
    const [activeOnly, setActiveOnly] = useState(false);
    const [searchTerm, setSearchTerm] = useState('');

    useEffect(() => {
        if (shipperId && open) {
            setLoading(true);
            router.get(route('shippers.loads', {shipper: shipperId, activeOnly: activeOnly, search: searchTerm}), {},
            {
                preserveState: true,
                onSuccess: (page) => {
                    setLoads(page.props.loads as Load[]);
                    setLoading(false);
                },
                onError: (err) => {
                    console.error(err);
                    setLoading(false);
                }
            });
        }
    }, [shipperId, open, activeOnly, searchTerm]);

    const filteredLoads = loads.filter(load =>
        Object.values(load).some(value =>
            String(value).toLowerCase().includes(searchTerm.toLowerCase())
        )
    );

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Loads for Shipper</DialogTitle>
                    <div className="flex items-center space-x-2">
                        <Input
                            placeholder="Search loads..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                        />
                        <label className="flex items-center space-x-2">
                            <input
                                type="checkbox"
                                checked={activeOnly}
                                onChange={(e) => setActiveOnly(e.target.checked)}
                            />
                            <span>Active Only</span>
                        </label>
                    </div>
                </DialogHeader>
                {loading ? (
                    <Skeleton className="h-8 w-full" />
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>ID</TableHead>
                                <TableHead>Status</TableHead>
                                {/* Add other headers */}
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filteredLoads.map((load) => (
                                <TableRow key={load.id}>
                                    <TableCell>{load.id}</TableCell>
                                    <TableCell>{load.status}</TableCell>
                                    {/* Add other cells */}
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}
                <DialogFooter>
                    <DialogClose asChild>
                        <Button>Close</Button>
                    </DialogClose>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
};

export default ShipperLoadsDialog; 