import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Loading } from '@/Components/ui/loading';
import { Carrier } from '@/types';
import axios from 'axios';
import { Search } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { columns } from './Columns';
import { DataTable } from './DataTable';

export default function CarrierList({
    onSelect,
}: {
    onSelect: (carrier: Carrier) => void;
}) {
    const [data, setData] = useState<Carrier[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const inputRef = useRef<HTMLInputElement>(null);

    const getCarriers = useCallback((searchTerm?: string) => {
        const getData = (): Promise<Carrier[]> => {
            return axios
                .get(route('carriers.search'), {
                    params: {
                        query: searchTerm,
                        with: ['physical_location', 'billing_location'],
                    },
                })
                .then((response) => response.data);
        };

        setIsLoading(true);

        getData()
            .then((carriers) => {
                setData(carriers);
                setIsLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching carriers:', error);
                setIsLoading(false);
            });
    }, []);

    useEffect(() => {
        if (!isLoading) {
            inputRef.current?.focus();
        }
    }, [isLoading]);

    useEffect(() => {
        getCarriers();
    }, [getCarriers]);

    return (
        <div className="mx-auto flex w-full max-w-screen-2xl flex-col gap-2">
            <div className="flex gap-1">
                <Input
                    ref={inputRef}
                    className="max-w-md"
                    placeholder="Search carriers"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    onKeyDown={(e) => {
                        if (e.key === 'Enter') {
                            getCarriers(searchTerm);
                        }
                    }}
                />
                <Button onClick={() => getCarriers(searchTerm)}>
                    <Search className="h-4 w-4" />
                </Button>
            </div>
            {isLoading ? (
                <>
                    <Loading
                        className="mx-auto h-[200px] w-full"
                        text="Loading carriers..."
                    />
                </>
            ) : (
                <>
                    <DataTable
                        columns={columns}
                        data={data}
                        onSelect={onSelect}
                    />
                </>
            )}
        </div>
    );
}
