import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import { useForm } from '@inertiajs/react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';

export default function ShipperNotes({ shipper }) {
    const { data, setData, post, processing, errors } = useForm({
        content: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('notes.store', { notableType: 'shipper', notableId: shipper.id }), {
            preserveScroll: true,
            onSuccess: () => setData('content', ''),
        });
    };

    return (
        <div className="space-y-4">
            <form onSubmit={handleSubmit} className="space-y-4">
                <Textarea
                    value={data.content}
                    onChange={(e) => setData('content', e.target.value)}
                    placeholder="Add a note..."
                    error={errors.content}
                />
                <div className="flex justify-end">
                    <Button type="submit" disabled={processing}>
                        Add Note
                    </Button>
                </div>
            </form>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Date</TableHead>
                        <TableHead>Note</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {shipper.notes.map(note => (
                        <TableRow key={note.id}>
                            <TableCell>{new Date(note.created_at).toLocaleString()}</TableCell>
                            <TableCell>{note.content}</TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
} 