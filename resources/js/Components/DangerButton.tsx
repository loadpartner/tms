import { ButtonHTMLAttributes } from 'react';
import { Button } from './ui/button';

export default function DangerButton({
    className = '',
    disabled,
    children,
    ...props
}: ButtonHTMLAttributes<HTMLButtonElement>) {
    return (
        <Button
            variant="destructive"
            {...props}
            className={className}
            disabled={disabled}
        >
            {children}
        </Button>
    );
}
