import { Button } from '@/Components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { useToast } from '@/hooks/UseToast';
import { useForm } from '@inertiajs/react';
import axios from 'axios';
import { useEffect, useState } from 'react';

interface Props {
    user: {
        language_preference?: string;
    };
}

interface Language {
    code: string;
    name: string;
    native_name: string;
}

export default function UpdateLanguageForm({ user }: Props) {
    const { toast } = useToast();
    const [languages, setLanguages] = useState<Language[]>([]);
    const { data, setData, patch, processing } = useForm({
        language_preference: user.language_preference,
    });

    useEffect(() => {
        axios
            .get<Language[]>(route('languages.index'))
            .then((response) => {
                setLanguages(response.data);
            })
            .catch((error) => {
                console.error('Failed to fetch languages:', error);
                toast({
                    variant: 'destructive',
                    description: 'Failed to load available languages.',
                });
            });
    }, [toast]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();

        patch(route('profile.language'), {
            onSuccess: () => {
                toast({
                    description: 'Language preference updated successfully.',
                });
            },
        });
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div>
                <label className="text-sm font-medium">Language</label>
                <Select
                    value={data.language_preference}
                    onValueChange={(value) =>
                        setData('language_preference', value)
                    }
                >
                    <SelectTrigger>
                        <SelectValue />
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

            <div className="flex items-center gap-4">
                <Button disabled={processing}>Save</Button>
            </div>
        </form>
    );
}
