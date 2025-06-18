import { cn } from '@/lib/utils';
import { Construction } from 'lucide-react';
import { HTMLAttributes } from 'react';

interface ComingSoonProps extends HTMLAttributes<HTMLDivElement> {
    title?: string;
    message?: string;
    icon?: React.ReactNode;
    variant?: 'default' | 'outline' | 'ghost';
    size?: 'sm' | 'md' | 'lg';
}

export function ComingSoon({
    className,
    title = 'Coming Soon',
    message = 'This feature is currently under development',
    icon,
    variant = 'default',
    size = 'md',
    ...props
}: ComingSoonProps) {
    const sizeClasses = {
        sm: 'p-3 max-w-xs',
        md: 'p-5 max-w-sm',
        lg: 'p-6 max-w-md',
    };

    const variantClasses = {
        default: 'bg-muted/50 border border-muted/30',
        outline: 'border border-muted/40',
        ghost: 'bg-transparent',
    };

    const iconSize = {
        sm: 'h-5 w-5',
        md: 'h-6 w-6',
        lg: 'h-8 w-8',
    };

    return (
        <div
            className={cn(
                'flex flex-col items-center justify-center gap-3 rounded-lg text-center',
                sizeClasses[size],
                variantClasses[variant],
                className,
            )}
            {...props}
        >
            <div className="rounded-full border border-muted/20 bg-background/60 p-2">
                {icon || (
                    <Construction
                        className={cn('text-muted-foreground', iconSize[size])}
                    />
                )}
            </div>

            <div className="space-y-1">
                <h3
                    className={cn('font-medium text-muted-foreground', {
                        'text-xs': size === 'sm',
                        'text-sm': size === 'md',
                        'text-base': size === 'lg',
                    })}
                >
                    {title}
                </h3>

                <p
                    className={cn('text-muted-foreground/70', {
                        'text-[10px]': size === 'sm',
                        'text-xs': size === 'md',
                        'text-sm': size === 'lg',
                    })}
                >
                    {message}
                </p>
            </div>
        </div>
    );
}
