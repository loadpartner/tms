import FeedbackModal from '@/Components/FeedbackModal';
import SeatSelectionModal from '@/Components/SeatSelectionModal';
import SubscriptionSuccessModal from '@/Components/SubscriptionSuccessModal';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Toaster } from '@/Components/ui/toaster';
import { PageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    Check,
    Github,
    LogOut,
    Settings,
    Star,
} from 'lucide-react';
import { useEffect, useState } from 'react';

export default function Products({
    hasSubscription,
}: {
    hasSubscription: boolean;
}) {
    const [showSeatModal, setShowSeatModal] = useState(false);
    const [showFeedbackModal, setShowFeedbackModal] = useState(false);
    const [showSuccessModal, setShowSuccessModal] = useState(false);
    const { auth } = usePage<PageProps>().props;

    // Check for success parameter in URL
    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');

        if (success) {
            setShowSuccessModal(true);
            // Clean up URL by removing the success parameter
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.delete('success');
            window.history.replaceState({}, '', newUrl.toString());
        }
    }, []);

    const plans = [
        {
            name: 'Open Source',
            price: 'Free',
            description: 'Perfect for technical individuals getting started',
            badge: 'Community',
            badgeVariant: 'secondary' as const,
            features: [
                'Full source code access',
                'Community support',
                'Basic features',
                'Self-hosted deployment',
                'Active development',
            ],
            buttonText: 'View on GitHub',
            buttonVariant: 'outline' as const,
            buttonIcon: <Github className="h-4 w-4" />,
            href: 'https://github.com/loadpartner/tms',
            isPopular: false,
            requiresSeatSelection: false,
        },
        {
            name: 'Premium',
            price: '$50',
            priceUnit: '/user/month',
            description:
                'For individuals and growing teams that want hassle-free management',
            badge: 'Most Popular',
            badgeVariant: 'default' as const,
            features: [
                'Everything in Open Source',
                'Managed hosting',
                'Automatic updates',
                'Priority support',
                'Advanced analytics',
                'Team collaboration tools',
                'API access',
            ],
            buttonText: hasSubscription ? 'Manage Billing' : 'Get Started',
            buttonVariant: hasSubscription
                ? ('outline' as const)
                : ('default' as const),
            buttonIcon: hasSubscription ? (
                <Settings className="h-4 w-4" />
            ) : (
                <ArrowRight className="h-4 w-4" />
            ),
            href: hasSubscription
                ? route(
                      'organizations.billing',
                      auth?.user?.current_organization_id || 1,
                  )
                : route('subscriptions.new'),
            isPopular: true,
            requiresSeatSelection: !hasSubscription,
        },
        {
            name: 'Enterprise',
            price: '$299',
            priceUnit: '/month',
            priceNote: 'starting at',
            description:
                'For enterprises wanting dedicated infrastructure and custom development',
            badge: '',
            badgeVariant: 'outline' as const,
            features: [
                'Everything in Managed',
                'Dedicated server infrastructure',
                'Custom integrations',
                'Advanced security features',
                'Dedicated support manager',
                'Custom SLA options',
                'White-label options',
                'Custom development hours included',
            ],
            buttonText: 'Contact Sales',
            buttonVariant: 'outline' as const,
            buttonIcon: <ArrowRight className="h-4 w-4" />,
            href: '#', // This will be handled by the click handler to open feedback modal
            isPopular: false,
            requiresSeatSelection: false,
            opensFeedbackModal: true,
        },
    ];

    const handlePlanClick = (plan: (typeof plans)[0]) => {
        if (plan.opensFeedbackModal) {
            setShowFeedbackModal(true);
        } else if (plan.requiresSeatSelection) {
            setShowSeatModal(true);
        } else {
            // For external links or non-seat-selection plans
            if (plan.href.startsWith('http')) {
                window.open(plan.href, '_blank');
            } else {
                window.location.href = plan.href;
            }
        }
    };

    return (
        <div>
            <Head title="Products" />

            <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="mb-12 text-center">
                    {/* Go Back Button - only show if user is logged in and has subscription */}
                    {((auth?.user && hasSubscription) || !auth?.user) && (
                        <div className="mb-6 flex justify-start">
                            <Button
                                variant="outline"
                                onClick={() =>
                                    (window.location.href = route('home'))
                                }
                                className="flex items-center gap-2"
                            >
                                <ArrowLeft className="h-4 w-4" />
                                Go Back
                            </Button>
                        </div>
                    )}

                    {/* Logout Button - show if user is logged in */}
                    {auth?.user && (
                        <div className="mb-6 flex justify-end">
                            <Button
                                variant="outline"
                                onClick={() =>
                                    router.visit(route('logout'), {
                                        method: 'post',
                                    })
                                }
                                className="flex items-center gap-2"
                            >
                                <LogOut className="h-4 w-4" />
                                Log out
                            </Button>
                        </div>
                    )}

                    <h1 className="mb-4 text-4xl font-bold tracking-tight text-foreground">
                        Choose your subscription plan
                    </h1>
                    <p className="mx-auto max-w-2xl text-lg text-muted-foreground">
                        Select the perfect plan for your needs
                    </p>
                </div>

                {/* Plans Grid */}
                <div className="mx-auto grid max-w-6xl grid-cols-1 gap-8 md:grid-cols-3">
                    {plans.map((plan) => (
                        <Card
                            key={plan.name}
                            className={`relative flex h-full flex-col overflow-hidden ${
                                plan.isPopular
                                    ? 'scale-105 shadow-xl ring-2 ring-primary'
                                    : 'transition-shadow hover:shadow-lg'
                            }`}
                        >
                            {plan.isPopular && (
                                <div className="absolute left-0 right-0 top-0 bg-primary py-2 text-center text-sm font-medium text-primary-foreground">
                                    <Star className="mr-1 inline h-4 w-4" />
                                    {plan.badge}
                                </div>
                            )}

                            <CardHeader
                                className={plan.isPopular ? 'pt-12' : ''}
                            >
                                <div className="mb-2 flex items-center justify-between md:mt-6">
                                    <CardTitle className="text-xl font-bold">
                                        {plan.name}
                                    </CardTitle>
                                    {!plan.isPopular && (
                                        <Badge variant={plan.badgeVariant}>
                                            {plan.badge}
                                        </Badge>
                                    )}
                                </div>

                                <div className="mb-4">
                                    <div className="flex items-baseline">
                                        {plan.priceNote && (
                                            <span className="mr-1 text-sm text-muted-foreground">
                                                {plan.priceNote}
                                            </span>
                                        )}
                                        <span className="text-3xl font-bold text-foreground">
                                            {plan.price}
                                        </span>
                                        {plan.priceUnit && (
                                            <span className="ml-1 text-sm text-muted-foreground">
                                                {plan.priceUnit}
                                            </span>
                                        )}
                                    </div>
                                </div>

                                <p className="text-sm text-muted-foreground">
                                    {plan.description}
                                </p>
                            </CardHeader>

                            <CardContent className="flex-grow space-y-6">
                                {/* Features List */}
                                <ul className="space-y-3">
                                    {plan.features.map(
                                        (feature, featureIndex) => (
                                            <li
                                                key={featureIndex}
                                                className="flex items-start"
                                            >
                                                <Check className="mr-3 mt-0.5 h-5 w-5 flex-shrink-0 text-green-500" />
                                                <span className="text-sm text-foreground">
                                                    {feature}
                                                </span>
                                            </li>
                                        ),
                                    )}
                                </ul>
                            </CardContent>
                            <CardFooter className="mt-auto">
                                {/* CTA Button */}
                                <div className="w-full pt-4">
                                    <Button
                                        variant={plan.buttonVariant}
                                        className="w-full"
                                        size="lg"
                                        onClick={() => handlePlanClick(plan)}
                                    >
                                        {plan.buttonIcon}
                                        {plan.buttonText}
                                    </Button>
                                </div>
                            </CardFooter>
                        </Card>
                    ))}
                </div>

                {/* Footer Text */}
                <div className="mt-12 text-center text-sm text-muted-foreground">
                    <p>
                        All plans include regular updates and access to our
                        community.
                        <br />
                        Need a custom solution?{' '}
                        <button
                            onClick={() => setShowFeedbackModal(true)}
                            className="cursor-pointer border-none bg-transparent p-0 text-primary hover:underline"
                        >
                            Contact us
                        </button>{' '}
                        for enterprise options.
                    </p>
                </div>
            </div>

            {/* Seat Selection Modal */}
            <SeatSelectionModal
                isOpen={showSeatModal}
                onClose={() => setShowSeatModal(false)}
            />

            {/* Feedback Modal */}
            <FeedbackModal
                isOpen={showFeedbackModal}
                onClose={() => setShowFeedbackModal(false)}
            />

            {/* Subscription Success Modal */}
            <SubscriptionSuccessModal
                isOpen={showSuccessModal}
                onClose={() => setShowSuccessModal(false)}
            />

            {/* Toaster for notifications */}
            <Toaster />
        </div>
    );
}
