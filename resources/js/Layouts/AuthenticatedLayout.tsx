import { AppSidebar } from '@/Components/AppSidebar';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb';
import { Separator } from '@/Components/ui/separator';
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from '@/Components/ui/sidebar';
import { Toaster } from '@/Components/ui/toaster';
import { useInertiaErrorHandler } from '@/hooks/useInertiaErrorHandler';
import { Link } from '@inertiajs/react';
import { Fragment, PropsWithChildren } from 'react';

interface BreadcrumbItem {
    title: string;
    href?: string;
}

export default function Authenticated({
    children,
    breadcrumbs = [],
}: PropsWithChildren<{ breadcrumbs?: BreadcrumbItem[] }>) {
    useInertiaErrorHandler();

    const sideBarOpen =
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('sidebar:state'))
            ?.split('=')[1] === 'true';

    return (
        <SidebarProvider defaultOpen={sideBarOpen}>
            <AppSidebar />
            <SidebarInset>
                <header className="flex h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
                    <div className="flex items-center gap-2 px-4">
                        <SidebarTrigger className="-ml-1" />
                        <Separator
                            orientation="vertical"
                            className="mr-2 h-4"
                        />
                        <Breadcrumb>
                            <BreadcrumbList>
                                {breadcrumbs.map((item, index) => (
                                    <Fragment key={index}>
                                        <BreadcrumbItem className="hidden md:block">
                                            {item.href ? (
                                                <BreadcrumbLink asChild>
                                                    <Link
                                                        prefetch={true}
                                                        href={item.href}
                                                    >
                                                        {item.title}
                                                    </Link>
                                                </BreadcrumbLink>
                                            ) : (
                                                <BreadcrumbPage>
                                                    {item.title}
                                                </BreadcrumbPage>
                                            )}
                                        </BreadcrumbItem>
                                        {index < breadcrumbs.length - 1 && (
                                            <BreadcrumbSeparator className="hidden md:block" />
                                        )}
                                    </Fragment>
                                ))}
                            </BreadcrumbList>
                        </Breadcrumb>
                    </div>
                </header>
                <main className="px-1 pb-2">{children}</main>
                <Toaster />
            </SidebarInset>
        </SidebarProvider>
    );
}
