import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Timezone } from '@/types';
import { Transition } from '@headlessui/react';
import { Link, useForm, usePage } from '@inertiajs/react';
import axios from 'axios';
import { FormEventHandler, useCallback, useEffect, useState } from 'react';
import { useDropzone } from 'react-dropzone';

interface Language {
    code: string;
    name: string;
    native_name: string;
}

export default function UpdateProfileInformation({
    mustVerifyEmail,
    status,
    className = '',
}: {
    mustVerifyEmail: boolean;
    status?: string;
    className?: string;
}) {
    const user = usePage().props.auth.user;

    const [timezones, setTimezones] = useState<Timezone[]>(
        user?.timezone ? [{ id: 0, name: user?.timezone }] : [],
    );
    const [languages, setLanguages] = useState<Language[]>([]);
    const [selectedLanguage, setSelectedLanguage] = useState<string>(
        user.language_preference || 'en',
    );

    const { data, setData, patch, errors, processing, recentlySuccessful } =
        useForm({
            name: user.name,
            email: user.email,
            photo: null as File | null,
            removePhoto: false as boolean,
            timezone: user.timezone,
            language_preference: selectedLanguage,
        });

    const [photoPreview, setPhotoPreview] = useState<string | null>(
        user.profile_photo_url || null,
    );

    useEffect(() => {
        fetch(route('timezones.search'), {
            method: 'GET',
            headers: {
                Accept: 'application/json',
            },
        })
            .then((res) => res.json())
            .then((data: Timezone[]) => {
                setTimezones(data);
            });

        axios
            .get<Language[]>(route('languages.index'))
            .then((response) => {
                setLanguages(response.data);
                setSelectedLanguage(user.language_preference || 'en');
            })
            .catch((error) => {
                console.error('Failed to fetch languages:', error);
            });
    }, [user.language_preference]);

    const onDrop = useCallback(
        (acceptedFiles: File[]) => {
            const file = acceptedFiles[0];
            if (file) {
                setData('removePhoto', false);
                setData('photo', file);
                const reader = new FileReader();
                reader.onload = (e) => {
                    setPhotoPreview(e.target?.result as string);
                };
                reader.readAsDataURL(file);
            }
        },
        [setData],
    );

    const { getRootProps, getInputProps, isDragActive } = useDropzone({
        onDrop,
        accept: {
            'image/*': ['.jpeg', '.jpg', '.png', '.gif'],
        },
        maxFiles: 1,
        maxSize: 2097152, // 2MB
    });

    const removePhoto = () => {
        setData('removePhoto', true);
        setData('photo', null);
        setPhotoPreview(null);
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(route('profile.update'), {});
    };

    const handleLanguageChange = (value: string) => {
        setSelectedLanguage(value);
        setData('language_preference', value);
    };

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Profile Information
                </h2>

                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update your account's profile information and email address.
                </p>
            </header>

            <form onSubmit={submit} className="mt-6 space-y-6">
                <div>
                    <Label htmlFor="photo">Picture</Label>

                    <div className="mt-2 flex items-center gap-6">
                        {photoPreview ? (
                            <div className="flex flex-col items-center gap-2">
                                <Avatar className="h-20 w-20">
                                    <AvatarImage
                                        src={photoPreview}
                                        alt={user.name}
                                    />
                                    <AvatarFallback>
                                        {user.name
                                            .split(' ')
                                            .map((n) => n[0])
                                            .join('')
                                            .toUpperCase()}
                                    </AvatarFallback>
                                </Avatar>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={removePhoto}
                                >
                                    Remove Photo
                                </Button>
                            </div>
                        ) : (
                            <Avatar className="h-20 w-20">
                                <AvatarFallback>
                                    {user.name
                                        .split(' ')
                                        .map((n) => n[0])
                                        .join('')
                                        .toUpperCase()}
                                </AvatarFallback>
                            </Avatar>
                        )}

                        <div
                            {...getRootProps()}
                            className={`flex cursor-pointer flex-col items-center justify-center rounded-md border-2 border-dashed p-6 ${
                                isDragActive
                                    ? 'border-primary bg-primary/10'
                                    : 'border-gray-300 dark:border-gray-700'
                            }`}
                        >
                            <input {...getInputProps()} id="photo" />
                            <div className="text-center">
                                <p className="text-sm text-gray-600 dark:text-gray-400">
                                    {isDragActive
                                        ? 'Drop the file here'
                                        : 'Drag & drop a profile picture, or click to select'}
                                </p>
                                <p className="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                    PNG, JPG, GIF up to 2MB
                                </p>
                            </div>
                        </div>
                    </div>

                    <InputError className="mt-2" message={errors.photo} />
                </div>

                <div>
                    <Label htmlFor="name">Name</Label>

                    <Input
                        id="name"
                        className="mt-1 block w-full"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        required
                        autoFocus={true}
                        autoComplete="name"
                    />

                    <InputError className="mt-2" message={errors.name} />
                </div>

                <div>
                    <Label htmlFor="email">Email</Label>

                    <Input
                        id="email"
                        type="email"
                        className="mt-1 block w-full"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        required
                        autoComplete="username"
                    />

                    <InputError className="mt-2" message={errors.email} />
                </div>

                <div>
                    <Label htmlFor="timezone">Timezone</Label>
                    <Select
                        defaultValue={user.timezone}
                        onValueChange={(value) => setData('timezone', value)}
                    >
                        <SelectTrigger>
                            <SelectValue placeholder="Select a timezone" />
                        </SelectTrigger>
                        <SelectContent>
                            {timezones.map((timezone) => (
                                <SelectItem
                                    key={timezone.name}
                                    value={timezone.name}
                                >
                                    {timezone.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <div>
                    <Label htmlFor="language">Language</Label>
                    <Select
                        value={selectedLanguage}
                        onValueChange={handleLanguageChange}
                    >
                        <SelectTrigger>
                            <SelectValue>
                                {languages.find(
                                    (lang) => lang.code === selectedLanguage,
                                )?.native_name || 'Select a language'}
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            {languages.map((language) => (
                                <SelectItem
                                    key={language.code}
                                    value={language.code}
                                >
                                    {language.native_name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                {mustVerifyEmail && user.email_verified_at === null && (
                    <div>
                        <p className="mt-2 text-sm text-gray-800 dark:text-gray-200">
                            Your email address is unverified.
                            <Link
                                href={route('verification.send')}
                                method="post"
                                as="button"
                                className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                            >
                                Click here to re-send the verification email.
                            </Link>
                        </p>

                        {status === 'verification-link-sent' && (
                            <div className="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                                A new verification link has been sent to your
                                email address.
                            </div>
                        )}
                    </div>
                )}

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing}>Save</PrimaryButton>

                    <Transition
                        show={recentlySuccessful}
                        enter="transition ease-in-out"
                        enterFrom="opacity-0"
                        leave="transition ease-in-out"
                        leaveTo="opacity-0"
                    >
                        <p className="text-sm text-gray-600 dark:text-gray-400">
                            Saved.
                        </p>
                    </Transition>
                </div>
            </form>
        </section>
    );
}
