import { ResourceSearchSelect } from '@/Components/ResourceSearchSelect';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Checkbox } from '@/Components/ui/checkbox';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/Components/ui/form';
import { Input } from '@/Components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { cn } from '@/lib/utils';
import { TrailerSize, TrailerType } from '@/types';
import { StopType } from '@/types/enums';
import { zodResolver } from '@hookform/resolvers/zod';
import { Head, router, usePage } from '@inertiajs/react';
import { ArrowDown, ArrowUp, Trash } from 'lucide-react';
import { useForm } from 'react-hook-form';

import FacilityForm from '@/Components/CreateForms/FacilityForm';
import { z } from 'zod';

export default function Create({
    trailerTypes,
    trailerSizes,
}: {
    trailerTypes: TrailerType[];
    trailerSizes: TrailerSize[];
}) {
    const { errors } = usePage().props;

    const formSchema = z.object({
        shipment_number: z.string().optional(),
        customer_ids: z.array(z.string()),
        carrier_id: z.string().optional(),

        weight: z.string().optional(),
        trip_distance: z.string().optional(),

        trailer_type_id: z.string().optional(),
        trailer_size_id: z.string().optional(),
        trailer_temperature_range: z.boolean().nullable(),
        trailer_temperature: z.string().optional(),
        trailer_temperature_maximum: z.string().nullable().optional(),

        stops: z
            .array(
                z.object({
                    facility_id: z.string(),
                    stop_type: z.enum(['pickup', 'delivery']),
                    appointment_at: z.string().refine((val) => {
                        // Accept any valid date string that can be parsed
                        const date = new Date(val);
                        return !isNaN(date.getTime());
                    }, 'Please enter a valid date and time'),
                    special_instructions: z.string().nullable(),
                    reference_numbers: z.string().nullable(),
                    stop_number: z.number().int(),
                }),
            )
            .min(2),
    });

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            trailer_type_id: undefined,
            trailer_size_id: undefined,
            trailer_temperature_range: false,
            trailer_temperature: undefined,
            trailer_temperature_maximum: undefined,

            weight: undefined,
            trip_distance: undefined,

            stops: [
                {
                    stop_type: StopType.Pickup,
                    appointment_at: '',
                    special_instructions: '',
                    reference_numbers: '',
                    stop_number: 1,
                    facility_id: undefined,
                },
                {
                    stop_type: StopType.Delivery,
                    appointment_at: '',
                    special_instructions: '',
                    reference_numbers: '',
                    stop_number: 2,
                    facility_id: undefined,
                },
            ],
        },
    });

    // 2. Define a submit handler.
    function onSubmit(values: z.infer<typeof formSchema>) {
        router.post(route('shipments.store'), values);
    }

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Shipments',
                    href: route('shipments.index'),
                },
                {
                    title: 'Create',
                },
            ]}
        >
            <Head title="Create Shipment" />

            <Form {...form}>
                <form
                    onSubmit={form.handleSubmit(onSubmit, (errors) => {
                        console.log(errors);
                        console.log(form.getValues());
                    })}
                    className="mx-auto max-w-screen-2xl space-y-2 pb-8 md:space-y-8 md:px-8"
                >
                    <Card>
                        <CardHeader>
                            <CardTitle>General</CardTitle>
                        </CardHeader>
                        <CardContent className="flex flex-wrap justify-evenly gap-4">
                            <div>
                                <FormField
                                    control={form.control}
                                    name={`shipment_number`}
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>
                                                Shipment Number
                                            </FormLabel>
                                            <FormControl>
                                                <div className="flex items-center gap-2">
                                                    <Input
                                                        className="w-fit"
                                                        {...field}
                                                        type="text"
                                                        value={
                                                            field.value ?? ''
                                                        }
                                                    />
                                                </div>
                                            </FormControl>
                                            <FormMessage>
                                                {errors[`shipment_number`]}
                                            </FormMessage>
                                        </FormItem>
                                    )}
                                />
                            </div>
                            <div className="flex flex-col flex-wrap gap-4">
                                <FormField
                                    control={form.control}
                                    name={`weight`}
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Weight</FormLabel>
                                            <FormControl>
                                                <div className="flex items-center gap-2">
                                                    <Input
                                                        className="w-fit"
                                                        {...field}
                                                        type="number"
                                                        value={
                                                            field.value ?? ''
                                                        }
                                                    />
                                                    <span>lbs</span>
                                                </div>
                                            </FormControl>
                                            <FormMessage>
                                                {errors[`weight`]}
                                            </FormMessage>
                                        </FormItem>
                                    )}
                                />
                                <FormField
                                    control={form.control}
                                    name={`trip_distance`}
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Trip Distance</FormLabel>
                                            <FormControl>
                                                <div className="flex items-center gap-2">
                                                    <Input
                                                        className="w-fit"
                                                        {...field}
                                                        type="number"
                                                        value={
                                                            field.value ?? ''
                                                        }
                                                    />
                                                    <span>miles</span>
                                                </div>
                                            </FormControl>
                                            <FormMessage>
                                                {errors[`trip_distance`]}
                                            </FormMessage>
                                        </FormItem>
                                    )}
                                />
                            </div>

                            <div className="flex flex-col gap-4">
                                <div>Temperature (F)</div>
                                <FormField
                                    control={form.control}
                                    name={`trailer_temperature_range`}
                                    render={({ field }) => (
                                        <FormItem className="gap-2">
                                            <FormControl>
                                                <Checkbox
                                                    {...field}
                                                    value={field.name}
                                                    checked={
                                                        field.value ?? false
                                                    }
                                                    onCheckedChange={
                                                        field.onChange
                                                    }
                                                />
                                            </FormControl>
                                            <FormLabel className="ml-2">
                                                Range
                                            </FormLabel>
                                            <FormMessage>
                                                {
                                                    errors[
                                                        `trailer_temperature_range`
                                                    ]
                                                }
                                            </FormMessage>
                                        </FormItem>
                                    )}
                                />
                                <div className="flex flex-wrap gap-2">
                                    <FormField
                                        control={form.control}
                                        name={`trailer_temperature`}
                                        render={({ field }) => (
                                            <FormItem>
                                                <FormLabel>
                                                    {form.watch(
                                                        'trailer_temperature_range',
                                                    )
                                                        ? 'Min'
                                                        : 'Target'}
                                                </FormLabel>
                                                <FormControl>
                                                    <Input
                                                        className="w-fit"
                                                        {...field}
                                                        type="number"
                                                        value={
                                                            field.value ?? ''
                                                        }
                                                    />
                                                </FormControl>
                                                <FormMessage>
                                                    {
                                                        errors[
                                                            `trailer_temperature`
                                                        ]
                                                    }
                                                </FormMessage>
                                            </FormItem>
                                        )}
                                    />
                                    <FormField
                                        control={form.control}
                                        name={`trailer_temperature_maximum`}
                                        render={({ field }) => (
                                            <FormItem
                                                className={cn(
                                                    form.watch(
                                                        'trailer_temperature_range',
                                                    )
                                                        ? ''
                                                        : 'invisible',
                                                )}
                                            >
                                                <FormLabel>Max</FormLabel>
                                                <FormControl>
                                                    <Input
                                                        className="w-fit"
                                                        {...field}
                                                        type="number"
                                                        value={
                                                            field.value ?? ''
                                                        }
                                                    />
                                                </FormControl>
                                                <FormMessage>
                                                    {
                                                        errors[
                                                            `trailer_temperature_maximum`
                                                        ]
                                                    }
                                                </FormMessage>
                                            </FormItem>
                                        )}
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Customers</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <FormField
                                control={form.control}
                                name={`customer_ids`}
                                render={({ field }) => (
                                    <FormItem>
                                        <FormControl>
                                            <ResourceSearchSelect
                                                className="w-full"
                                                searchRoute={route(
                                                    'customers.search',
                                                )}
                                                onValueChange={field.onChange}
                                                allowMultiple={true}
                                                defaultSelectedItems={
                                                    field.value
                                                }
                                            />
                                        </FormControl>
                                        <FormMessage>
                                            {errors.customer_ids}
                                        </FormMessage>
                                    </FormItem>
                                )}
                            />
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Carrier</CardTitle>
                        </CardHeader>
                        <CardContent className="flex flex-col flex-wrap gap-4">
                            <FormField
                                control={form.control}
                                name={`carrier_id`}
                                render={({ field }) => (
                                    <FormItem className="flex flex-grow flex-col">
                                        <FormLabel>Carrier</FormLabel>
                                        <FormControl>
                                            <ResourceSearchSelect
                                                className="w-full"
                                                searchRoute={route(
                                                    'carriers.search',
                                                )}
                                                onValueChange={field.onChange}
                                                allowMultiple={false}
                                                defaultSelectedItems={
                                                    field.value
                                                }
                                            />
                                        </FormControl>
                                        <FormMessage>
                                            {errors.carrier_id}
                                        </FormMessage>
                                    </FormItem>
                                )}
                            />
                            <div className="flex flex-row space-x-2">
                                <FormField
                                    control={form.control}
                                    name={`trailer_size_id`}
                                    render={({ field }) => (
                                        <FormItem className="flex flex-col">
                                            <FormLabel>Trailer</FormLabel>
                                            <FormControl>
                                                <div className="flex flex-grow">
                                                    <Select
                                                        onValueChange={
                                                            field.onChange
                                                        }
                                                        value={
                                                            field.value ??
                                                            undefined
                                                        }
                                                    >
                                                        <SelectTrigger className="w-fit min-w-[100px]">
                                                            <SelectValue placeholder="Select ..." />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {trailerSizes.map(
                                                                (
                                                                    trailerSize,
                                                                ) => (
                                                                    <SelectItem
                                                                        key={
                                                                            trailerSize.id
                                                                        }
                                                                        value={trailerSize.id.toString()}
                                                                    >
                                                                        {
                                                                            trailerSize.name
                                                                        }
                                                                    </SelectItem>
                                                                ),
                                                            )}
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                            </FormControl>
                                            <FormMessage>
                                                {errors.trailer_size_id}
                                            </FormMessage>
                                        </FormItem>
                                    )}
                                />

                                <FormField
                                    control={form.control}
                                    name={`trailer_type_id`}
                                    render={({ field }) => (
                                        <FormItem className="flex flex-col">
                                            <FormLabel className="invisible">
                                                Trailer Type
                                            </FormLabel>
                                            <FormControl>
                                                <div className="flex flex-grow">
                                                    <Select
                                                        onValueChange={
                                                            field.onChange
                                                        }
                                                        value={
                                                            field.value ??
                                                            undefined
                                                        }
                                                    >
                                                        <SelectTrigger className="w-fit min-w-[100px]">
                                                            <SelectValue placeholder="Select ..." />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {trailerTypes.map(
                                                                (
                                                                    trailerType,
                                                                ) => (
                                                                    <SelectItem
                                                                        key={
                                                                            trailerType.id
                                                                        }
                                                                        value={trailerType.id.toString()}
                                                                    >
                                                                        {
                                                                            trailerType.name
                                                                        }
                                                                    </SelectItem>
                                                                ),
                                                            )}
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                            </FormControl>
                                            <FormMessage>
                                                {errors.trailer_type_id}
                                            </FormMessage>
                                        </FormItem>
                                    )}
                                />
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>
                                <div className="flex items-center justify-between">
                                    <span>Stops</span>
                                    <Button
                                        type="button"
                                        variant="default"
                                        onClick={() =>
                                            form.setValue('stops', [
                                                ...form.getValues('stops'),
                                                {
                                                    stop_type: StopType.Pickup,
                                                    appointment_at: '',
                                                    special_instructions: '',
                                                    reference_numbers: '',
                                                    stop_number:
                                                        form.getValues('stops')
                                                            .length + 1,
                                                    facility_id: '0',
                                                },
                                            ])
                                        }
                                    >
                                        Add Stop
                                    </Button>
                                </div>
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-8">
                            {form
                                .watch('stops')
                                .map(
                                    (
                                        stop: z.infer<
                                            typeof formSchema.shape.stops
                                        >[number],
                                        index: number,
                                    ) => (
                                        <div
                                            className="flex items-center justify-between space-x-2"
                                            key={'stops-div-' + index}
                                        >
                                            <div className="flex h-full flex-shrink-0 flex-col justify-between gap-2">
                                                <Button
                                                    variant="secondary"
                                                    size="icon"
                                                    onClick={(e) => {
                                                        e.preventDefault();
                                                        const stops = [
                                                            ...form.getValues(
                                                                'stops',
                                                            ),
                                                        ];
                                                        const temp =
                                                            stops[index];
                                                        stops[index] =
                                                            stops[index - 1];
                                                        stops[index - 1] = temp;

                                                        stops.forEach(
                                                            (stop, i) => {
                                                                stop.stop_number =
                                                                    i + 1;
                                                            },
                                                        );
                                                        form.setValue(
                                                            'stops',
                                                            stops,
                                                            {
                                                                shouldValidate:
                                                                    false,
                                                                shouldTouch:
                                                                    false,
                                                            },
                                                        );
                                                    }}
                                                    disabled={index === 0}
                                                >
                                                    <ArrowUp className="h-4 w-4" />
                                                </Button>
                                                <span className="text-center text-sm font-bold">
                                                    {stop.stop_number}
                                                </span>
                                                <Button
                                                    variant="secondary"
                                                    size="icon"
                                                    onClick={(e) => {
                                                        e.preventDefault();
                                                        const stops = [
                                                            ...form.getValues(
                                                                'stops',
                                                            ),
                                                        ];
                                                        const temp =
                                                            stops[index];
                                                        stops[index] =
                                                            stops[index + 1];
                                                        stops[index + 1] = temp;

                                                        stops.forEach(
                                                            (stop, i) => {
                                                                stop.stop_number =
                                                                    i + 1;
                                                            },
                                                        );

                                                        form.setValue(
                                                            'stops',
                                                            stops,
                                                            {
                                                                shouldValidate:
                                                                    false,
                                                                shouldTouch:
                                                                    false,
                                                            },
                                                        );
                                                    }}
                                                    disabled={
                                                        index ===
                                                        form.getValues('stops')
                                                            .length -
                                                            1
                                                    }
                                                >
                                                    <ArrowDown className="h-4 w-4" />
                                                </Button>
                                            </div>
                                            <div
                                                key={'stop' + index}
                                                className="flex flex-grow flex-col space-y-4 border-l-2 border-primary py-2 pl-2 md:border-l-4 md:pl-4"
                                            >
                                                <FormField
                                                    key={index}
                                                    control={form.control}
                                                    name={`stops.${index}.stop_type`}
                                                    render={({ field }) => (
                                                        <FormItem className="flex flex-row">
                                                            <FormControl>
                                                                <div className="flex flex-grow">
                                                                    <Select
                                                                        onValueChange={
                                                                            field.onChange
                                                                        }
                                                                        value={
                                                                            field.value
                                                                        }
                                                                    >
                                                                        <SelectTrigger className="w-fit min-w-[100px]">
                                                                            <SelectValue placeholder="Select a stop type" />
                                                                        </SelectTrigger>
                                                                        <SelectContent>
                                                                            <SelectItem
                                                                                value={
                                                                                    StopType.Pickup
                                                                                }
                                                                            >
                                                                                Pickup
                                                                            </SelectItem>
                                                                            <SelectItem
                                                                                value={
                                                                                    StopType.Delivery
                                                                                }
                                                                            >
                                                                                Delivery
                                                                            </SelectItem>
                                                                        </SelectContent>
                                                                    </Select>
                                                                </div>
                                                            </FormControl>
                                                            <FormLabel className="">
                                                                <Button
                                                                    disabled={
                                                                        form.getValues(
                                                                            'stops',
                                                                        )
                                                                            .length <=
                                                                        2
                                                                    }
                                                                    type="button"
                                                                    variant="ghost"
                                                                    onClick={() => {
                                                                        const stops =
                                                                            [
                                                                                ...form.getValues(
                                                                                    'stops',
                                                                                ),
                                                                            ];
                                                                        stops.splice(
                                                                            index,
                                                                            1,
                                                                        );

                                                                        stops.forEach(
                                                                            (
                                                                                stop,
                                                                                i,
                                                                            ) => {
                                                                                stop.stop_number =
                                                                                    i +
                                                                                    1;
                                                                            },
                                                                        );

                                                                        form.setValue(
                                                                            'stops',
                                                                            stops,
                                                                        );
                                                                    }}
                                                                >
                                                                    Remove
                                                                    <Trash className="h-4 w-4" />
                                                                </Button>
                                                            </FormLabel>
                                                            <FormMessage>
                                                                {
                                                                    errors[
                                                                        `stops.${index}.stop_type`
                                                                    ]
                                                                }
                                                            </FormMessage>
                                                        </FormItem>
                                                    )}
                                                />
                                                <FormField
                                                    control={form.control}
                                                    name={`stops.${index}.facility_id`}
                                                    render={({ field }) => (
                                                        <FormItem className="flex flex-col">
                                                            <FormLabel>
                                                                Facility
                                                            </FormLabel>
                                                            <FormControl>
                                                                <ResourceSearchSelect
                                                                    searchRoute={route(
                                                                        'facilities.search',
                                                                    )}
                                                                    createForm={
                                                                        FacilityForm
                                                                    }
                                                                    allowMultiple={
                                                                        false
                                                                    }
                                                                    onValueChange={
                                                                        field.onChange
                                                                    }
                                                                    defaultSelectedItems={
                                                                        field.value
                                                                    }
                                                                    className="w-full"
                                                                />
                                                            </FormControl>
                                                            <FormMessage>
                                                                {
                                                                    errors[
                                                                        `stops.${index}.facility_id`
                                                                    ]
                                                                }
                                                            </FormMessage>
                                                        </FormItem>
                                                    )}
                                                />
                                                <FormField
                                                    control={form.control}
                                                    name={`stops.${index}.appointment_at`}
                                                    render={({ field }) => (
                                                        <FormItem>
                                                            <FormLabel>
                                                                Appointment Date
                                                            </FormLabel>
                                                            <FormControl>
                                                                <Input
                                                                    type="datetime-local"
                                                                    {...field}
                                                                />
                                                            </FormControl>
                                                            <FormMessage>
                                                                {
                                                                    errors[
                                                                        `stops.${index}.appointment_at`
                                                                    ]
                                                                }
                                                            </FormMessage>
                                                        </FormItem>
                                                    )}
                                                />

                                                <FormField
                                                    control={form.control}
                                                    name={`stops.${index}.special_instructions`}
                                                    render={({ field }) => (
                                                        <FormItem>
                                                            <FormLabel>
                                                                Special
                                                                Instructions
                                                            </FormLabel>
                                                            <FormControl>
                                                                <Textarea
                                                                    {...field}
                                                                    value={
                                                                        field.value ??
                                                                        ''
                                                                    }
                                                                />
                                                            </FormControl>
                                                            <FormMessage>
                                                                {
                                                                    errors[
                                                                        `stops.${index}.special_instructions`
                                                                    ]
                                                                }
                                                            </FormMessage>
                                                        </FormItem>
                                                    )}
                                                />
                                                <FormField
                                                    control={form.control}
                                                    name={`stops.${index}.reference_numbers`}
                                                    render={({ field }) => (
                                                        <FormItem>
                                                            <FormLabel>
                                                                Reference
                                                                Numbers
                                                            </FormLabel>
                                                            <FormControl>
                                                                <Textarea
                                                                    {...field}
                                                                    value={
                                                                        field.value ??
                                                                        ''
                                                                    }
                                                                />
                                                            </FormControl>
                                                            <FormMessage>
                                                                {
                                                                    errors[
                                                                        `stops.${index}.reference_numbers`
                                                                    ]
                                                                }
                                                            </FormMessage>
                                                        </FormItem>
                                                    )}
                                                />
                                            </div>
                                        </div>
                                    ),
                                )}
                        </CardContent>
                    </Card>

                    <div className="flex justify-center md:justify-start">
                        <Button type="submit">Create Shipment</Button>
                    </div>
                </form>
            </Form>
        </AuthenticatedLayout>
    );
}
