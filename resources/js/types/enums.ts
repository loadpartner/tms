export enum TemperatureUnit {
    Fahrenheit = 'F',
    Celsius = 'C',
}

export enum StopType {
    Pickup = 'pickup',
    Delivery = 'delivery',
}

export enum Notable {
    CheckCall = 'check_call',
    Shipment = 'shipment',
    Customer = 'customer',
    Carrier = 'carrier',
    Facility = 'facility',
}

export enum Contactable {
    Shipment = 'shipment',
    Customer = 'customer',
    Carrier = 'carrier',
    Facility = 'facility',
}

export enum ShipmentState {
    Pending = 'pending',
    InTransit = 'in_transit',
    Delivered = 'delivered',
    Canceled = 'canceled',
    Booked = 'booked',
    AtPickup = 'at_pickup',
    AtDelivery = 'at_delivery',
    Dispatched = 'dispatched',
}

export enum Documentable {
    Shipment = 'shipment',
    Customer = 'customer',
    Carrier = 'carrier',
    Facility = 'facility',
}

export enum ContactMethodType {
    Phonecall = 'phonecall',
    Email = 'email',
    Text = 'text',
    Fax = 'fax',
    Web = 'web',
    Other = 'other',
}

export enum Currency {
    UnitedStatesDollar = 'usd',
}
