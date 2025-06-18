import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ConfirmButton } from '@/Components/ui/confirm-button';
import { Input } from '@/Components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
} from '@/Components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { toast } from '@/hooks/UseToast';
import { cn, formatCurrency, getCurrencySymbol } from '@/lib/utils';
import { Shipment, ShipmentAccounting } from '@/types';
import { Currency } from '@/types/enums';
import { useForm } from '@inertiajs/react';
import {
    ArrowRight,
    Check,
    CirclePlus,
    DollarSign,
    Pencil,
    Trash,
    Truck,
    Users,
    Warehouse,
    X,
} from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';

type PayableFormData = {
    id?: number;
    payee_id: number;
    payee_type: string;
    rate: number;
    quantity: number;
    total: number;
    rate_type_id: number;
    currency_code: string;
};

// Define a type for the errors object from Inertia
type FormErrors = Record<string, string>;

export default function Payables({
    shipment,
    shipmentAccounting,
}: {
    shipment: Shipment;
    shipmentAccounting?: ShipmentAccounting;
}) {
    const [isEditing, setIsEditing] = useState<boolean>(false);

    const {
        data,
        setData,
        post,
        errors: formErrors,
    } = useForm<{
        payables: PayableFormData[];
    }>({
        payables: shipmentAccounting?.payables || [],
    });

    // Correctly type the errors object
    const errors = formErrors as unknown as FormErrors;

    const setDataRef = useRef(setData);

    useEffect(() => {
        if (!isEditing) {
            setDataRef.current({
                payables: shipmentAccounting?.payables || [],
            });
        }
    }, [shipmentAccounting, isEditing]);

    const findRateType = useCallback(
        (id: number) => {
            return shipmentAccounting?.rate_types.find(
                (rateType) => rateType.id === id,
            );
        },
        [shipmentAccounting],
    );

    const findPayee = useCallback(
        (id: number, type: string) => {
            return shipmentAccounting?.related_entities.find(
                (entity) => entity.id === id && entity.alias_name === type,
            );
        },
        [shipmentAccounting],
    );

    const payeeUniqueIdForComponent = (id: number, type: string) => {
        return `${type}|${id}`;
    };

    const decodePayeeUniqueIdForComponent = (uniqueId: string) => {
        const [type, id] = uniqueId.split('|');
        return { type, id: parseInt(id) };
    };

    const getAliasNameIcon = (aliasName: string) => {
        if (aliasName == 'customer') {
            return Users;
        }

        if (aliasName == 'carrier') {
            return Truck;
        }

        if (aliasName == 'facility') {
            return Warehouse;
        }
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center justify-between gap-2">
                    <div className="flex items-center gap-2">
                        <div className="relative">
                            <DollarSign className="h-5 w-5" />
                            <div className="absolute -bottom-1 -right-1 rounded-full border-2 border-destructive bg-secondary">
                                <ArrowRight className="h-2 w-2 text-secondary-foreground" />
                            </div>
                        </div>
                        Payables
                    </div>

                    <div className="flex gap-2">
                        <Button
                            variant="ghost"
                            onClick={() => {
                                setData({
                                    payables: [
                                        ...data.payables,
                                        {
                                            payee_id: 0,
                                            payee_type: 'carrier',
                                            rate: 0,
                                            quantity: 0,
                                            total: 0,
                                            rate_type_id: 0,
                                            currency_code:
                                                Currency.UnitedStatesDollar,
                                        },
                                    ],
                                });
                                setIsEditing(true);
                            }}
                        >
                            <CirclePlus className="h-4 w-4" /> New
                        </Button>
                        {isEditing ? (
                            <>
                                <Button
                                    variant="ghost"
                                    onClick={() => {
                                        post(
                                            route(
                                                'shipments.accounting.payables',
                                                { shipment: shipment.id },
                                            ),
                                            {
                                                preserveScroll: true,
                                                onSuccess: () => {
                                                    setIsEditing(false);
                                                    toast({
                                                        title: 'Payables saved',
                                                    });
                                                },
                                                onError: () => {
                                                    toast({
                                                        title: 'Error saving payables',
                                                        description:
                                                            'Please check form for errors',
                                                        variant: 'destructive',
                                                    });
                                                },
                                            },
                                        );
                                    }}
                                >
                                    <Check />
                                </Button>
                                <Button
                                    variant="ghost"
                                    onClick={() => {
                                        setIsEditing(false);
                                        setData({
                                            payables:
                                                shipmentAccounting?.payables ||
                                                [],
                                        });
                                    }}
                                >
                                    <X />
                                </Button>
                            </>
                        ) : (
                            <Button
                                variant="ghost"
                                onClick={() => setIsEditing(true)}
                            >
                                <Pencil />
                                Edit
                            </Button>
                        )}
                    </div>
                </CardTitle>
            </CardHeader>
            <CardContent>
                <Table className="w-full table-fixed">
                    <TableHeader>
                        <TableRow className="table-row">
                            <TableHead className="hidden w-[30%] sm:table-cell">
                                Payee
                            </TableHead>
                            <TableHead className="hidden w-[20%] sm:table-cell">
                                Type
                            </TableHead>
                            <TableHead
                                className={cn(
                                    'hidden w-[15%] sm:table-cell',
                                    !isEditing && 'hidden',
                                )}
                            >
                                Rate
                            </TableHead>
                            <TableHead
                                className={cn(
                                    'hidden w-[12%] sm:table-cell',
                                    !isEditing && 'hidden',
                                )}
                            >
                                Quantity
                            </TableHead>
                            <TableHead className="hidden w-[18%] text-right sm:table-cell">
                                Total
                            </TableHead>
                            <TableHead className="hidden w-[5%] sm:table-cell"></TableHead>

                            {/* Mobile Header - only displayed on small screens */}
                            <TableHead className="w-full sm:hidden">
                                Details
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {data.payables.length === 0 ? (
                            <TableRow>
                                <TableCell
                                    colSpan={6}
                                    className="h-24 text-center text-muted-foreground"
                                >
                                    No payables added yet
                                </TableCell>
                            </TableRow>
                        ) : (
                            data.payables.map((payable, index) =>
                                isEditing ? (
                                    <TableRow key={index}>
                                        {/* Desktop View */}
                                        <TableCell className="hidden w-[30%] max-w-[200px] sm:table-cell">
                                            <Select
                                                value={payeeUniqueIdForComponent(
                                                    payable.payee_id,
                                                    payable.payee_type,
                                                )}
                                                onValueChange={(value) => {
                                                    const { type, id } =
                                                        decodePayeeUniqueIdForComponent(
                                                            value,
                                                        );
                                                    setData({
                                                        ...data,
                                                        payables:
                                                            data.payables.map(
                                                                (
                                                                    payable,
                                                                    payableIndex,
                                                                ) =>
                                                                    payableIndex ===
                                                                    index
                                                                        ? {
                                                                              ...payable,
                                                                              payee_id:
                                                                                  id,
                                                                              payee_type:
                                                                                  type,
                                                                          }
                                                                        : payable,
                                                            ),
                                                    });
                                                }}
                                            >
                                                <SelectTrigger className="w-full overflow-hidden">
                                                    <div className="flex w-full items-center overflow-hidden">
                                                        {(() => {
                                                            const payee =
                                                                findPayee(
                                                                    payable.payee_id,
                                                                    payable.payee_type,
                                                                );
                                                            const Icon =
                                                                payee?.alias_name
                                                                    ? getAliasNameIcon(
                                                                          payee.alias_name,
                                                                      )
                                                                    : null;
                                                            return (
                                                                <>
                                                                    {Icon && (
                                                                        <Icon className="mr-2 h-4 w-4 flex-shrink-0" />
                                                                    )}
                                                                    <span className="truncate">
                                                                        {
                                                                            payee?.label
                                                                        }
                                                                    </span>
                                                                </>
                                                            );
                                                        })()}
                                                    </div>
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {shipmentAccounting?.related_entities.map(
                                                        (entity) => (
                                                            <SelectItem
                                                                key={
                                                                    entity.alias_name +
                                                                    entity.id
                                                                }
                                                                value={payeeUniqueIdForComponent(
                                                                    entity.id,
                                                                    entity.alias_name,
                                                                )}
                                                            >
                                                                {(() => {
                                                                    const Icon =
                                                                        getAliasNameIcon(
                                                                            entity.alias_name,
                                                                        );
                                                                    return Icon ? (
                                                                        <Icon className="mr-2 inline h-4 w-4 flex-shrink-0" />
                                                                    ) : null;
                                                                })()}
                                                                {entity.label}
                                                            </SelectItem>
                                                        ),
                                                    )}
                                                </SelectContent>
                                            </Select>
                                            <InputError
                                                message={
                                                    errors[
                                                        `payables.${index}.payee_id` as keyof typeof errors
                                                    ]
                                                }
                                            />
                                            <InputError
                                                message={
                                                    errors[
                                                        `payables.${index}.payee_type` as keyof typeof errors
                                                    ]
                                                }
                                            />
                                        </TableCell>
                                        <TableCell className="hidden w-[20%] sm:table-cell">
                                            <Select
                                                value={
                                                    payable.rate_type_id?.toString() ||
                                                    ''
                                                }
                                                onValueChange={(value) => {
                                                    setData({
                                                        ...data,
                                                        payables:
                                                            data.payables.map(
                                                                (r, i) =>
                                                                    i === index
                                                                        ? {
                                                                              ...r,
                                                                              rate_type_id:
                                                                                  parseInt(
                                                                                      value,
                                                                                  ),
                                                                          }
                                                                        : r,
                                                            ),
                                                    });
                                                }}
                                            >
                                                <SelectTrigger className="w-full overflow-hidden">
                                                    <div className="flex w-full items-center overflow-hidden">
                                                        <span className="truncate">
                                                            {
                                                                findRateType(
                                                                    payable.rate_type_id,
                                                                )?.name
                                                            }
                                                        </span>
                                                    </div>
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {shipmentAccounting?.rate_types.map(
                                                        (rateType) => (
                                                            <SelectItem
                                                                key={
                                                                    rateType.id
                                                                }
                                                                value={rateType.id.toString()}
                                                            >
                                                                {rateType.name}
                                                            </SelectItem>
                                                        ),
                                                    )}
                                                </SelectContent>
                                            </Select>
                                            <InputError
                                                message={
                                                    errors[
                                                        `payables.${index}.rate_type_id` as keyof typeof errors
                                                    ]
                                                }
                                            />
                                        </TableCell>
                                        <TableCell className="hidden w-[15%] sm:table-cell">
                                            <div className="relative">
                                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                                                    {getCurrencySymbol(
                                                        payable.currency_code,
                                                    )}
                                                </span>
                                                <Input
                                                    type="number"
                                                    className="pl-7"
                                                    value={payable.rate}
                                                    onChange={(e) => {
                                                        const newRate =
                                                            parseFloat(
                                                                e.target.value,
                                                            );
                                                        setData({
                                                            ...data,
                                                            payables:
                                                                data.payables.map(
                                                                    (r, i) =>
                                                                        i ===
                                                                        index
                                                                            ? {
                                                                                  ...r,
                                                                                  rate: newRate,
                                                                                  total:
                                                                                      newRate *
                                                                                      r.quantity,
                                                                              }
                                                                            : r,
                                                                ),
                                                        });
                                                    }}
                                                />
                                            </div>
                                            <InputError
                                                message={
                                                    errors[
                                                        `payables.${index}.rate` as keyof typeof errors
                                                    ]
                                                }
                                            />
                                        </TableCell>
                                        <TableCell className="hidden w-[12%] sm:table-cell">
                                            <Input
                                                type="number"
                                                value={payable.quantity}
                                                onChange={(e) => {
                                                    const newQuantity =
                                                        parseInt(
                                                            e.target.value,
                                                        );
                                                    setData({
                                                        ...data,
                                                        payables:
                                                            data.payables.map(
                                                                (r, i) =>
                                                                    i === index
                                                                        ? {
                                                                              ...r,
                                                                              quantity:
                                                                                  newQuantity,
                                                                              total:
                                                                                  r.rate *
                                                                                  newQuantity,
                                                                          }
                                                                        : r,
                                                            ),
                                                    });
                                                }}
                                            />
                                            <InputError
                                                message={
                                                    errors[
                                                        `payables.${index}.quantity` as keyof typeof errors
                                                    ]
                                                }
                                            />
                                        </TableCell>
                                        <TableCell className="hidden w-[18%] text-right sm:table-cell">
                                            <div className="relative">
                                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                                                    {getCurrencySymbol(
                                                        payable.currency_code,
                                                    )}
                                                </span>
                                                <Input
                                                    type="number"
                                                    className="pl-7"
                                                    value={payable.total}
                                                    onChange={(e) => {
                                                        setData({
                                                            ...data,
                                                            payables:
                                                                data.payables.map(
                                                                    (r, i) =>
                                                                        i ===
                                                                        index
                                                                            ? {
                                                                                  ...r,
                                                                                  total: parseFloat(
                                                                                      e
                                                                                          .target
                                                                                          .value,
                                                                                  ),
                                                                              }
                                                                            : r,
                                                                ),
                                                        });
                                                    }}
                                                />
                                            </div>
                                            <InputError
                                                message={
                                                    errors[
                                                        `payables.${index}.total` as keyof typeof errors
                                                    ]
                                                }
                                            />
                                        </TableCell>
                                        <TableCell className="hidden w-[5%] sm:table-cell">
                                            <ConfirmButton
                                                variant="ghost"
                                                size="icon"
                                                className="h-7 w-7 text-destructive"
                                                onConfirm={() =>
                                                    setData({
                                                        ...data,
                                                        payables:
                                                            data.payables.filter(
                                                                (_, i) =>
                                                                    i !== index,
                                                            ),
                                                    })
                                                }
                                                confirmText="Delete"
                                            >
                                                <Trash className="h-4 w-4" />
                                            </ConfirmButton>
                                        </TableCell>

                                        {/* Mobile View - Single cell containing all fields */}
                                        <TableCell className="sm:hidden">
                                            <div className="max-w-full space-y-3 overflow-hidden">
                                                <div>
                                                    <Select
                                                        value={payeeUniqueIdForComponent(
                                                            payable.payee_id,
                                                            payable.payee_type,
                                                        )}
                                                        onValueChange={(
                                                            value,
                                                        ) => {
                                                            const { type, id } =
                                                                decodePayeeUniqueIdForComponent(
                                                                    value,
                                                                );
                                                            setData({
                                                                ...data,
                                                                payables:
                                                                    data.payables.map(
                                                                        (
                                                                            payable,
                                                                            payableIndex,
                                                                        ) =>
                                                                            payableIndex ===
                                                                            index
                                                                                ? {
                                                                                      ...payable,
                                                                                      payee_id:
                                                                                          id,
                                                                                      payee_type:
                                                                                          type,
                                                                                  }
                                                                                : payable,
                                                                    ),
                                                            });
                                                        }}
                                                    >
                                                        <SelectTrigger className="w-full overflow-hidden">
                                                            <div className="flex w-full items-center overflow-hidden">
                                                                {(() => {
                                                                    const payee =
                                                                        findPayee(
                                                                            payable.payee_id,
                                                                            payable.payee_type,
                                                                        );
                                                                    const Icon =
                                                                        payee?.alias_name
                                                                            ? getAliasNameIcon(
                                                                                  payee.alias_name,
                                                                              )
                                                                            : null;
                                                                    return (
                                                                        <>
                                                                            {Icon && (
                                                                                <Icon className="mr-2 h-4 w-4 flex-shrink-0" />
                                                                            )}
                                                                            <span className="truncate">
                                                                                {
                                                                                    payee?.label
                                                                                }
                                                                            </span>
                                                                        </>
                                                                    );
                                                                })()}
                                                            </div>
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {shipmentAccounting?.related_entities.map(
                                                                (entity) => (
                                                                    <SelectItem
                                                                        key={
                                                                            entity.alias_name +
                                                                            entity.id
                                                                        }
                                                                        value={payeeUniqueIdForComponent(
                                                                            entity.id,
                                                                            entity.alias_name,
                                                                        )}
                                                                    >
                                                                        {(() => {
                                                                            const Icon =
                                                                                getAliasNameIcon(
                                                                                    entity.alias_name,
                                                                                );
                                                                            return Icon ? (
                                                                                <Icon className="mr-2 inline h-4 w-4 flex-shrink-0" />
                                                                            ) : null;
                                                                        })()}
                                                                        {
                                                                            entity.label
                                                                        }
                                                                    </SelectItem>
                                                                ),
                                                            )}
                                                        </SelectContent>
                                                    </Select>
                                                    <InputError
                                                        message={
                                                            errors[
                                                                `payables.${index}.payee_id` as keyof typeof errors
                                                            ]
                                                        }
                                                    />
                                                    <InputError
                                                        message={
                                                            errors[
                                                                `payables.${index}.payee_type` as keyof typeof errors
                                                            ]
                                                        }
                                                    />
                                                </div>

                                                <div>
                                                    <Select
                                                        value={
                                                            payable.rate_type_id?.toString() ||
                                                            ''
                                                        }
                                                        onValueChange={(
                                                            value,
                                                        ) => {
                                                            setData({
                                                                ...data,
                                                                payables:
                                                                    data.payables.map(
                                                                        (
                                                                            r,
                                                                            i,
                                                                        ) =>
                                                                            i ===
                                                                            index
                                                                                ? {
                                                                                      ...r,
                                                                                      rate_type_id:
                                                                                          parseInt(
                                                                                              value,
                                                                                          ),
                                                                                  }
                                                                                : r,
                                                                    ),
                                                            });
                                                        }}
                                                    >
                                                        <SelectTrigger className="w-full overflow-hidden">
                                                            <div className="flex w-full items-center overflow-hidden">
                                                                <span className="truncate">
                                                                    {
                                                                        findRateType(
                                                                            payable.rate_type_id,
                                                                        )?.name
                                                                    }
                                                                </span>
                                                            </div>
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {shipmentAccounting?.rate_types.map(
                                                                (rateType) => (
                                                                    <SelectItem
                                                                        key={
                                                                            rateType.id
                                                                        }
                                                                        value={rateType.id.toString()}
                                                                    >
                                                                        {
                                                                            rateType.name
                                                                        }
                                                                    </SelectItem>
                                                                ),
                                                            )}
                                                        </SelectContent>
                                                    </Select>
                                                    <InputError
                                                        message={
                                                            errors[
                                                                `payables.${index}.rate_type_id` as keyof typeof errors
                                                            ]
                                                        }
                                                    />
                                                </div>

                                                <div className="grid max-w-full grid-cols-3 gap-2 overflow-hidden">
                                                    <div>
                                                        <label className="block truncate pb-1 text-sm font-medium">
                                                            Rate
                                                        </label>
                                                        <div className="relative">
                                                            <span className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                                                                {getCurrencySymbol(
                                                                    payable.currency_code,
                                                                )}
                                                            </span>
                                                            <Input
                                                                type="number"
                                                                className="w-full pl-7"
                                                                value={
                                                                    payable.rate
                                                                }
                                                                onChange={(
                                                                    e,
                                                                ) => {
                                                                    const newRate =
                                                                        parseFloat(
                                                                            e
                                                                                .target
                                                                                .value,
                                                                        );
                                                                    setData({
                                                                        ...data,
                                                                        payables:
                                                                            data.payables.map(
                                                                                (
                                                                                    r,
                                                                                    i,
                                                                                ) =>
                                                                                    i ===
                                                                                    index
                                                                                        ? {
                                                                                              ...r,
                                                                                              rate: newRate,
                                                                                              total:
                                                                                                  newRate *
                                                                                                  r.quantity,
                                                                                          }
                                                                                        : r,
                                                                            ),
                                                                    });
                                                                }}
                                                            />
                                                        </div>
                                                        <InputError
                                                            message={
                                                                errors[
                                                                    `payables.${index}.rate` as keyof typeof errors
                                                                ]
                                                            }
                                                        />
                                                    </div>

                                                    <div>
                                                        <label className="block truncate pb-1 text-sm font-medium">
                                                            Quantity
                                                        </label>
                                                        <Input
                                                            type="number"
                                                            className="w-full"
                                                            value={
                                                                payable.quantity
                                                            }
                                                            onChange={(e) => {
                                                                const newQuantity =
                                                                    parseInt(
                                                                        e.target
                                                                            .value,
                                                                    );
                                                                setData({
                                                                    ...data,
                                                                    payables:
                                                                        data.payables.map(
                                                                            (
                                                                                r,
                                                                                i,
                                                                            ) =>
                                                                                i ===
                                                                                index
                                                                                    ? {
                                                                                          ...r,
                                                                                          quantity:
                                                                                              newQuantity,
                                                                                          total:
                                                                                              r.rate *
                                                                                              newQuantity,
                                                                                      }
                                                                                    : r,
                                                                        ),
                                                                });
                                                            }}
                                                        />
                                                        <InputError
                                                            message={
                                                                errors[
                                                                    `payables.${index}.quantity` as keyof typeof errors
                                                                ]
                                                            }
                                                        />
                                                    </div>

                                                    <div>
                                                        <label className="block truncate pb-1 text-sm font-medium">
                                                            Total
                                                        </label>
                                                        <div className="relative">
                                                            <span className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                                                                {getCurrencySymbol(
                                                                    payable.currency_code,
                                                                )}
                                                            </span>
                                                            <Input
                                                                type="number"
                                                                className="w-full pl-7"
                                                                value={
                                                                    payable.total
                                                                }
                                                                onChange={(
                                                                    e,
                                                                ) => {
                                                                    setData({
                                                                        ...data,
                                                                        payables:
                                                                            data.payables.map(
                                                                                (
                                                                                    r,
                                                                                    i,
                                                                                ) =>
                                                                                    i ===
                                                                                    index
                                                                                        ? {
                                                                                              ...r,
                                                                                              total: parseFloat(
                                                                                                  e
                                                                                                      .target
                                                                                                      .value,
                                                                                              ),
                                                                                          }
                                                                                        : r,
                                                                            ),
                                                                    });
                                                                }}
                                                            />
                                                        </div>
                                                        <InputError
                                                            message={
                                                                errors[
                                                                    `payables.${index}.total` as keyof typeof errors
                                                                ]
                                                            }
                                                        />
                                                    </div>
                                                </div>

                                                <div className="text-right">
                                                    <ConfirmButton
                                                        variant="ghost"
                                                        size="icon"
                                                        className="h-7 w-7 text-destructive"
                                                        onConfirm={() =>
                                                            setData({
                                                                ...data,
                                                                payables:
                                                                    data.payables.filter(
                                                                        (
                                                                            _,
                                                                            i,
                                                                        ) =>
                                                                            i !==
                                                                            index,
                                                                    ),
                                                            })
                                                        }
                                                        confirmText="Delete"
                                                    >
                                                        <Trash className="h-4 w-4" />
                                                    </ConfirmButton>
                                                </div>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    <TableRow key={index}>
                                        <TableCell className="hidden w-[30%] sm:table-cell">
                                            <span className="block max-w-full truncate">
                                                {
                                                    findPayee(
                                                        payable.payee_id,
                                                        payable.payee_type,
                                                    )?.label
                                                }
                                            </span>
                                        </TableCell>
                                        <TableCell className="hidden w-[20%] sm:table-cell">
                                            <span className="block truncate">
                                                {
                                                    findRateType(
                                                        payable.rate_type_id,
                                                    )?.name
                                                }
                                            </span>
                                        </TableCell>
                                        <TableCell className="hidden w-[15%] sm:table-cell">
                                            {formatCurrency(
                                                payable.rate,
                                                payable.currency_code,
                                            )}
                                        </TableCell>
                                        <TableCell className="hidden w-[12%] sm:table-cell">
                                            {payable.quantity}
                                        </TableCell>
                                        <TableCell className="hidden w-[18%] text-right sm:table-cell">
                                            {formatCurrency(
                                                payable.total,
                                                payable.currency_code,
                                            )}
                                        </TableCell>
                                        <TableCell className="hidden w-[5%] sm:table-cell"></TableCell>

                                        {/* Mobile view for non-edit mode */}
                                        <TableCell className="sm:hidden">
                                            <div className="max-w-full space-y-1 overflow-hidden">
                                                <div className="flex max-w-full items-center justify-between">
                                                    <div className="max-w-[60%] truncate">
                                                        <span className="font-medium">
                                                            {
                                                                findPayee(
                                                                    payable.payee_id,
                                                                    payable.payee_type,
                                                                )?.label
                                                            }
                                                        </span>
                                                    </div>
                                                    <span className="flex-shrink-0">
                                                        {formatCurrency(
                                                            payable.total,
                                                            payable.currency_code,
                                                        )}
                                                    </span>
                                                </div>
                                                <div className="truncate text-sm text-muted-foreground">
                                                    {
                                                        findRateType(
                                                            payable.rate_type_id,
                                                        )?.name
                                                    }
                                                </div>
                                                <div className="text-sm text-muted-foreground">
                                                    {formatCurrency(
                                                        payable.rate,
                                                        payable.currency_code,
                                                    )}{' '}
                                                    x {payable.quantity}
                                                </div>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ),
                            )
                        )}
                    </TableBody>
                    <TableFooter>
                        <TableRow>
                            <TableCell
                                colSpan={4}
                                className="hidden text-right font-medium sm:table-cell"
                            >
                                Total
                            </TableCell>
                            <TableCell className="hidden text-right font-bold sm:table-cell">
                                {formatCurrency(
                                    data.payables.reduce(
                                        (sum, payable) => sum + payable.total,
                                        0,
                                    ),
                                    data.payables.length > 0
                                        ? data.payables[0].currency_code
                                        : 'USD',
                                )}
                            </TableCell>
                            <TableCell className="hidden sm:table-cell"></TableCell>

                            {/* Mobile view for totals */}
                            <TableCell colSpan={1} className="sm:hidden">
                                <div className="flex items-center justify-between">
                                    <span className="font-medium">Total</span>
                                    <span className="font-bold">
                                        {formatCurrency(
                                            data.payables.reduce(
                                                (sum, payable) =>
                                                    sum + payable.total,
                                                0,
                                            ),
                                            data.payables.length > 0
                                                ? data.payables[0].currency_code
                                                : 'USD',
                                        )}
                                    </span>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableFooter>
                </Table>
            </CardContent>
        </Card>
    );
}
