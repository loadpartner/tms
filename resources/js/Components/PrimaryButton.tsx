import { ButtonHTMLAttributes } from 'react';
import { Button } from './ui/button';

export default function PrimaryButton({
    className = '',
    disabled,
    children,
    ...props
}: ButtonHTMLAttributes<HTMLButtonElement>) {
    return (
        <Button {...props} className={className} disabled={disabled}>
            {children}
        </Button>
    );
}
