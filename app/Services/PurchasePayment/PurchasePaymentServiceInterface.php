<?php

namespace App\Services\PurchasePayment;

use App\Commons\Http\ServiceResponse;
use App\Schemas\PurchasePayment\PurchasePaymentEvidenceSchema;
use App\Schemas\PurchasePayment\PurchasePaymentQuery;
use App\Schemas\PurchasePayment\PurchasePaymentSchema;

interface PurchasePaymentServiceInterface
{
    public function create(PurchasePaymentSchema $schema): ServiceResponse;
    public function findAll(PurchasePaymentQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function uploadEvidence($id, PurchasePaymentEvidenceSchema $schema): ServiceResponse;
}
