import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';

export default function ShipperGeneral({ shipper }) {
    return (
        <Card>
            <CardHeader>
                <CardTitle>General Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
                <div className="space-y-2">
                    <Label>Name</Label>
                    <p>{shipper.name}</p>
                </div>
                <div className="space-y-2">
                    <Label>MC Number</Label>
                    <p>{shipper.mc_number || 'N/A'}</p>
                </div>
                <div className="space-y-2">
                    <Label>DOT Number</Label>
                    <p>{shipper.dot_number || 'N/A'}</p>
                </div>
                <div className="space-y-2">
                    <Label>Status</Label>
                    <p>{shipper.status}</p>
                </div>
            </CardContent>
        </Card>
    );
} 