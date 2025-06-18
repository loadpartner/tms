<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Http;
use LoadPartner\FmcsaSaferApi\FmcsaSafer;

class FmcsaService extends FmcsaSafer
{

    const BASE_URL = 'https://mobile.fmcsa.dot.gov/qc/services/';

    public function __construct()
    {
        parent::__construct(config('fmcsa.api_key'));
    }
}