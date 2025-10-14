import { Card, CardContent } from '@/Components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { Clock, Table } from 'lucide-react';
import { useEffect, useState } from 'react';
import AuditTable from './AuditTable';
import AuditTimeline from './AuditTimeline';
import { AuditEntry } from './types';

interface AuditHistoryProps {
    entityId: number;
    routeName: string;
    showTabs?: boolean;
}

export default function AuditHistory({
    entityId,
    routeName,
    showTabs = true,
}: AuditHistoryProps) {
    const [audits, setAudits] = useState<AuditEntry[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [activeTab, setActiveTab] = useState('timeline');

    useEffect(() => {
        const fetchAudits = async () => {
            try {
                setLoading(true);
                const response = await fetch(route(routeName, entityId));

                if (!response.ok) {
                    throw new Error('Failed to fetch audit history');
                }

                const data = await response.json();
                setAudits(data.audits || []);
            } catch (err) {
                setError(
                    err instanceof Error ? err.message : 'An error occurred',
                );
            } finally {
                setLoading(false);
            }
        };

        if (entityId) {
            fetchAudits();
        }
    }, [entityId, routeName]);

    if (error) {
        return (
            <Card>
                <CardContent className="p-6">
                    <div className="py-8 text-center">
                        <div className="mb-2 text-red-500">
                            Error loading audit history
                        </div>
                        <div className="text-sm text-gray-500">{error}</div>
                    </div>
                </CardContent>
            </Card>
        );
    }

    if (!showTabs) {
        return <AuditTable audits={audits} loading={loading} />;
    }

    return (
        <div className="space-y-6">
            <Tabs
                value={activeTab}
                onValueChange={setActiveTab}
                className="w-full"
            >
                <TabsList className="w-full">
                    <TabsTrigger
                        value="timeline"
                        className="flex items-center gap-2"
                    >
                        <Clock className="h-4 w-4" />
                        Audit Timeline
                    </TabsTrigger>
                    <TabsTrigger
                        value="table"
                        className="flex items-center gap-2"
                    >
                        <Table className="h-4 w-4" />
                        Audit Table
                    </TabsTrigger>
                </TabsList>

                <TabsContent value="timeline" className="mt-6">
                    <AuditTimeline audits={audits} loading={loading} />
                </TabsContent>

                <TabsContent value="table" className="mt-6">
                    <AuditTable audits={audits} loading={loading} />
                </TabsContent>
            </Tabs>
        </div>
    );
}
