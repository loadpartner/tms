import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Shipper } from '@/types';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { Link } from '@inertiajs/react';
import axios from 'axios';
import { Search } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';

interface Props {
    selectedShipper: Shipper | null;
    onShipperSelect: (shipper: Shipper) => void;
}

export default function ShipperList({ selectedShipper, onShipperSelect }: Props) {
    const [shippers, setShippers] = useState<Shipper[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const inputRef = useRef<HTMLInputElement>(null);

    const getShippers = useCallback((searchTerm?: string) => {
        const getData = (): Promise<Shipper[]> => {
            return axios
                .get(route('shippers.search'), {
                    params: {
                        query: searchTerm,
                    },
                })
                .then((response) => response.data);
        };

        setIsLoading(true);

        getData()
            .then((shippers) => {
                setShippers(shippers);
                setIsLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching shippers:', error);
                setIsLoading(false);
            });
    }, []);

    useEffect(() => {
        getShippers(searchTerm);
    }, [getShippers, searchTerm]);

    useEffect(() => {
        if (!isLoading) {
            inputRef.current?.focus();
        }
    }, [isLoading]);

    return (
        <div className="p-6 space-y-4">
            <div className="flex items-center justify-between gap-4">
                <div className="relative flex-1">
                    <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input
                        ref={inputRef}
                        placeholder="Search shippers..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className="pl-8"
                    />
                </div>
                <Link href={route('shippers.create')}>
                    <Button>Add Shipper</Button>
                </Link>
            </div>

            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>City</TableHead>
                            <TableHead>State</TableHead>
                            <TableHead>Contact</TableHead>
                            <TableHead>Phone</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {shippers.map((shipper) => (
                            <TableRow
                                key={shipper.id}
                                onClick={() => onShipperSelect(shipper)}
                                className={`cursor-pointer hover:bg-muted ${
                                    selectedShipper?.id === shipper.id
                                        ? 'bg-muted'
                                        : ''
                                }`}
                            >
                                <TableCell className="font-medium">
                                    {shipper.name}
                                </TableCell>
                                <TableCell>{shipper.city}</TableCell>
                                <TableCell>{shipper.state}</TableCell>
                                <TableCell>{shipper.contact_name}</TableCell>
                                <TableCell>{shipper.phone}</TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </div>
    );
} 