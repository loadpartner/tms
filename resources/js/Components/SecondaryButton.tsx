import { ButtonHTMLAttributes } from 'react';
import { Button } from './ui/button';

export default function SecondaryButton({
    className = '',
    disabled,
    children,
    ...props
}: ButtonHTMLAttributes<HTMLButtonElement>) {
    return (
        <Button
            variant="secondary"
            {...props}
            className={className}
            disabled={disabled}
        >
            {children}
        </Button>
    );
}
