import { useToast } from '@/hooks/UseToast';
import { cn } from '@/lib/utils';
import { CreateFormResult } from '@/types/create-form';
import axios from 'axios';
import { useState } from 'react';
import InputError from '../InputError';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
export default function LocationForm({
    className,
    onCreate,
    formRef,
    ...props
}: {
    onCreate: (data: CreateFormResult) => void;
    formRef?: React.RefObject<HTMLFormElement>;
} & React.ComponentPropsWithoutRef<'form'>) {
    const [name, setName] = useState('');
    const [addressLine1, setAddressLine1] = useState('');
    const [addressLine2, setAddressLine2] = useState('');
    const [addressCity, setAddressCity] = useState('');
    const [addressState, setAddressState] = useState('');
    const [addressZipcode, setAddressZipcode] = useState('');

    const [errors, setErrors] = useState<Record<string, string>>({});

    const { toast } = useToast();

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        e.stopPropagation();

        const data = {
            name: name,
            address_line_1: addressLine1,
            address_line_2: addressLine2,
            address_city: addressCity,
            address_state: addressState,
            address_zipcode: addressZipcode,
        };

        axios
            .post(route('locations.store'), data)
            .then((response) => {
                onCreate(response.data);
            })
            .catch((error) => {
                toast({
                    title: 'Error',
                    description: 'Failed to create location',
                    variant: 'destructive',
                });
                setErrors(error.response.data.errors);
                console.error(error);
            });
    };

    return (
        <form
            ref={formRef}
            onSubmit={handleSubmit}
            className={cn('flex flex-col gap-2', className)}
            {...props}
        >
            <h2 className="text-lg font-medium">Create Location</h2>
            <div className="flex flex-col gap-2">
                <Label htmlFor="name">Name</Label>
                <Input
                    id="name"
                    type="text"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                />
                {errors.name && <InputError message={errors.name} />}
            </div>
            <div className="flex flex-col gap-2">
                <Label htmlFor="address_line_1">Address Line 1</Label>
                <Input
                    id="address_line_1"
                    type="text"
                    value={addressLine1}
                    onChange={(e) => setAddressLine1(e.target.value)}
                />
                {errors.address_line_1 && (
                    <InputError message={errors.address_line_1} />
                )}
            </div>
            <div className="flex flex-col gap-2">
                <Label htmlFor="address_line_2">Address Line 2</Label>
                <Input
                    id="address_line_2"
                    type="text"
                    value={addressLine2}
                    onChange={(e) => setAddressLine2(e.target.value)}
                />
                {errors.address_line_2 && (
                    <InputError message={errors.address_line_2} />
                )}
            </div>
            <div className="flex flex-col gap-2">
                <Label htmlFor="address_city">City</Label>
                <Input
                    id="address_city"
                    type="text"
                    value={addressCity}
                    onChange={(e) => setAddressCity(e.target.value)}
                />
                {errors.address_city && (
                    <InputError message={errors.address_city} />
                )}
            </div>
            <div className="flex flex-col gap-2">
                <Label htmlFor="address_state">State</Label>
                <Input
                    id="address_state"
                    type="text"
                    value={addressState}
                    onChange={(e) => setAddressState(e.target.value)}
                />
                {errors.address_state && (
                    <InputError message={errors.address_state} />
                )}
            </div>
            <div className="flex flex-col gap-2">
                <Label htmlFor="address_zipcode">Zipcode</Label>
                <Input
                    id="address_zipcode"
                    type="text"
                    value={addressZipcode}
                    onChange={(e) => setAddressZipcode(e.target.value)}
                />
                {errors.address_zipcode && (
                    <InputError message={errors.address_zipcode} />
                )}
            </div>
        </form>
    );
}
