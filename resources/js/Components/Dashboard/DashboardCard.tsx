import { cn } from '@/lib/utils';
import React from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';

export default function DashboardCard({
    title,
    children,
    cols = 1,
    className,
}: {
    title: React.ReactNode;
    children: React.ReactNode;
    cols?: number;
    className?: string;
}) {
    return (
        <Card className={cn('w-full', className, `col-span-${cols}`)}>
            <CardHeader>
                <CardTitle>{title}</CardTitle>
            </CardHeader>
            <CardContent>{children}</CardContent>
        </Card>
    );
}
