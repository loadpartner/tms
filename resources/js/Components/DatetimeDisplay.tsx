import { HTMLAttributes } from 'react';

export default function DatetimeDisplay({
    datetime,
    timezone,
    className = '',
    ...props
}: HTMLAttributes<HTMLDivElement> & { datetime?: string; timezone?: string }) {
    let formattedDatetime = datetime;

    if (formattedDatetime) {
        if (formattedDatetime.substring(formattedDatetime.length - 1) !== 'Z') {
            formattedDatetime = formattedDatetime + 'Z';
        }

        const options: Intl.DateTimeFormatOptions = {
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
            timeZoneName: 'short',
        };

        if (timezone && timezone.trim() !== '') {
            options.timeZone = timezone;
        }

        formattedDatetime = new Date(formattedDatetime).toLocaleString(
            'en-US',
            options,
        );
    }

    return (
        <div {...props} className={className}>
            {formattedDatetime !== '' ? formattedDatetime : 'Not set'}
        </div>
    );
}
