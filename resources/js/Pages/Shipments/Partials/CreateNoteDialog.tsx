import { router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';

interface Props {
    open: boolean;
    setOpen: (open: boolean) => void;
    shipmentId: number | null;
    shipperId: number | null;
}

const CreateNoteDialog: React.FC<Props> = ({ open, setOpen, shipmentId, shipperId }) => {
    const { data, setData, post, processing, errors, reset } = useForm({
        note: '',
        shipment_id: shipmentId,
        shipper_id: shipperId,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        let url = '';
        if (shipmentId) {
            url = route('shipments.notes.store', { shipment: shipmentId });
        } else if (shipperId) {
            url = route('shippers.notes.store', { shipper: shipperId });
        }

        router.post(url, data);
    };

    // ... (rest of the component)
};

export default CreateNoteDialog; 