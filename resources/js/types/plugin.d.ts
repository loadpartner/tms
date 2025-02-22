interface Plugin {
    id: number;
    slug: string;
    is_active: boolean;
    name: string;
    description: string;
    version: string;
    author: string;
}

export type { Plugin }; 