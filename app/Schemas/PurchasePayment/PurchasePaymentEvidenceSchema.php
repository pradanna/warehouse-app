<?php

namespace App\Schemas\PurchasePayment;

use App\Commons\Schema\BaseSchema;
use Illuminate\Http\UploadedFile;

class PurchasePaymentEvidenceSchema extends BaseSchema
{
    /** @var UploadedFile $evidence */
    private $evidence;

    protected function rules()
    {
        return [
            'evidence' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ];
    }

    public function hydrateBody()
    {
        $evidence = $this->body['evidence'];
        $this->setEvidence($evidence);
    }

    /**
     * Get the value of evidence
     */
    public function getEvidence()
    {
        return $this->evidence;
    }

    /**
     * Set the value of evidence
     *
     * @return  self
     */
    public function setEvidence($evidence)
    {
        $this->evidence = $evidence;

        return $this;
    }
}
