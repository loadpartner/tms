import { Button } from '@/Components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';

export default function ShipperLocations({ shipper }) {
    return (
        <div className="space-y-4">
            <div className="flex justify-end">
                <Button>Add Location</Button>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Address</TableHead>
                        <TableHead>City</TableHead>
                        <TableHead>State</TableHead>
                        <TableHead>Zip</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {shipper.locations.map(location => (
                        <TableRow key={location.id}>
                            <TableCell>{location.name}</TableCell>
                            <TableCell>{location.address}</TableCell>
                            <TableCell>{location.city}</TableCell>
                            <TableCell>{location.state}</TableCell>
                            <TableCell>{location.zip}</TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
} 