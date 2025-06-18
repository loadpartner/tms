import { Input } from '@/Components/ui/input';

import {
    Dialog,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';

import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { DialogContent } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';
import { Loading } from '@/Components/ui/loading';
import { CarrierSaferReport } from '@/types';
import { router } from '@inertiajs/react';
import axios from 'axios';
import { ArrowLeft, ArrowRight, Search } from 'lucide-react';
import { useState } from 'react';

function CarrierManualCreateForm({
    setIsOpen,
    setFormState,
    allowFmcsaSearch,
}: {
    setIsOpen: (isOpen: boolean) => void;
    setFormState: (formState: 'manual' | 'fmcsa') => void;
    allowFmcsaSearch: boolean;
}) {
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});
        router.post(
            route('carriers.store'),
            {
                name: carrierName,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsOpen(false);
                    setCarrierName('');
                },
                onError: (errors) => {
                    setErrors(errors);
                },
            },
        );
    };

    const [carrierName, setCarrierName] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <Input
                placeholder="Carrier name"
                value={carrierName}
                onChange={(e) => setCarrierName(e.target.value)}
            />
            {errors.name && <InputError message={errors.name} />}
            <DialogFooter>
                {allowFmcsaSearch && (
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => setFormState('fmcsa')}
                    >
                        <ArrowLeft className="inline" /> Back to search
                    </Button>
                )}
                <Button type="submit">Create</Button>
            </DialogFooter>
        </form>
    );
}

function CarrierFmcsaCreateForm({
    setIsOpen,
    setFormState,
}: {
    setIsOpen: (isOpen: boolean) => void;
    setFormState: (formState: 'manual' | 'fmcsa') => void;
}) {
    const [carrierName, setCarrierName] = useState('');
    const [carrierDotNumber, setCarrierDotNumber] = useState('');

    const [possibleCarriers, setPossibleCarriers] = useState<
        CarrierSaferReport[]
    >([]);

    const [showCarrierSelectList, setShowCarrierSelectList] = useState(false);

    const [isLoading, setIsLoading] = useState(false);

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setIsLoading(true);
        setShowCarrierSelectList(true);

        if (carrierDotNumber) {
            axios
                .get(route('carriers.fmcsa.lookup.dot'), {
                    params: {
                        dotNumber: carrierDotNumber,
                    },
                })
                .then((response) => {
                    setPossibleCarriers([response.data]);
                })
                .finally(() => {
                    setIsLoading(false);
                });
        } else {
            axios
                .get(route('carriers.fmcsa.lookup.name'), {
                    params: {
                        name: carrierName,
                    },
                })
                .then((response) => {
                    setPossibleCarriers(response.data);
                })
                .finally(() => {
                    setIsLoading(false);
                });
        }
    };
    return (
        <>
            {!showCarrierSelectList && (
                <form onSubmit={handleSearch} className="space-y-4">
                    <div className="space-y-2">
                        <Label>Carrier Name</Label>
                        <Input
                            placeholder="Carrier name"
                            value={carrierName}
                            onChange={(e) => setCarrierName(e.target.value)}
                        />
                    </div>
                    <div className="space-y-2">
                        <Label>DOT Number</Label>
                        <Input
                            placeholder="#######"
                            value={carrierDotNumber}
                            onChange={(e) =>
                                setCarrierDotNumber(e.target.value)
                            }
                        />
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => setIsOpen(false)}
                        >
                            Cancel
                        </Button>
                        <Button type="submit">
                            <Search className="inline" /> Search
                        </Button>
                    </DialogFooter>
                </form>
            )}

            {showCarrierSelectList && (
                <div className="space-y-2">
                    <div className="max-h-[500px] space-y-2 overflow-y-auto">
                        {isLoading && (
                            <Loading
                                className="mx-auto h-[200px] w-full"
                                text="Loading..."
                            />
                        )}
                        {!isLoading && possibleCarriers.length < 1 && (
                            <p className="text-center text-muted-foreground">
                                No carriers found
                            </p>
                        )}
                        {!isLoading &&
                            possibleCarriers.map((carrier) => (
                                <Card key={carrier.id} className="py-0">
                                    <CardHeader className="md:p-4">
                                        <CardTitle>
                                            {
                                                carrier.report.general?.carrier
                                                    ?.legalName
                                            }
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="flex justify-between px-4">
                                        <div className="flex-grow space-y-2">
                                            <div className="text-sm">
                                                <p>
                                                    {
                                                        carrier.report?.general
                                                            ?.carrier?.phyStreet
                                                    }
                                                </p>
                                                <p>
                                                    {
                                                        carrier.report?.general
                                                            ?.carrier?.phyCity
                                                    }
                                                    ,
                                                    {
                                                        carrier.report?.general
                                                            ?.carrier?.phyState
                                                    }{' '}
                                                    {
                                                        carrier.report?.general
                                                            ?.carrier
                                                            ?.phyZipcode
                                                    }
                                                </p>
                                            </div>
                                            <p className="text-sm">
                                                <span className="mr-2 inline text-muted-foreground">
                                                    DOT:
                                                </span>
                                                {carrier.dot_number}
                                            </p>
                                        </div>
                                        <div className="items-end">
                                            {carrier.carrier ? (
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    onClick={() =>
                                                        router.get(
                                                            route(
                                                                'carriers.show',
                                                                {
                                                                    carrier:
                                                                        carrier
                                                                            .carrier
                                                                            ?.id,
                                                                },
                                                            ),
                                                        )
                                                    }
                                                >
                                                    View{' '}
                                                    <ArrowRight className="inline" />
                                                </Button>
                                            ) : (
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    onClick={() =>
                                                        router.post(
                                                            route(
                                                                'carriers.fmcsa.store',
                                                                {
                                                                    carrierSaferReport:
                                                                        carrier.id,
                                                                },
                                                            ),
                                                        )
                                                    }
                                                >
                                                    Create{' '}
                                                    <ArrowRight className="inline" />{' '}
                                                </Button>
                                            )}
                                        </div>
                                    </CardContent>
                                </Card>
                            ))}
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            disabled={isLoading}
                            onClick={() => setFormState('manual')}
                        >
                            Create Manually
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            disabled={isLoading}
                            onClick={() => setShowCarrierSelectList(false)}
                        >
                            Search again <Search className="inline" />
                        </Button>
                    </DialogFooter>
                </div>
            )}
        </>
    );
}

export default function CarrierCreateDialog({
    isOpen,
    setIsOpen,
    allowFmcsaSearch,
}: {
    isOpen: boolean;
    setIsOpen: (isOpen: boolean) => void;
    allowFmcsaSearch: boolean;
}) {
    const [formState, setFormState] = useState<'manual' | 'fmcsa'>(
        allowFmcsaSearch ? 'fmcsa' : 'manual',
    );

    const changeFormStateCheckForSearch = (formState: 'manual' | 'fmcsa') => {
        if (allowFmcsaSearch) {
            setFormState(formState);
        } else {
            setFormState('manual');
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={setIsOpen}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Create Carrier</DialogTitle>
                </DialogHeader>
                {formState === 'manual' && (
                    <CarrierManualCreateForm
                        setIsOpen={setIsOpen}
                        setFormState={changeFormStateCheckForSearch}
                        allowFmcsaSearch={allowFmcsaSearch}
                    />
                )}
                {formState === 'fmcsa' && (
                    <CarrierFmcsaCreateForm
                        setIsOpen={setIsOpen}
                        setFormState={changeFormStateCheckForSearch}
                    />
                )}
            </DialogContent>
        </Dialog>
    );
}
