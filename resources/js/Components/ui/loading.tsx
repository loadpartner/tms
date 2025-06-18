import { cn } from '@/lib/utils';
import { Loader2 } from 'lucide-react';
import { HTMLAttributes } from 'react';

interface LoadingProps extends HTMLAttributes<HTMLDivElement> {
    size?: 'sm' | 'md' | 'lg';
    text?: string;
}

export function Loading({
    className,
    size = 'md',
    text,
    ...props
}: LoadingProps) {
    const sizeClasses = {
        sm: 'h-4 w-4',
        md: 'h-6 w-6',
        lg: 'h-8 w-8',
    };

    return (
        <div
            className={cn(
                'flex flex-col items-center justify-center gap-2',
                className,
            )}
            {...props}
        >
            <Loader2
                className={cn('animate-spin text-primary', sizeClasses[size])}
            />
            {text && <p className="text-sm text-muted-foreground">{text}</p>}
        </div>
    );
}
