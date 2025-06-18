import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Loading } from '@/Components/ui/loading';
import { Customer } from '@/types';
import axios from 'axios';
import { Search } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { columns } from './Columns';
import { DataTable } from './DataTable';

export default function CustomerList({
    onSelect,
}: {
    onSelect: (customer: Customer) => void;
}) {
    const [data, setData] = useState<Customer[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const inputRef = useRef<HTMLInputElement>(null);

    const getCustomers = useCallback((searchTerm?: string) => {
        const getData = (): Promise<Customer[]> => {
            return axios
                .get(route('customers.search'), {
                    params: {
                        query: searchTerm,
                        with: [],
                    },
                })
                .then((response) => response.data);
        };

        setIsLoading(true);

        getData()
            .then((customers) => {
                setData(customers);
                setIsLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching customers:', error);
                setIsLoading(false);
            });
    }, []);

    useEffect(() => {
        if (!isLoading) {
            inputRef.current?.focus();
        }
    }, [isLoading]);

    useEffect(() => {
        getCustomers();
    }, [getCustomers]);

    return (
        <div className="mx-auto flex w-full max-w-screen-2xl flex-col gap-2">
            <div className="flex gap-1">
                <Input
                    ref={inputRef}
                    className="max-w-md"
                    placeholder="Search customers"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    onKeyDown={(e) => {
                        if (e.key === 'Enter') {
                            getCustomers(searchTerm);
                        }
                    }}
                />
                <Button onClick={() => getCustomers(searchTerm)}>
                    <Search className="h-4 w-4" />
                </Button>
            </div>
            {isLoading ? (
                <>
                    <Loading
                        className="mx-auto h-[200px] w-full"
                        text="Loading..."
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
