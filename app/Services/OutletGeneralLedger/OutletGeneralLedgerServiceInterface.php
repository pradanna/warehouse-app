<?php

namespace App\Services\OutletGeneralLedger;

use App\Commons\Http\ServiceResponse;
use App\Schemas\OutletGeneralLedger\OutletGeneralLedgerQuery;

interface OutletGeneralLedgerServiceInterface
{
    public function findAll(OutletGeneralLedgerQuery $queryParams): ServiceResponse;
}
