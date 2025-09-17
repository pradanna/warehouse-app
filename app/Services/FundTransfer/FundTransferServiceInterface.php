<?php

namespace App\Services\FundTransfer;

use App\Commons\Http\ServiceResponse;
use App\Schemas\FundTransfer\FundTransferQuery;
use App\Schemas\FundTransfer\FundTransferSchema;

interface FundTransferServiceInterface
{
    public function create(FundTransferSchema $schema): ServiceResponse;
    public function findAll(FundTransferQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, FundTransferSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
