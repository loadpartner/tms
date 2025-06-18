import FacilityForm from '@/Components/CreateForms/FacilityForm';
import { ResourceSearchSelect } from '@/Components/ResourceSearchSelect';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader } from '@/Components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Customer, Facility } from '@/types';
import { useForm } from '@inertiajs/react';
import axios from 'axios';
import { ExternalLink } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';
import { Loading } from '../ui/loading';

export default function CustomerFacilities({
    customer,
}: {
    customer?: Customer;
}) {
    const [facilityModalOpen, setFacilityModalOpen] = useState(false);
    const {
        setData: setAttachFacilityData,
        post: attachFacilityPost,
        data: attachFacilityData,
    } = useForm<{ facility_id: string | null }>({
        facility_id: null,
    });

    const [facilities, setFacilities] = useState<Facility[]>([]);
    const [isLoading, setIsLoading] = useState(false);

    const detachFacility = (facility: Facility) => {
        axios
            .delete(
                route('customers.facilities.destroy', {
                    customer: customer?.id,
                    facility: facility.id,
                }),
            )
            .then(() => {
                loadFacilities();
            });
    };

    const loadFacilities = useCallback(() => {
        setIsLoading(true);
        axios
            .get(
                route('customers.facilities.index', {
                    customer: customer?.id,
                }),
            )
            .then((response) => {
                setFacilities(response.data);
                setIsLoading(false);
            });
    }, [setFacilities, setIsLoading, customer]);

    useEffect(() => {
        loadFacilities();
    }, [loadFacilities]);

    return (
        <Card>
            <CardHeader>
                <div className="flex items-center md:justify-end">
                    <Button
                        variant="default"
                        onClick={() => setFacilityModalOpen(true)}
                    >
                        Attach Facility
                    </Button>
                </div>
            </CardHeader>
            <CardContent className="p-0 pt-2">
                <div className="flex flex-col">
                    {isLoading ? (
                        <Loading
                            className="mx-auto h-[200px] w-full"
                            text="Loading..."
                        />
                    ) : (
                        <>
                            {facilities.length === 0 ? (
                                <p className="py-8 text-center text-muted-foreground">
                                    No facilities attached to this customer
                                </p>
                            ) : (
                                facilities.map((facility) => (
                                    <Card
                                        key={facility.id}
                                        className="m-0 flex items-center justify-between p-2 md:p-4"
                                    >
                                        <div className="flex flex-col gap-2">
                                            <span>{facility.name}</span>
                                            <span className="text-sm text-muted-foreground">
                                                {
                                                    facility.location
                                                        ?.address_city
                                                }
                                                ,&nbsp;
                                                {
                                                    facility.location
                                                        ?.address_state
                                                }
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <Button
                                                variant="outline"
                                                onClick={() => {
                                                    window.open(
                                                        route(
                                                            'facilities.show',
                                                            {
                                                                facility:
                                                                    facility.id,
                                                            },
                                                        ),
                                                        '_blank',
                                                    );
                                                }}
                                            >
                                                View{' '}
                                                <ExternalLink className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                variant="destructive"
                                                onClick={() =>
                                                    detachFacility(facility)
                                                }
                                            >
                                                Detach
                                            </Button>
                                        </div>
                                    </Card>
                                ))
                            )}
                        </>
                    )}
                </div>
            </CardContent>

            <Dialog
                open={facilityModalOpen}
                onOpenChange={setFacilityModalOpen}
            >
                <DialogContent className="max-w-screen-sm">
                    <DialogHeader>
                        <DialogTitle>Attach Facility</DialogTitle>
                    </DialogHeader>
                    <div className="w-full max-w-full">
                        <ResourceSearchSelect
                            searchRoute={route('facilities.search')}
                            onValueChange={(value) => {
                                // Handle the facility selection
                                setAttachFacilityData({
                                    facility_id: value as string,
                                });
                            }}
                            createForm={FacilityForm}
                            defaultSelectedItems={
                                attachFacilityData.facility_id
                            }
                            allowMultiple={false}
                            className="w-full"
                        />
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setFacilityModalOpen(false)}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="default"
                            onClick={() => {
                                attachFacilityPost(
                                    route('customers.facilities.store', {
                                        customer: customer?.id,
                                    }),
                                    {
                                        onSuccess: () => {
                                            loadFacilities();

                                            // TODO - show a toast
                                            setFacilityModalOpen(false);
                                        },
                                    },
                                );
                            }}
                        >
                            Attach
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </Card>
    );
}
