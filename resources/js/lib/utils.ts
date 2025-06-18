import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

// Better as a utility in a separate file like utils/currency.ts
export const getCurrencySymbol = (currencyCode: string): string => {
    const symbols: Record<string, string> = {
        usd: '$',
        eur: '€',
        gbp: '£',
        // Add more currencies as needed
    };
    return symbols[currencyCode.toLowerCase()] || currencyCode;
};

export const formatCurrency = (value: number, currencyCode: string) => {
    const symbol = getCurrencySymbol(currencyCode);
    return `${symbol}${value.toFixed(2)}`;
};
