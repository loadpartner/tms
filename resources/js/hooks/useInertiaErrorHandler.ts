import { useToast } from '@/hooks/UseToast';
import { router } from '@inertiajs/react';
import { useEffect } from 'react';

export function useInertiaErrorHandler() {
    const { toast } = useToast();

    useEffect(() => {
        const removeEventListener = router.on('invalid', (event) => {
            const responseBody = event.detail.response?.data;
            if (responseBody?.error_message) {
                toast({
                    variant: 'destructive',
                    title: 'An error has occurred',
                    description: responseBody?.error_message,
                });
                event.preventDefault();
            }
        });

        // Cleanup the event listener on component unmount
        return () => {
            removeEventListener();
        };
    }, [toast]); // Add toast as a dependency
}
