export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    current_organization_id: number;
    organizations: Organization[];
    profile_photo_url?: string;
    timezone?: string;
    language_preference?: string;
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
    integration_settings: {
        [key: string]: string;
    };
    config: {
        enable_billing: boolean;
    };
};

export interface Location {
    id: number;
    name: string;
    address_line_1: string;
    address_line_2: string;
    address_city: string;
    address_state: string;
    address_zipcode: string;
    selectable_label: string;
    latitude?: number;
    longitude?: number;
    timezone?: TimezoneData;
}

export interface Contact {
    id: number;
    contact_type?: string;
    title?: string;
    name: string;
    email?: string;
    mobile_phone?: string;
    office_phone?: string;
    office_phone_extension?: string;
    contact_for_id: number;
    contact_for_type: string;
    contact_for?: {
        id: number;
    };
}

export interface Facility {
    id: number;
    name: string;
    location?: Location;
    location_id?: number;

    documents?: Document[];
    document_folders?: DocumentFolder[];
}

export interface Carrier {
    id: number;
    name: string;
    mc_number: string;
    dot_number: string;
    contact_email: string;
    contact_phone: string;

    physical_location_id: number;
    billing_email: string;
    billing_phone: string;
    billing_location_id: number;

    billing_location?: Location;
    physical_location?: Location;
    safer_report?: CarrierSaferReport;

    documents?: Document[];
    document_folders?: DocumentFolder[];

    contacts?: Contact[];
}

export interface CarrierSaferReport {
    id: number;
    dot_number: string;
    created_at: string;
    report: {
        name: string;
        dba: string;
        full_address: string;
        address: {
            street: string;
            city: string;
            state: string;
            zip: string;
        };
        general?: {
            carrier?: {
                legalName?: string;
                dbaName?: string;
                dotNumber?: string;
                statusCode?: string;
                safetyRating?: string;
                totalPowerUnits?: number;
                totalDrivers?: number;
                mcs150Outdated?: string;
                crashTotal?: number;
                fatalCrash?: number;
                injCrash?: number;
                driverInsp?: number;
                driverOosInsp?: number;
                driverOosRate?: number;
                driverOosRateNationalAverage?: string;
                vehicleInsp?: number;
                vehicleOosInsp?: number;
                vehicleOosRate?: number;
                vehicleOosRateNationalAverage?: string;
                phyStreet?: string;
                phyCity?: string;
                phyState?: string;
                phyZipcode?: string;
            };
        };
    };
    is_full_report: boolean;
    carrier?: Carrier;
}

export interface Customer {
    id: number;
    name: string;

    net_pay_days: number | null;
    billing_location?: Location;
    dba_name: string | null;
    invoice_number_schema: string | null;
    billing_contact?: Contact;

    documents?: Document[];
    document_folders?: DocumentFolder[];
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
    [key: string]: string | number | boolean | undefined;
}

export interface Shipment {
    id: number;
    shipment_number: string;
    customers: Customer[];
    carrier?: Carrier;
    driver?: Contact;
    stops: ShipmentStop[];
    weight: number;
    trip_distance: number;
    trailer_type_id: number;
    trailer_size_id: number;
    trailer_temperature_range: boolean;
    trailer_temperature?: number;
    trailer_temperature_maximum?: number;
    lane?: string;
    next_stop?: ShipmentStop;
    current_stop?: ShipmentStop;
    trailer_type?: TrailerType;
    trailer_size?: TrailerSize;
    state_label: string;
    state: ShipmentState;
    documents?: Document[];
    document_folders?: DocumentFolder[];
    latest_rate_confirmation?: Document;
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

export interface CarrierBounce {
    id: number;
    carrier_id: number;
    shipment_id: number;
    driver_id: number;
    bounce_cause: string;
    reason: string;
    created_at: string;
    bounced_by: number;

    bounced_by_user?: User;
    shipment?: Shipment;
    carrier?: Carrier;
    driver?: Contact;
}

export interface Timezone {
    id: number;
    name: string;
}

export interface Document {
    id: number;
    name: string;
    path: string;
    folder_name?: string;
    documentable_type: string;
    documentable_id: number;
    uploaded_by?: User;
    temporary_url?: string;
    metadata?: Record<string, unknown>;
}

export interface DocumentFolder {
    id?: number;
    name: string;
}

export interface RateType {
    id: number;
    name: string;
}

export interface Payable {
    id: number;
    organization_id: number;
    shipment_id: number;
    payee_id: number;
    payee_type: string;
    payee?: {
        id: number;
        name: string;
    };
    rate: number;
    quantity: number;
    total: number;
    rate_type_id: number;
    rate_type?: {
        id: number;
        name: string;
    };
    currency_code: string;
}

export interface Receivable {
    id: number;
    organization_id: number;
    shipment_id: number;
    payer_id: number;
    payer_type: string;
    payer?: {
        id: number;
        name: string;
    };
    rate: number;
    quantity: number;
    total: number;
    rate_type_id: number;
    rate_type?: {
        id: number;
        name: string;
    };
    currency_code: string;
}

export interface ShipmentAccounting {
    id: number;
    receivables: Receivable[];
    payables: Payable[];
    rate_types: RateType[];
    related_entities: AliasModel[];
}

export interface AliasModel {
    id: number;
    alias_name: string;
    label: string;
}

export interface CheckCall {
    id: number;
    organization_id: number;
    carrier_id: number;
    shipment_id: number;
    user_id: number | null;
    is_late: boolean;
    arrived_at: string | null;
    left_at: string | null;
    eta: string | null;
    is_truck_empty: boolean | null;
    reported_trailer_temp: number | null;
    loaded_unloaded_at: string | null;
    contact_name: string | null;
    contact_method: string | null;
    contact_method_detail: string | null;
    note_id: number | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    carrier?: Carrier;
    shipment?: Shipment;
    creator?: User;
    note?: Note;
    next_stop?: ShipmentStop;
    current_stop?: ShipmentStop;
    current_stop_id?: number;
    next_stop_id?: number;
}

// Typescript interface for timezone data
export interface TimezoneData {
    identifier: string;
    dst_tz: string;
    std_tz: string;
}
