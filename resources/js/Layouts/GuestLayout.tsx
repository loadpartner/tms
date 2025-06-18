import { useInertiaErrorHandler } from '@/hooks/useInertiaErrorHandler';
import { PropsWithChildren } from 'react';

export default function Guest({ children }: PropsWithChildren) {
    useInertiaErrorHandler();

    return (
        <div className="flex min-h-screen flex-col items-center bg-[#fafafa] pt-6 dark:bg-gray-900 sm:justify-center sm:pt-0">
            {/* <div>
                <Link href="/">
                    <ApplicationLogo className="h-20 w-20 fill-current text-gray-500" />
                </Link>
            </div> */}

            <div className="mt-6 w-full overflow-hidden px-6 py-4 sm:max-w-md sm:rounded-lg">
                {children}
            </div>
        </div>
    );
}
