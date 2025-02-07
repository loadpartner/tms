import { Skeleton } from '@/Components/ui/skeleton';

const ShipperList = () => {
    return (
        <div className="shipper-list-page">
            <h1>Shipper Management</h1>
            <div className="shipper-table">
                {/* Table of shippers with search bar */}
                <Skeleton />
            </div>
            <div className="shipper-details">
                {/* Detailed information about the selected shipper */}
                <Skeleton />
            </div>
        </div>
    );
};

export default ShipperList; 