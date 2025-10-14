import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { Download, Filter, Search } from 'lucide-react';
import { useMemo, useState } from 'react';
import AuditFieldValue from './AuditFieldValue';

interface AuditChange {
    field: string;
    field_name: string;
    old_value: unknown;
    new_value: unknown;
}

interface AuditEntry {
    id: number;
    event: string;
    auditable_type?: string;
    auditable_id?: number;
    entity_type?: string;
    entity_name?: string;
    user: {
        id: number;
        name: string;
        email: string;
    } | null;
    old_values: Record<string, unknown>;
    new_values: Record<string, unknown>;
    changes: AuditChange[];
    created_at: string;
    created_at_human: string;
    ip_address?: string;
    user_agent?: string;
}

interface AuditTableProps {
    audits: AuditEntry[];
    loading?: boolean;
}

export default function AuditTable({ audits, loading }: AuditTableProps) {
    const [searchTerm, setSearchTerm] = useState('');
    const [eventFilter, setEventFilter] = useState<string>('all');
    const [userFilter, setUserFilter] = useState<string>('all');
    const [sortField, setSortField] = useState<'created_at' | 'event' | 'user'>(
        'created_at',
    );
    const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>('desc');

    // Get unique users for filter
    const uniqueUsers = useMemo(() => {
        const users = audits
            .filter((audit) => audit.user)
            .map((audit) => audit.user!)
            .filter(
                (user, index, self) =>
                    self.findIndex((u) => u.id === user.id) === index,
            );
        return users;
    }, [audits]);

    // Filter and sort audits
    const filteredAndSortedAudits = useMemo(() => {
        const filtered = audits.filter((audit) => {
            // Search filter
            if (searchTerm) {
                const searchLower = searchTerm.toLowerCase();
                const matchesUser =
                    audit.user?.name.toLowerCase().includes(searchLower) ||
                    audit.user?.email.toLowerCase().includes(searchLower);
                const matchesChanges = audit.changes.some(
                    (change) =>
                        change.field.toLowerCase().includes(searchLower) ||
                        String(change.old_value)
                            .toLowerCase()
                            .includes(searchLower) ||
                        String(change.new_value)
                            .toLowerCase()
                            .includes(searchLower),
                );
                if (!matchesUser && !matchesChanges) return false;
            }

            // Event filter
            if (eventFilter !== 'all' && audit.event !== eventFilter)
                return false;

            // User filter
            if (
                userFilter !== 'all' &&
                audit.user?.id.toString() !== userFilter
            )
                return false;

            return true;
        });

        // Sort
        filtered.sort((a, b) => {
            let aValue: string | number;
            let bValue: string | number;

            switch (sortField) {
                case 'created_at':
                    aValue = new Date(a.created_at).getTime();
                    bValue = new Date(b.created_at).getTime();
                    break;
                case 'event':
                    aValue = a.event;
                    bValue = b.event;
                    break;
                case 'user':
                    aValue = a.user?.name || '';
                    bValue = b.user?.name || '';
                    break;
                default:
                    aValue = new Date(a.created_at).getTime();
                    bValue = new Date(b.created_at).getTime();
            }

            if (sortDirection === 'asc') {
                return aValue > bValue ? 1 : -1;
            } else {
                return aValue < bValue ? 1 : -1;
            }
        });

        return filtered;
    }, [audits, searchTerm, eventFilter, userFilter, sortField, sortDirection]);

    const handleSort = (field: 'created_at' | 'event' | 'user') => {
        if (sortField === field) {
            setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
        } else {
            setSortField(field);
            setSortDirection('desc');
        }
    };

    const getEventBadgeVariant = (event: string) => {
        switch (event) {
            case 'created':
                return 'default';
            case 'updated':
                return 'secondary';
            case 'deleted':
                return 'destructive';
            default:
                return 'outline';
        }
    };

    const formatValue = (value: unknown): string => {
        if (value === null || value === undefined) {
            return 'No value';
        }
        if (typeof value === 'boolean') {
            return value ? 'Yes' : 'No';
        }
        if (typeof value === 'object') {
            return JSON.stringify(value);
        }
        return String(value);
    };

    const exportToCsv = () => {
        const headers = ['Date', 'Event', 'User', 'Entity', 'Changes'];
        const csvData = filteredAndSortedAudits.map((audit) => [
            new Date(audit.created_at).toLocaleString(),
            audit.event,
            audit.user?.name || 'System',
            audit.entity_type &&
            (audit.entity_type === 'Document' ||
                audit.entity_type === 'Contact')
                ? `${audit.entity_type}: ${audit.entity_name}`
                : '-',
            audit.changes
                .map(
                    (c) =>
                        `${c.field}: ${formatValue(c.old_value)} → ${formatValue(c.new_value)}`,
                )
                .join('; '),
        ]);

        const csvContent = [headers, ...csvData]
            .map((row) => row.map((field) => `"${field}"`).join(','))
            .join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `audit-log-${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
    };

    if (loading) {
        return (
            <Card>
                <CardContent className="p-6">
                    <div className="animate-pulse space-y-4">
                        <div className="h-4 w-1/4 rounded bg-gray-200"></div>
                        <div className="space-y-2">
                            {[...Array(5)].map((_, i) => (
                                <div
                                    key={i}
                                    className="h-12 rounded bg-gray-200"
                                ></div>
                            ))}
                        </div>
                    </div>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardContent className="p-6">
                {/* Filters and Actions */}
                <div className="mb-6 flex flex-col gap-4">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <Filter className="h-4 w-4 text-gray-500" />
                            <span className="text-sm font-medium">Filters</span>
                        </div>
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={exportToCsv}
                            disabled={filteredAndSortedAudits.length === 0}
                        >
                            <Download className="mr-2 h-4 w-4" />
                            Export CSV
                        </Button>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
                        {/* Search */}
                        <div className="relative">
                            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 transform text-gray-500" />
                            <Input
                                placeholder="Search audits..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className="pl-10"
                            />
                        </div>

                        {/* Event Filter */}
                        <Select
                            value={eventFilter}
                            onValueChange={setEventFilter}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="All events" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All Events</SelectItem>
                                <SelectItem value="created">Created</SelectItem>
                                <SelectItem value="updated">Updated</SelectItem>
                                <SelectItem value="deleted">Deleted</SelectItem>
                            </SelectContent>
                        </Select>

                        {/* User Filter */}
                        <Select
                            value={userFilter}
                            onValueChange={setUserFilter}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="All users" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All Users</SelectItem>
                                {uniqueUsers.map((user) => (
                                    <SelectItem
                                        key={user.id}
                                        value={user.id.toString()}
                                    >
                                        {user.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>

                        {/* Results count */}
                        <div className="flex items-center text-sm text-gray-500">
                            {filteredAndSortedAudits.length} of {audits.length}{' '}
                            entries
                        </div>
                    </div>
                </div>

                {/* Table */}
                <div className="rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead
                                    className="cursor-pointer hover:bg-gray-50"
                                    onClick={() => handleSort('created_at')}
                                >
                                    <div className="flex items-center gap-2">
                                        Date/Time
                                        {sortField === 'created_at' && (
                                            <span className="text-xs">
                                                {sortDirection === 'asc'
                                                    ? '↑'
                                                    : '↓'}
                                            </span>
                                        )}
                                    </div>
                                </TableHead>
                                <TableHead
                                    className="cursor-pointer hover:bg-gray-50"
                                    onClick={() => handleSort('event')}
                                >
                                    <div className="flex items-center gap-2">
                                        Event
                                        {sortField === 'event' && (
                                            <span className="text-xs">
                                                {sortDirection === 'asc'
                                                    ? '↑'
                                                    : '↓'}
                                            </span>
                                        )}
                                    </div>
                                </TableHead>
                                <TableHead
                                    className="cursor-pointer hover:bg-gray-50"
                                    onClick={() => handleSort('user')}
                                >
                                    <div className="flex items-center gap-2">
                                        User
                                        {sortField === 'user' && (
                                            <span className="text-xs">
                                                {sortDirection === 'asc'
                                                    ? '↑'
                                                    : '↓'}
                                            </span>
                                        )}
                                    </div>
                                </TableHead>
                                <TableHead>Entity</TableHead>
                                <TableHead>Changes</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filteredAndSortedAudits.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={5}
                                        className="py-8 text-center text-gray-500"
                                    >
                                        {audits.length === 0
                                            ? 'No audit records found'
                                            : 'No records match your filters'}
                                    </TableCell>
                                </TableRow>
                            ) : (
                                filteredAndSortedAudits.map((audit) => (
                                    <TableRow key={audit.id}>
                                        <TableCell>
                                            <div>
                                                <div className="font-medium">
                                                    {new Date(
                                                        audit.created_at,
                                                    ).toLocaleDateString()}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {new Date(
                                                        audit.created_at,
                                                    ).toLocaleTimeString()}
                                                </div>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                variant={getEventBadgeVariant(
                                                    audit.event,
                                                )}
                                            >
                                                {audit.event}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {audit.user ? (
                                                <div>
                                                    <div className="font-medium">
                                                        {audit.user.name}
                                                    </div>
                                                    <div className="text-sm text-gray-500">
                                                        {audit.user.email}
                                                    </div>
                                                </div>
                                            ) : (
                                                <span className="text-gray-500">
                                                    System
                                                </span>
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            {audit.entity_type &&
                                            (audit.entity_type === 'Document' ||
                                                audit.entity_type ===
                                                    'Contact') ? (
                                                <div>
                                                    <Badge
                                                        variant="secondary"
                                                        className="text-xs"
                                                    >
                                                        {audit.entity_type}
                                                    </Badge>
                                                    <div className="mt-1 max-w-[120px] truncate text-xs text-gray-500">
                                                        {audit.entity_name}
                                                    </div>
                                                </div>
                                            ) : (
                                                <span className="text-xs text-gray-500">
                                                    -
                                                </span>
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            <div className="space-y-2">
                                                {audit.changes.length === 0 ? (
                                                    <span className="text-gray-500">
                                                        No changes
                                                    </span>
                                                ) : (
                                                    audit.changes.map(
                                                        (
                                                            change,
                                                            changeIndex,
                                                        ) => (
                                                            <div
                                                                key={
                                                                    changeIndex
                                                                }
                                                                className="border-l-2 border-gray-200 pl-3"
                                                            >
                                                                <div className="mb-1 text-sm font-medium text-gray-700">
                                                                    {
                                                                        change.field
                                                                    }
                                                                </div>
                                                                <div className="flex items-center gap-2 text-sm">
                                                                    <span className="text-red-600">
                                                                        <AuditFieldValue
                                                                            fieldName={
                                                                                change.field_name
                                                                            }
                                                                            value={
                                                                                change.old_value
                                                                            }
                                                                            auditUser={
                                                                                audit.user
                                                                            }
                                                                        />
                                                                    </span>
                                                                    <span className="text-gray-400">
                                                                        →
                                                                    </span>
                                                                    <span className="text-green-600">
                                                                        <AuditFieldValue
                                                                            fieldName={
                                                                                change.field_name
                                                                            }
                                                                            value={
                                                                                change.new_value
                                                                            }
                                                                            auditUser={
                                                                                audit.user
                                                                            }
                                                                        />
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        ),
                                                    )
                                                )}
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    );
}
