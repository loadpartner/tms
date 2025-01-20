import { Button } from '@/Components/ui/button';
import GuestLayout from '@/Layouts/GuestLayout';
import { cn } from '@/lib/utils';
import { Organization, OrganizationInvite } from '@/types/organization';
import { Head, router, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function InviteAccept({
    invite,
    organization,
    showCreateOption = false,
}: {
    invite: OrganizationInvite;
    organization: Organization;
    showCreateOption: boolean;
}) {
    const { data, setData, post, processing, errors } = useForm({});

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(
            route('organizations.invites.accept', {
                invite: invite.code,
                organization: organization.id,
            }),
        );
    };

    return (
        <GuestLayout>
            <Head title="Accept Organization Invite" />

            <div className={cn('flex flex-col gap-6')}>
                <form onSubmit={submit}>
                    <div className="flex flex-col gap-6">
                        <h1 className="mb-8 text-xl font-bold">
                            Join {organization.name}
                        </h1>
                    </div>
                    <div className="flex flex-col gap-6">
                        <div className="grid gap-2">
                            <p>
                                Invite expires at{' '}
                                {new Date(invite.expire_at).toLocaleString()}{' '}
                            </p>
                        </div>
                        <Button
                            type="submit"
                            className="w-full"
                            disabled={processing}
                            tabIndex={2}
                        >
                            Accept Invite
                        </Button>
                    </div>
                </form>
            </div>
            {showCreateOption && (
                <>
                    <div className="relative my-4 text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-border">
                        <span className="relative z-10 bg-background px-2 text-muted-foreground">
                            Or
                        </span>
                    </div>
                    <div className="flex flex-col gap-6">
                        <Button
                            onClick={() => {
                                router.get(route('organizations.create'));
                            }}
                            className="w-full"
                            variant="secondary"
                            tabIndex={3}
                        >
                            Create a new organization
                        </Button>
                    </div>
                </>
            )}
        </GuestLayout>
    );
}
