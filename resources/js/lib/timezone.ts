// resources/js/utils/timezone.ts

import { TimezoneData } from '@/types';

/**
 * Converts IANA timezone format (e.g., "America/New_York") to shorthand format (e.g., "EST")
 */
export function getTimezoneShorthand(timeZone: string): string {
    try {
        return (
            new Intl.DateTimeFormat('en', {
                timeZone: timeZone,
                timeZoneName: 'short',
            })
                .formatToParts(new Date())
                .find((part) => part.type === 'timeZoneName')?.value || timeZone
        );
    } catch (error) {
        console.error('Error converting timezone:', error);
        return timeZone;
    }
}

/**
 * Gets the current user's browser timezone in IANA format (e.g., "America/New_York")
 */
export function getUserBrowserTimezone(): string {
    return Intl.DateTimeFormat().resolvedOptions().timeZone;
}

/**
 * Fetches timezone data for a list of zipcodes
 * @param zipcodes List of zipcodes to fetch timezone data for
 * @param existingTimezones Optional existing timezone data to avoid refetching
 * @returns Promise that resolves to record of zipcode to timezone data
 */
export async function fetchTimezones(
    zipcodes: string[],
    existingTimezones: Record<string, TimezoneData> = {},
): Promise<Record<string, TimezoneData>> {
    // Filter out zipcodes we already have data for
    const newZipcodes = zipcodes.filter(
        (zipcode) => !(zipcode in existingTimezones) && Boolean(zipcode),
    );

    if (newZipcodes.length === 0) {
        return existingTimezones;
    }

    try {
        const response = await fetch(
            route('timezones.zipcode', { zipcodes: newZipcodes }),
            {
                method: 'GET',
            },
        );
        const data = await response.json();

        return {
            ...existingTimezones,
            ...data,
        };
    } catch (error) {
        console.error('Error fetching timezone data:', error);
        return existingTimezones;
    }
}

/**
 * Gets the timezone identifier for a location with a zipcode
 * @param zipcode Zipcode to get timezone for
 * @param timezones Record of zipcode to timezone data
 * @returns Timezone identifier or undefined if not found
 */
export function getTimezoneByZipcode(
    zipcode: string | undefined,
    timezones: Record<string, TimezoneData>,
): string | undefined {
    if (!zipcode) {
        return undefined;
    }

    return timezones[zipcode]?.identifier;
}

/**
 * Converts a date string to a localized string in the specified timezone
 * @param date Date string to convert
 * @param timezone Timezone identifier (e.g., "America/New_York")
 * @returns Localized date string
 */
export function convertDateForTimezone(
    date: string,
    timezone?: string,
): string {
    if (!date) {
        return '';
    }

    // Ensure date string has Z suffix for UTC
    if (date.substring(date.length - 1) !== 'Z') {
        date = date + 'Z';
    }

    const dateObj = new Date(date);

    // Convert to specified timezone if available
    if (timezone) {
        return dateObj.toLocaleString('en-US', { timeZone: timezone });
    }

    // Default to local timezone
    return dateObj.toLocaleString('en-US');
}
