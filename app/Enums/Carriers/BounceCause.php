<?php

namespace App\Enums\Carriers;

enum BounceCause: string
{
    case RATE_DISAGREEMENT_RATE_TOO_LOW = 'Rate disagreement/rate too low';
    case EQUIPMENT_UNAVAILABILITY = 'Equipment unavailability';
    case DRIVER_ILLNESS_EMERGENCY = 'Driver illness/emergency';
    case MECHANICAL_BREAKDOWN = 'Mechanical breakdown';
    case WEATHER_CONDITIONS = 'Weather conditions';
    case DOUBLE_BOOKED_EQUIPMENT = 'Double-booked equipment';
    case DETENTION_AT_PREVIOUS_LOAD = 'Detention at previous load';
    case DRIVER_HOURS_OF_SERVICE_LIMITATIONS = 'Driver hours of service limitations';
    case PAPERWORK_DOCUMENTATION_ISSUES = 'Paperwork/documentation issues';
    case PERMIT_AUTHORITY_ISSUES = 'Permit/authority issues';
    case LOAD_DIMENSIONS_WEIGHT_DISCREPANCY = 'Load dimensions/weight discrepancy';
    case LOADING_UNLOADING_TIME_EXTENDED = 'Loading/unloading time extended';
    case FACILITY_ACCESS_ISSUES = 'Facility access issues';
    case INSURANCE_COVERAGE_INADQUATE = 'Insurance coverage inadequate';
    case SCHEDULING_CONFLICT = 'Scheduling conflict';
    case BETTER_PAYING_LOAD_ACCEPTED = 'Better-paying load accepted';
    case FUEL_COST_CONCERNS = 'Fuel cost concerns';
    case ROUTE_CONCERNS_DEADHEAD_TOO_LONG = 'Route concerns/deadhead too long';
    case CANCELED_BY_SHIPPER = 'Canceled by shipper';
    case COMMUNICATION_BREAKDOWN = 'Communication breakdown';
}