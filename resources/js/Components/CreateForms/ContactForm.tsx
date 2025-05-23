import { useToast } from '@/hooks/UseToast';
import { cn } from '@/lib/utils';
import { CreateFormResult } from '@/types/create-form';
import { Contactable } from '@/types/enums';
import axios from 'axios';
import { useEffect, useState } from 'react';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '../ui/select';

export default function ContactForm({
    className,
    onCreate,
    formRef,
    showFormHeader = true,
    contactForId,
    contactForType,
    ...props
}: {
    onCreate: (data: CreateFormResult) => void;
    formRef?: React.RefObject<HTMLFormElement>;
    showFormHeader?: boolean;
    contactForId: number;
    contactForType: Contactable;
} & React.ComponentPropsWithoutRef<'form'>) {
    const [contactType, setContactType] = useState('');
    const [title, setTitle] = useState('');
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [mobilePhone, setMobilePhone] = useState('');
    const [officePhone, setOfficePhone] = useState('');
    const [officePhoneExtension, setOfficePhoneExtension] = useState('');
    const [contactTypes, setContactTypes] = useState<string[]>([]);

    const { toast } = useToast();

    useEffect(() => {
        fetch(route('contacts.types', contactForType))
            .then((response) => response.json())
            .then((data) => setContactTypes(data));
    }, [contactForType]);

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        e.stopPropagation();

        const data = {
            contact_type: contactType,
            title: title || undefined,
            name: name,
            email: email || undefined,
            mobile_phone: mobilePhone || undefined,
            office_phone: officePhone || undefined,
            office_phone_extension: officePhoneExtension || undefined,
            contact_for_id: contactForId,
            contact_for_type: contactForType,
        };

        axios
            .post(route('contacts.store'), data)
            .then((response) => {
                onCreate(response.data);
            })
            .catch((error) => {
                toast({
                    title: 'Error',
                    description: 'Failed to create contact',
                    variant: 'destructive',
                });
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
            {showFormHeader && (
                <h2 className="text-lg font-medium">Create Contact</h2>
            )}
            <div className="flex flex-col gap-2">
                <Label htmlFor="contact-type">
                    Type{' '}
                    <span className="text-xs text-gray-500">(required)</span>
                </Label>
                <Select value={contactType} onValueChange={setContactType}>
                    <SelectTrigger>
                        <SelectValue placeholder="Select type" />
                    </SelectTrigger>
                    <SelectContent>
                        {contactTypes.map((type) => (
                            <SelectItem key={type} value={type}>
                                {type[0].toUpperCase() + type.slice(1)}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>
            <div className="flex flex-col gap-2">
                <Label htmlFor="contact-name">
                    Name{' '}
                    <span className="text-xs text-gray-500">(required)</span>
                </Label>
                <Input
                    id="contact-name"
                    type="text"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                />
            </div>
            <div className="flex flex-col gap-2">
                <Label htmlFor="contact-title">Title</Label>
                <Input
                    id="contact-title"
                    type="text"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                />
            </div>
            <div className="flex flex-col gap-2">
                <Label htmlFor="contact-email">Email</Label>
                <Input
                    id="contact-email"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                />
            </div>
            <div className="flex flex-col gap-2">
                <Label htmlFor="mobile-phone">Mobile Phone</Label>
                <Input
                    id="mobile-phone"
                    type="text"
                    value={mobilePhone}
                    onChange={(e) => setMobilePhone(e.target.value)}
                />
            </div>
            <div className="grid grid-cols-3 gap-4">
                <div className="col-span-2">
                    <Label htmlFor="office-phone">Office Phone</Label>
                    <Input
                        id="office-phone"
                        type="text"
                        value={officePhone}
                        onChange={(e) => setOfficePhone(e.target.value)}
                    />
                </div>
                <div>
                    <Label htmlFor="office-phone-extension">Extension</Label>
                    <Input
                        id="office-phone-extension"
                        type="text"
                        value={officePhoneExtension}
                        onChange={(e) =>
                            setOfficePhoneExtension(e.target.value)
                        }
                    />
                </div>
            </div>
        </form>
    );
}
