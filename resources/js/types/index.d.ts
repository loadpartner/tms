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
}
