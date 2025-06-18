import { useIntegrationSettings } from '@/hooks/useIntegrationSettings';
import { useToast } from '@/hooks/UseToast';
import { cn } from '@/lib/utils';
import { Location } from '@/types';
import { CreateFormResult } from '@/types/create-form';
import axios from 'axios';
import { useState } from 'react';
import AddressSearch from '../AddressSearch';
import InputError from '../InputError';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Switch } from '../ui/switch';

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
    const [latitude, setLatitude] = useState<number | null>(null);
    const [longitude, setLongitude] = useState<number | null>(null);

    const { getGoogleMapsApiKey } = useIntegrationSettings();
    const googleMapsApiKey = getGoogleMapsApiKey();

    const hasGoogleMapsKey = googleMapsApiKey && googleMapsApiKey != '';

    const [searchMode, setSearchMode] = useState(hasGoogleMapsKey);

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
            latitude: latitude,
            longitude: longitude,
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

    const fillFromAddressSearch = (searchResult: Location) => {
        setAddressLine1(searchResult.address_line_1 || '');
        setAddressLine2(searchResult.address_line_2 || '');
        setAddressCity(searchResult.address_city || '');
        setAddressState(searchResult.address_state || '');
        setAddressZipcode(searchResult.address_zipcode || '');
        if (searchResult.latitude) {
            setLatitude(searchResult.latitude);
        }
        if (searchResult.longitude) {
            setLongitude(searchResult.longitude);
        }
        if (searchResult.name && name == '') {
            setName(searchResult.name);
        }
        setErrors({});
    };

    return (
        <form
            ref={formRef}
            onSubmit={handleSubmit}
            className={cn('flex flex-col gap-2', className)}
            {...props}
        >
            <h2 className="text-lg font-medium">Create Location</h2>
            <div className="flex hidden flex-col gap-2">
                <Label htmlFor="name">Name</Label>
                <Input
                    id="name"
                    type="text"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                />
                {errors.name && <InputError message={errors.name} />}
            </div>

            {searchMode ? (
                <div className="flex flex-col gap-2">
                    <Label htmlFor="name">Search</Label>
                    <AddressSearch onAddressSelect={fillFromAddressSearch} />
                    {errors.address_line_1 && (
                        <InputError message={errors.address_line_1} />
                    )}
                    {errors.address_line_2 && (
                        <InputError message={errors.address_line_2} />
                    )}
                    {errors.address_city && (
                        <InputError message={errors.address_city} />
                    )}
                    {errors.address_state && (
                        <InputError message={errors.address_state} />
                    )}
                    {errors.address_zipcode && (
                        <InputError message={errors.address_zipcode} />
                    )}
                </div>
            ) : (
                <>
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
                </>
            )}
            {hasGoogleMapsKey && (
                <div className="mt-2 flex items-center gap-2 text-muted-foreground">
                    <Switch
                        id="search_mode_toggle"
                        onCheckedChange={(checked) => setSearchMode(!checked)}
                        checked={!searchMode}
                    />
                    <Label
                        htmlFor="search_mode_toggle"
                        className="cursor-pointer"
                    >
                        Manual
                    </Label>
                </div>
            )}
        </form>
    );
}
