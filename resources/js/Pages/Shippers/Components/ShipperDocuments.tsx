import { Button } from '@/Components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';

export default function ShipperDocuments({ shipper }) {
    return (
        <div className="space-y-4">
            <div className="flex justify-end">
                <Button>Upload Document</Button>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Type</TableHead>
                        <TableHead>Uploaded At</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {shipper.documents.map(document => (
                        <TableRow key={document.id}>
                            <TableCell>{document.name}</TableCell>
                            <TableCell>{document.type}</TableCell>
                            <TableCell>{new Date(document.created_at).toLocaleString()}</TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
} 