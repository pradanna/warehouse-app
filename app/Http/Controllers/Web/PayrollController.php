<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\Payroll\PayrollCollection;
use App\Http\Resources\Payroll\PayrollResource;
use App\Schemas\Payroll\PayrollQuery;
use App\Schemas\Payroll\PayrollSchema;
use App\Services\Payroll\PayrollService;
use Illuminate\Http\Request;

class PayrollController extends CustomController
{
    /** @var PayrollService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new PayrollService();
    }

    public function create()
    {
        $schema = (new PayrollSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new PayrollResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new PayrollQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new PayrollCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new PayrollResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function update($id)
    {
        $schema = (new PayrollSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->update($id, $schema);
        return (new PayrollResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new PayrollResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
