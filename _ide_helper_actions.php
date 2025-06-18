<?php

namespace App\Actions\Accounting;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob()
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean)
 * @method static dispatchSync()
 * @method static dispatchNow()
 * @method static dispatchAfterResponse()
 * @method static \Illuminate\Database\Eloquent\Collection run()
 */
class GetRateTypes
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, array $payables)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, array $payables)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, array $payables)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $payables)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $payables)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, array $payables)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, array $payables)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, array $payables)
 * @method static \Illuminate\Support\Collection run(\App\Models\Shipments\Shipment $shipment, array $payables)
 */
class SavePayables
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, array $receivables)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, array $receivables)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, array $receivables)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $receivables)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $receivables)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, array $receivables)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, array $receivables)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, array $receivables)
 * @method static \Illuminate\Support\Collection run(\App\Models\Shipments\Shipment $shipment, array $receivables)
 */
class SaveReceivables
{
}
namespace App\Actions\Carriers;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, \App\Enums\Carriers\BounceCause|string $bounceCause, ?string $reason = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, \App\Enums\Carriers\BounceCause|string $bounceCause, ?string $reason = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, \App\Enums\Carriers\BounceCause|string $bounceCause, ?string $reason = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, \App\Enums\Carriers\BounceCause|string $bounceCause, ?string $reason = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, \App\Enums\Carriers\BounceCause|string $bounceCause, ?string $reason = null)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, \App\Enums\Carriers\BounceCause|string $bounceCause, ?string $reason = null)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, \App\Enums\Carriers\BounceCause|string $bounceCause, ?string $reason = null)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, \App\Enums\Carriers\BounceCause|string $bounceCause, ?string $reason = null)
 * @method static \App\Models\Carriers\CarrierBounce run(\App\Models\Shipments\Shipment $shipment, \App\Enums\Carriers\BounceCause|string $bounceCause, ?string $reason = null)
 */
class BounceCarrier
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $name)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $name)
 * @method static dispatchSync(string $name)
 * @method static dispatchNow(string $name)
 * @method static dispatchAfterResponse(string $name)
 * @method static \App\Models\Carriers\Carrier run(string $name)
 */
class CreateCarrier
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Carriers\CarrierSaferReport $carrierSaferReport)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Carriers\CarrierSaferReport $carrierSaferReport)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Carriers\CarrierSaferReport $carrierSaferReport)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Carriers\CarrierSaferReport $carrierSaferReport)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Carriers\CarrierSaferReport $carrierSaferReport)
 * @method static dispatchSync(\App\Models\Carriers\CarrierSaferReport $carrierSaferReport)
 * @method static dispatchNow(\App\Models\Carriers\CarrierSaferReport $carrierSaferReport)
 * @method static dispatchAfterResponse(\App\Models\Carriers\CarrierSaferReport $carrierSaferReport)
 * @method static \App\Models\Carriers\Carrier run(\App\Models\Carriers\CarrierSaferReport $carrierSaferReport)
 */
class CreateCarrierFromSaferReport
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $dotNumber)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $dotNumber)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $dotNumber)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $dotNumber)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $dotNumber)
 * @method static dispatchSync(string $dotNumber)
 * @method static dispatchNow(string $dotNumber)
 * @method static dispatchAfterResponse(string $dotNumber)
 * @method static ?\App\Models\Carriers\CarrierSaferReport run(string $dotNumber)
 */
class FmcsaDOTLookup
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $name)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $name)
 * @method static dispatchSync(string $name)
 * @method static dispatchNow(string $name)
 * @method static dispatchAfterResponse(string $name)
 * @method static \Illuminate\Support\Collection run(string $name)
 */
class FmcsaNameLookup
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Carriers\Carrier $carrier, ?string $name = null, ?string $mc_number = null, ?string $dot_number = null, ?int $physical_location_id = null, ?string $contact_email = null, ?string $contact_phone = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Carriers\Carrier $carrier, ?string $name = null, ?string $mc_number = null, ?string $dot_number = null, ?int $physical_location_id = null, ?string $contact_email = null, ?string $contact_phone = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Carriers\Carrier $carrier, ?string $name = null, ?string $mc_number = null, ?string $dot_number = null, ?int $physical_location_id = null, ?string $contact_email = null, ?string $contact_phone = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Carriers\Carrier $carrier, ?string $name = null, ?string $mc_number = null, ?string $dot_number = null, ?int $physical_location_id = null, ?string $contact_email = null, ?string $contact_phone = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Carriers\Carrier $carrier, ?string $name = null, ?string $mc_number = null, ?string $dot_number = null, ?int $physical_location_id = null, ?string $contact_email = null, ?string $contact_phone = null)
 * @method static dispatchSync(\App\Models\Carriers\Carrier $carrier, ?string $name = null, ?string $mc_number = null, ?string $dot_number = null, ?int $physical_location_id = null, ?string $contact_email = null, ?string $contact_phone = null)
 * @method static dispatchNow(\App\Models\Carriers\Carrier $carrier, ?string $name = null, ?string $mc_number = null, ?string $dot_number = null, ?int $physical_location_id = null, ?string $contact_email = null, ?string $contact_phone = null)
 * @method static dispatchAfterResponse(\App\Models\Carriers\Carrier $carrier, ?string $name = null, ?string $mc_number = null, ?string $dot_number = null, ?int $physical_location_id = null, ?string $contact_email = null, ?string $contact_phone = null)
 * @method static \App\Models\Carriers\Carrier run(\App\Models\Carriers\Carrier $carrier, ?string $name = null, ?string $mc_number = null, ?string $dot_number = null, ?int $physical_location_id = null, ?string $contact_email = null, ?string $contact_phone = null)
 */
class UpdateCarrierGeneral
{
}
namespace App\Actions\CheckCalls;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(int $shipmentId, ?string $eta, ?int $reportedTrailerTemp, string $contactName, ?\App\Enums\ContactMethodType $contactMethod, ?string $contactMethodDetail, ?bool $isLate, ?bool $isTruckEmpty, ?string $note, ?string $arrivedAt, ?string $leftAt, ?string $loadedUnloadedAt)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(int $shipmentId, ?string $eta, ?int $reportedTrailerTemp, string $contactName, ?\App\Enums\ContactMethodType $contactMethod, ?string $contactMethodDetail, ?bool $isLate, ?bool $isTruckEmpty, ?string $note, ?string $arrivedAt, ?string $leftAt, ?string $loadedUnloadedAt)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(int $shipmentId, ?string $eta, ?int $reportedTrailerTemp, string $contactName, ?\App\Enums\ContactMethodType $contactMethod, ?string $contactMethodDetail, ?bool $isLate, ?bool $isTruckEmpty, ?string $note, ?string $arrivedAt, ?string $leftAt, ?string $loadedUnloadedAt)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, int $shipmentId, ?string $eta, ?int $reportedTrailerTemp, string $contactName, ?\App\Enums\ContactMethodType $contactMethod, ?string $contactMethodDetail, ?bool $isLate, ?bool $isTruckEmpty, ?string $note, ?string $arrivedAt, ?string $leftAt, ?string $loadedUnloadedAt)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, int $shipmentId, ?string $eta, ?int $reportedTrailerTemp, string $contactName, ?\App\Enums\ContactMethodType $contactMethod, ?string $contactMethodDetail, ?bool $isLate, ?bool $isTruckEmpty, ?string $note, ?string $arrivedAt, ?string $leftAt, ?string $loadedUnloadedAt)
 * @method static dispatchSync(int $shipmentId, ?string $eta, ?int $reportedTrailerTemp, string $contactName, ?\App\Enums\ContactMethodType $contactMethod, ?string $contactMethodDetail, ?bool $isLate, ?bool $isTruckEmpty, ?string $note, ?string $arrivedAt, ?string $leftAt, ?string $loadedUnloadedAt)
 * @method static dispatchNow(int $shipmentId, ?string $eta, ?int $reportedTrailerTemp, string $contactName, ?\App\Enums\ContactMethodType $contactMethod, ?string $contactMethodDetail, ?bool $isLate, ?bool $isTruckEmpty, ?string $note, ?string $arrivedAt, ?string $leftAt, ?string $loadedUnloadedAt)
 * @method static dispatchAfterResponse(int $shipmentId, ?string $eta, ?int $reportedTrailerTemp, string $contactName, ?\App\Enums\ContactMethodType $contactMethod, ?string $contactMethodDetail, ?bool $isLate, ?bool $isTruckEmpty, ?string $note, ?string $arrivedAt, ?string $leftAt, ?string $loadedUnloadedAt)
 * @method static \App\Models\CheckCalls\CheckCall run(int $shipmentId, ?string $eta, ?int $reportedTrailerTemp, string $contactName, ?\App\Enums\ContactMethodType $contactMethod, ?string $contactMethodDetail, ?bool $isLate, ?bool $isTruckEmpty, ?string $note, ?string $arrivedAt, ?string $leftAt, ?string $loadedUnloadedAt)
 */
class CreateCheckCall
{
}
namespace App\Actions\Contacts;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static dispatchSync(string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static dispatchNow(string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static dispatchAfterResponse(string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static \App\Models\Contact run(string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 */
class CreateContact
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Contact $contact)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Contact $contact)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Contact $contact)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Contact $contact)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Contact $contact)
 * @method static dispatchSync(\App\Models\Contact $contact)
 * @method static dispatchNow(\App\Models\Contact $contact)
 * @method static dispatchAfterResponse(\App\Models\Contact $contact)
 * @method static \App\Models\Contact run(\App\Models\Contact $contact)
 */
class DeleteContact
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Enums\Contactable $contactable)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Enums\Contactable $contactable)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Enums\Contactable $contactable)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Enums\Contactable $contactable)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Enums\Contactable $contactable)
 * @method static dispatchSync(\App\Enums\Contactable $contactable)
 * @method static dispatchNow(\App\Enums\Contactable $contactable)
 * @method static dispatchAfterResponse(\App\Enums\Contactable $contactable)
 * @method static array run(\App\Enums\Contactable $contactable)
 */
class GetContactTypes
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Contact $contact, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Contact $contact, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Contact $contact, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Contact $contact, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Contact $contact, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static dispatchSync(\App\Models\Contact $contact, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static dispatchNow(\App\Models\Contact $contact, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static dispatchAfterResponse(\App\Models\Contact $contact, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static \App\Models\Contact run(\App\Models\Contact $contact, string $name, string $contact_type, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 */
class UpdateContact
{
}
namespace App\Actions\Customers;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $name)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $name)
 * @method static dispatchSync(string $name)
 * @method static dispatchNow(string $name)
 * @method static dispatchAfterResponse(string $name)
 * @method static \App\Models\Customers\Customer run(string $name)
 */
class CreateCustomer
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(int $customer_id, int $facility_id)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(int $customer_id, int $facility_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(int $customer_id, int $facility_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, int $customer_id, int $facility_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, int $customer_id, int $facility_id)
 * @method static dispatchSync(int $customer_id, int $facility_id)
 * @method static dispatchNow(int $customer_id, int $facility_id)
 * @method static dispatchAfterResponse(int $customer_id, int $facility_id)
 * @method static \App\Models\Facility run(int $customer_id, int $facility_id)
 */
class CreateCustomerFacility
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(int $customerId, int $facilityId)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(int $customerId, int $facilityId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(int $customerId, int $facilityId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, int $customerId, int $facilityId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, int $customerId, int $facilityId)
 * @method static dispatchSync(int $customerId, int $facilityId)
 * @method static dispatchNow(int $customerId, int $facilityId)
 * @method static dispatchAfterResponse(int $customerId, int $facilityId)
 * @method static \App\Models\Facility run(int $customerId, int $facilityId)
 */
class DeleteCustomerFacility
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Customers\Customer $customer, ?string $name = null, ?int $net_pay_days = null, ?int $billing_location_id = null, ?string $dba_name = null, ?string $invoice_number_schema = null, ?int $billing_contact_id = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Customers\Customer $customer, ?string $name = null, ?int $net_pay_days = null, ?int $billing_location_id = null, ?string $dba_name = null, ?string $invoice_number_schema = null, ?int $billing_contact_id = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Customers\Customer $customer, ?string $name = null, ?int $net_pay_days = null, ?int $billing_location_id = null, ?string $dba_name = null, ?string $invoice_number_schema = null, ?int $billing_contact_id = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Customers\Customer $customer, ?string $name = null, ?int $net_pay_days = null, ?int $billing_location_id = null, ?string $dba_name = null, ?string $invoice_number_schema = null, ?int $billing_contact_id = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Customers\Customer $customer, ?string $name = null, ?int $net_pay_days = null, ?int $billing_location_id = null, ?string $dba_name = null, ?string $invoice_number_schema = null, ?int $billing_contact_id = null)
 * @method static dispatchSync(\App\Models\Customers\Customer $customer, ?string $name = null, ?int $net_pay_days = null, ?int $billing_location_id = null, ?string $dba_name = null, ?string $invoice_number_schema = null, ?int $billing_contact_id = null)
 * @method static dispatchNow(\App\Models\Customers\Customer $customer, ?string $name = null, ?int $net_pay_days = null, ?int $billing_location_id = null, ?string $dba_name = null, ?string $invoice_number_schema = null, ?int $billing_contact_id = null)
 * @method static dispatchAfterResponse(\App\Models\Customers\Customer $customer, ?string $name = null, ?int $net_pay_days = null, ?int $billing_location_id = null, ?string $dba_name = null, ?string $invoice_number_schema = null, ?int $billing_contact_id = null)
 * @method static \App\Models\Customers\Customer run(\App\Models\Customers\Customer $customer, ?string $name = null, ?int $net_pay_days = null, ?int $billing_location_id = null, ?string $dba_name = null, ?string $invoice_number_schema = null, ?int $billing_contact_id = null)
 */
class UpdateCustomer
{
}
namespace App\Actions\Dashboard;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob()
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean)
 * @method static dispatchSync()
 * @method static dispatchNow()
 * @method static dispatchAfterResponse()
 * @method static \Illuminate\Support\Collection run()
 */
class RecentCarriersCard
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob()
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean)
 * @method static dispatchSync()
 * @method static dispatchNow()
 * @method static dispatchAfterResponse()
 * @method static \Illuminate\Support\Collection run()
 */
class RecentShipmentsCard
{
}
namespace App\Actions\Defaults;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(int $organizationId)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(int $organizationId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(int $organizationId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, int $organizationId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, int $organizationId)
 * @method static dispatchSync(int $organizationId)
 * @method static dispatchNow(int $organizationId)
 * @method static dispatchAfterResponse(int $organizationId)
 * @method static mixed run(int $organizationId)
 */
class CreateOrUpdateOrganizationDefaults
{
}
namespace App\Actions\DocumentTemplates;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Enums\Documents\DocumentTemplateType $templateType)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Enums\Documents\DocumentTemplateType $templateType)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Enums\Documents\DocumentTemplateType $templateType)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Enums\Documents\DocumentTemplateType $templateType)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Enums\Documents\DocumentTemplateType $templateType)
 * @method static dispatchSync(\App\Enums\Documents\DocumentTemplateType $templateType)
 * @method static dispatchNow(\App\Enums\Documents\DocumentTemplateType $templateType)
 * @method static dispatchAfterResponse(\App\Enums\Documents\DocumentTemplateType $templateType)
 * @method static string run(\App\Enums\Documents\DocumentTemplateType $templateType)
 */
class GetDefaultTemplate
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Organizations\Organization $organization, \App\Models\Documents\DocumentTemplate $documentTemplate)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Organizations\Organization $organization, \App\Models\Documents\DocumentTemplate $documentTemplate)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Organizations\Organization $organization, \App\Models\Documents\DocumentTemplate $documentTemplate)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Organizations\Organization $organization, \App\Models\Documents\DocumentTemplate $documentTemplate)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Organizations\Organization $organization, \App\Models\Documents\DocumentTemplate $documentTemplate)
 * @method static dispatchSync(\App\Models\Organizations\Organization $organization, \App\Models\Documents\DocumentTemplate $documentTemplate)
 * @method static dispatchNow(\App\Models\Organizations\Organization $organization, \App\Models\Documents\DocumentTemplate $documentTemplate)
 * @method static dispatchAfterResponse(\App\Models\Organizations\Organization $organization, \App\Models\Documents\DocumentTemplate $documentTemplate)
 * @method static mixed run(\App\Models\Organizations\Organization $organization, \App\Models\Documents\DocumentTemplate $documentTemplate)
 */
class GetDocumentTemplate
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Organizations\Organization $organization)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Organizations\Organization $organization)
 * @method static dispatchSync(\App\Models\Organizations\Organization $organization)
 * @method static dispatchNow(\App\Models\Organizations\Organization $organization)
 * @method static dispatchAfterResponse(\App\Models\Organizations\Organization $organization)
 * @method static mixed run(\App\Models\Organizations\Organization $organization)
 */
class GetDocumentTemplates
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Organizations\Organization $organization, array $data)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Organizations\Organization $organization, array $data)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Organizations\Organization $organization, array $data)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Organizations\Organization $organization, array $data)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Organizations\Organization $organization, array $data)
 * @method static dispatchSync(\App\Models\Organizations\Organization $organization, array $data)
 * @method static dispatchNow(\App\Models\Organizations\Organization $organization, array $data)
 * @method static dispatchAfterResponse(\App\Models\Organizations\Organization $organization, array $data)
 * @method static mixed run(\App\Models\Organizations\Organization $organization, array $data)
 */
class SaveDocumentTemplate
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $template)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $template)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $template)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $template)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $template)
 * @method static dispatchSync(string $template)
 * @method static dispatchNow(string $template)
 * @method static dispatchAfterResponse(string $template)
 * @method static array run(string $template)
 */
class ValidateTemplate
{
}
namespace App\Actions\Documents;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $documentableType, int $documentableId, string $fileName, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, ?string $folderName = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $documentableType, int $documentableId, string $fileName, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, ?string $folderName = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $documentableType, int $documentableId, string $fileName, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, ?string $folderName = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $documentableType, int $documentableId, string $fileName, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, ?string $folderName = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $documentableType, int $documentableId, string $fileName, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, ?string $folderName = null)
 * @method static dispatchSync(string $documentableType, int $documentableId, string $fileName, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, ?string $folderName = null)
 * @method static dispatchNow(string $documentableType, int $documentableId, string $fileName, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, ?string $folderName = null)
 * @method static dispatchAfterResponse(string $documentableType, int $documentableId, string $fileName, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, ?string $folderName = null)
 * @method static mixed run(string $documentableType, int $documentableId, string $fileName, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, ?string $folderName = null)
 */
class CreateDocument
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Documents\Document $document)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Documents\Document $document)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Documents\Document $document)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Documents\Document $document)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Documents\Document $document)
 * @method static dispatchSync(\App\Models\Documents\Document $document)
 * @method static dispatchNow(\App\Models\Documents\Document $document)
 * @method static dispatchAfterResponse(\App\Models\Documents\Document $document)
 * @method static mixed run(\App\Models\Documents\Document $document)
 */
class DeleteDocument
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Documents\Document $document)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Documents\Document $document)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Documents\Document $document)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Documents\Document $document)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Documents\Document $document)
 * @method static dispatchSync(\App\Models\Documents\Document $document)
 * @method static dispatchNow(\App\Models\Documents\Document $document)
 * @method static dispatchAfterResponse(\App\Models\Documents\Document $document)
 * @method static mixed run(\App\Models\Documents\Document $document)
 */
class GetDocument
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Enums\Documents\Documentable $documentableType, string $documentableId)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Enums\Documents\Documentable $documentableType, string $documentableId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Enums\Documents\Documentable $documentableType, string $documentableId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Enums\Documents\Documentable $documentableType, string $documentableId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Enums\Documents\Documentable $documentableType, string $documentableId)
 * @method static dispatchSync(\App\Enums\Documents\Documentable $documentableType, string $documentableId)
 * @method static dispatchNow(\App\Enums\Documents\Documentable $documentableType, string $documentableId)
 * @method static dispatchAfterResponse(\App\Enums\Documents\Documentable $documentableType, string $documentableId)
 * @method static array run(\App\Enums\Documents\Documentable $documentableType, string $documentableId)
 */
class GetDocumentsWithFolders
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Documents\Document $document, ?string $fileName, ?string $folderName)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Documents\Document $document, ?string $fileName, ?string $folderName)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Documents\Document $document, ?string $fileName, ?string $folderName)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Documents\Document $document, ?string $fileName, ?string $folderName)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Documents\Document $document, ?string $fileName, ?string $folderName)
 * @method static dispatchSync(\App\Models\Documents\Document $document, ?string $fileName, ?string $folderName)
 * @method static dispatchNow(\App\Models\Documents\Document $document, ?string $fileName, ?string $folderName)
 * @method static dispatchAfterResponse(\App\Models\Documents\Document $document, ?string $fileName, ?string $folderName)
 * @method static mixed run(\App\Models\Documents\Document $document, ?string $fileName, ?string $folderName)
 */
class UpdateDocument
{
}
namespace App\Actions\Documents\Generators;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, \App\Models\Customers\Customer $customer)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, \App\Models\Customers\Customer $customer)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, \App\Models\Customers\Customer $customer)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, \App\Models\Customers\Customer $customer)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, \App\Models\Customers\Customer $customer)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, \App\Models\Customers\Customer $customer)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, \App\Models\Customers\Customer $customer)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, \App\Models\Customers\Customer $customer)
 * @method static mixed run(\App\Models\Shipments\Shipment $shipment, \App\Models\Customers\Customer $customer)
 */
class GenerateCustomerInvoice
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment)
 * @method static mixed run(\App\Models\Shipments\Shipment $shipment)
 */
class GenerateRateConfirmation
{
}
namespace App\Actions\Facilities;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $name, int $location_id)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $name, int $location_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $name, int $location_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $name, int $location_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $name, int $location_id)
 * @method static dispatchSync(string $name, int $location_id)
 * @method static dispatchNow(string $name, int $location_id)
 * @method static dispatchAfterResponse(string $name, int $location_id)
 * @method static \App\Models\Facility run(string $name, int $location_id)
 */
class CreateFacility
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Facility $facility, ?string $name = null, ?int $location_id = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Facility $facility, ?string $name = null, ?int $location_id = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Facility $facility, ?string $name = null, ?int $location_id = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Facility $facility, ?string $name = null, ?int $location_id = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Facility $facility, ?string $name = null, ?int $location_id = null)
 * @method static dispatchSync(\App\Models\Facility $facility, ?string $name = null, ?int $location_id = null)
 * @method static dispatchNow(\App\Models\Facility $facility, ?string $name = null, ?int $location_id = null)
 * @method static dispatchAfterResponse(\App\Models\Facility $facility, ?string $name = null, ?int $location_id = null)
 * @method static \App\Models\Facility run(\App\Models\Facility $facility, ?string $name = null, ?int $location_id = null)
 */
class UpdateFacility
{
}
namespace App\Actions\IntegrationSettings;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $key, ?string $provider = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $key, ?string $provider = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $key, ?string $provider = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $key, ?string $provider = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $key, ?string $provider = null)
 * @method static dispatchSync(string $key, ?string $provider = null)
 * @method static dispatchNow(string $key, ?string $provider = null)
 * @method static dispatchAfterResponse(string $key, ?string $provider = null)
 * @method static mixed run(string $key, ?string $provider = null)
 */
class ClearIntegrationCaches
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $key)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $key)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $key)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $key)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $key)
 * @method static dispatchSync(string $key)
 * @method static dispatchNow(string $key)
 * @method static dispatchAfterResponse(string $key)
 * @method static bool run(string $key)
 */
class DeleteIntegrationSetting
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob()
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean)
 * @method static dispatchSync()
 * @method static dispatchNow()
 * @method static dispatchAfterResponse()
 * @method static \Illuminate\Support\Collection run()
 */
class GetAllGlobalIntegrationSettings
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob()
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean)
 * @method static dispatchSync()
 * @method static dispatchNow()
 * @method static dispatchAfterResponse()
 * @method static \Illuminate\Support\Collection run()
 */
class GetFrontendIntegrationSettings
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $key)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $key)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $key)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $key)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $key)
 * @method static dispatchSync(string $key)
 * @method static dispatchNow(string $key)
 * @method static dispatchAfterResponse(string $key)
 * @method static mixed run(string $key)
 */
class GetIntegrationSetting
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $provider)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $provider)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $provider)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $provider)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $provider)
 * @method static dispatchSync(string $provider)
 * @method static dispatchNow(string $provider)
 * @method static dispatchAfterResponse(string $provider)
 * @method static \Illuminate\Support\Collection run(string $provider)
 */
class GetProviderSettings
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $key, mixed $value, ?string $provider = null, bool $exposeToFrontend = false)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $key, mixed $value, ?string $provider = null, bool $exposeToFrontend = false)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $key, mixed $value, ?string $provider = null, bool $exposeToFrontend = false)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $key, mixed $value, ?string $provider = null, bool $exposeToFrontend = false)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $key, mixed $value, ?string $provider = null, bool $exposeToFrontend = false)
 * @method static dispatchSync(string $key, mixed $value, ?string $provider = null, bool $exposeToFrontend = false)
 * @method static dispatchNow(string $key, mixed $value, ?string $provider = null, bool $exposeToFrontend = false)
 * @method static dispatchAfterResponse(string $key, mixed $value, ?string $provider = null, bool $exposeToFrontend = false)
 * @method static \App\Models\Organizations\IntegrationSetting run(string $key, mixed $value, ?string $provider = null, bool $exposeToFrontend = false)
 */
class SetIntegrationSetting
{
}
namespace App\Actions\Locations;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $abbreviation)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $abbreviation)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $abbreviation)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $abbreviation)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $abbreviation)
 * @method static dispatchSync(string $abbreviation)
 * @method static dispatchNow(string $abbreviation)
 * @method static dispatchAfterResponse(string $abbreviation)
 * @method static string run(string $abbreviation)
 */
class AbbreviationToState
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null, ?float $latitude = null, ?float $longitude = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null, ?float $latitude = null, ?float $longitude = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null, ?float $latitude = null, ?float $longitude = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null, ?float $latitude = null, ?float $longitude = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null, ?float $latitude = null, ?float $longitude = null)
 * @method static dispatchSync(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null, ?float $latitude = null, ?float $longitude = null)
 * @method static dispatchNow(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null, ?float $latitude = null, ?float $longitude = null)
 * @method static dispatchAfterResponse(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null, ?float $latitude = null, ?float $longitude = null)
 * @method static \App\Models\Location run(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null, ?float $latitude = null, ?float $longitude = null)
 */
class CreateLocation
{
}
namespace App\Actions\Notes;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static dispatchSync(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static dispatchNow(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static dispatchAfterResponse(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static \App\Models\Note run(string $note, string $notableType, int $notableId, ?int $userId = null)
 */
class CreateNote
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Note $note)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Note $note)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Note $note)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Note $note)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Note $note)
 * @method static dispatchSync(\App\Models\Note $note)
 * @method static dispatchNow(\App\Models\Note $note)
 * @method static dispatchAfterResponse(\App\Models\Note $note)
 * @method static \App\Models\Note run(\App\Models\Note $note)
 */
class DeleteNote
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $notableType, int $notableId)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $notableType, int $notableId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $notableType, int $notableId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $notableType, int $notableId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $notableType, int $notableId)
 * @method static dispatchSync(string $notableType, int $notableId)
 * @method static dispatchNow(string $notableType, int $notableId)
 * @method static dispatchAfterResponse(string $notableType, int $notableId)
 * @method static \Illuminate\Database\Eloquent\Collection run(string $notableType, int $notableId)
 */
class GetNotes
{
}
namespace App\Actions\Organizations;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Organizations\Organization $organization)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Organizations\Organization $organization)
 * @method static dispatchSync(\App\Models\Organizations\Organization $organization)
 * @method static dispatchNow(\App\Models\Organizations\Organization $organization)
 * @method static dispatchAfterResponse(\App\Models\Organizations\Organization $organization)
 * @method static void run(\App\Models\Organizations\Organization $organization)
 */
class CheckSeatLimits
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $email, \App\Models\Organizations\Organization $organization)
 * @method static dispatchSync(string $email, \App\Models\Organizations\Organization $organization)
 * @method static dispatchNow(string $email, \App\Models\Organizations\Organization $organization)
 * @method static dispatchAfterResponse(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \App\Models\Organizations\OrganizationInvite run(string $email, \App\Models\Organizations\Organization $organization)
 */
class SendInvite
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Organizations\Organization $organization, string $name, ?string $company_name = null, ?int $company_location_id = null, ?string $company_phone = null, ?string $company_email = null, ?string $accounting_contact_email = null, ?string $accounting_contact_phone = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Organizations\Organization $organization, string $name, ?string $company_name = null, ?int $company_location_id = null, ?string $company_phone = null, ?string $company_email = null, ?string $accounting_contact_email = null, ?string $accounting_contact_phone = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Organizations\Organization $organization, string $name, ?string $company_name = null, ?int $company_location_id = null, ?string $company_phone = null, ?string $company_email = null, ?string $accounting_contact_email = null, ?string $accounting_contact_phone = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Organizations\Organization $organization, string $name, ?string $company_name = null, ?int $company_location_id = null, ?string $company_phone = null, ?string $company_email = null, ?string $accounting_contact_email = null, ?string $accounting_contact_phone = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Organizations\Organization $organization, string $name, ?string $company_name = null, ?int $company_location_id = null, ?string $company_phone = null, ?string $company_email = null, ?string $accounting_contact_email = null, ?string $accounting_contact_phone = null)
 * @method static dispatchSync(\App\Models\Organizations\Organization $organization, string $name, ?string $company_name = null, ?int $company_location_id = null, ?string $company_phone = null, ?string $company_email = null, ?string $accounting_contact_email = null, ?string $accounting_contact_phone = null)
 * @method static dispatchNow(\App\Models\Organizations\Organization $organization, string $name, ?string $company_name = null, ?int $company_location_id = null, ?string $company_phone = null, ?string $company_email = null, ?string $accounting_contact_email = null, ?string $accounting_contact_phone = null)
 * @method static dispatchAfterResponse(\App\Models\Organizations\Organization $organization, string $name, ?string $company_name = null, ?int $company_location_id = null, ?string $company_phone = null, ?string $company_email = null, ?string $accounting_contact_email = null, ?string $accounting_contact_phone = null)
 * @method static \App\Models\Organizations\Organization run(\App\Models\Organizations\Organization $organization, string $name, ?string $company_name = null, ?int $company_location_id = null, ?string $company_phone = null, ?string $company_email = null, ?string $accounting_contact_email = null, ?string $accounting_contact_phone = null)
 */
class UpdateOrganization
{
}
namespace App\Actions\Shipments;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment)
 */
class CancelShipment
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(array $customerIds, array $stops, ?int $carrierId = null, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(array $customerIds, array $stops, ?int $carrierId = null, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(array $customerIds, array $stops, ?int $carrierId = null, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, array $customerIds, array $stops, ?int $carrierId = null, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, array $customerIds, array $stops, ?int $carrierId = null, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchSync(array $customerIds, array $stops, ?int $carrierId = null, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchNow(array $customerIds, array $stops, ?int $carrierId = null, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchAfterResponse(array $customerIds, array $stops, ?int $carrierId = null, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \App\Models\Shipments\Shipment run(array $customerIds, array $stops, ?int $carrierId = null, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 */
class CreateShipment
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment)
 */
class DispatchShipment
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment)
 */
class GetShipmentAccounting
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, ?\App\Services\Shipments\ShipmentStateService $stateService = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, ?\App\Services\Shipments\ShipmentStateService $stateService = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, ?\App\Services\Shipments\ShipmentStateService $stateService = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?\App\Services\Shipments\ShipmentStateService $stateService = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?\App\Services\Shipments\ShipmentStateService $stateService = null)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, ?\App\Services\Shipments\ShipmentStateService $stateService = null)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, ?\App\Services\Shipments\ShipmentStateService $stateService = null)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, ?\App\Services\Shipments\ShipmentStateService $stateService = null)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, ?\App\Services\Shipments\ShipmentStateService $stateService = null)
 */
class UncancelShipment
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, int $carrierId, ?int $driverId = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, int $carrierId, ?int $driverId = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, int $carrierId, ?int $driverId = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, int $carrierId, ?int $driverId = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, int $carrierId, ?int $driverId = null)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, int $carrierId, ?int $driverId = null)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, int $carrierId, ?int $driverId = null)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, int $carrierId, ?int $driverId = null)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, int $carrierId, ?int $driverId = null)
 */
class UpdateShipmentCarrierDetails
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 */
class UpdateShipmentCustomers
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = false)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = false)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = false)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = false)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = false)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = false)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = false)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = false)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = false)
 */
class UpdateShipmentGeneral
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 */
class UpdateShipmentNumber
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, array $stops)
 */
class UpdateShipmentStops
{
}
namespace App\Actions;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $email, string $feedback)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $email, string $feedback)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $email, string $feedback)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $email, string $feedback)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $email, string $feedback)
 * @method static dispatchSync(string $email, string $feedback)
 * @method static dispatchNow(string $email, string $feedback)
 * @method static dispatchAfterResponse(string $email, string $feedback)
 * @method static \Illuminate\Http\Response run(string $email, string $feedback)
 */
class SubmitFeedback
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string|array $zipcodes)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string|array $zipcodes)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string|array $zipcodes)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string|array $zipcodes)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string|array $zipcodes)
 * @method static dispatchSync(string|array $zipcodes)
 * @method static dispatchNow(string|array $zipcodes)
 * @method static dispatchAfterResponse(string|array $zipcodes)
 * @method static array run(string|array $zipcodes)
 */
class ZipToTimezone
{
}
namespace App\Actions\Subscriptions;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(int $quantity = 1)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(int $quantity = 1)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(int $quantity = 1)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, int $quantity = 1)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, int $quantity = 1)
 * @method static dispatchSync(int $quantity = 1)
 * @method static dispatchNow(int $quantity = 1)
 * @method static dispatchAfterResponse(int $quantity = 1)
 * @method static mixed run(int $quantity = 1)
 */
class NewUserSeatsSubscription
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob()
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch()
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean)
 * @method static dispatchSync()
 * @method static dispatchNow()
 * @method static dispatchAfterResponse()
 * @method static mixed run()
 */
class RedirectToBillingPortal
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(int $quantity)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(int $quantity)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(int $quantity)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, int $quantity)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, int $quantity)
 * @method static dispatchSync(int $quantity)
 * @method static dispatchNow(int $quantity)
 * @method static dispatchAfterResponse(int $quantity)
 * @method static mixed run(int $quantity)
 */
class UpdateUserSeatsSubscription
{
}
namespace Lorisleiva\Actions\Concerns;

/**
 * @method void asController()
 */
trait AsController
{
}
/**
 * @method void asListener()
 */
trait AsListener
{
}
/**
 * @method void asJob()
 */
trait AsJob
{
}
/**
 * @method void asCommand(\Illuminate\Console\Command $command)
 */
trait AsCommand
{
}