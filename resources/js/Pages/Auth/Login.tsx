import { LoginForm } from '@/Components/LoginForm';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head } from '@inertiajs/react';

export default function Login({
    status,
    canResetPassword,
}: {
    status?: string;
    canResetPassword: boolean;
}) {
    // const { data, setData, post, processing, errors, reset } = useForm({
    //     email: '',
    //     password: '',
    //     remember: false,
    // });

    // const submit: FormEventHandler = (e) => {
    //     e.preventDefault();

    //     post(route('login'), {
    //         onFinish: () => reset('password'),
    //     });
    // };

    return (
        <GuestLayout>
            <Head title="Log in" />

            <LoginForm />

            {status && (
                <div className="mb-4 w-full text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}
        </GuestLayout>
    );
}
