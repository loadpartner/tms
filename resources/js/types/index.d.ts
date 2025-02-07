export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    current_organization_id: number;
    organizations: Organization[];
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
        permissions: {
            [key: string]: boolean;
        };
    };
    app: {
        name: string;
    };
    shipper?: Shipper;
    errors: any;
};

export interface Facility {
    id: number;
    name: string;
}

export interface Carrier {
    id: number;
    name: string;
}

export interface Shipper {
    id: number;
    name: string;
    city: string | null;
    state: string | null;
}

export interface ShipmentStop {
    id: number | null;
    shipment_id: number;
    facility?: Facility | null;
    facility_id?: number | null;
    stop_type: StopType;
    stop_number: number;
    special_instructions: string;
    reference_numbers: string;
    eta: string;
    arrived_at: string;
    loaded_unloaded_at: string;
    left_at: string;
    appointment_at: string;
    appointment_end_at: string;
    appointment_type: string;
}

export interface Shipment {
    id: number;
    shipment_number: string;
    shippers: Shipper[];
    carrier: Carrier;
    stops: ShipmentStop[];
    weight: number;
    trip_distance: number;
    trailer_type_id: number;
    trailer_size_id: number;
    trailer_temperature_range: boolean;
    trailer_temperature: number;
    trailer_temperature_maximum?: number;
    lane?: string;
    next_stop?: ShipmentStop;
    trailer_type?: TrailerType;
    trailer_size?: TrailerSize;
}

export interface Note {
    id: number;
    user_id: number;
    note: string;
    created_at: string;
    updated_at: string;
    user: User | null;
}

export interface TrailerType {
    id: number;
    name: string;
}

export interface TrailerSize {
    id: number;
    name: string;
}
