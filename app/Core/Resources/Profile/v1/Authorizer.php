<?php

namespace App\Core\Resources\Profile\v1;

use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;

class Authorizer implements ProfileInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson)
    {
        $this->schemaJson = $schemaJson;
    }

    public function getDataMyProfile()
    {
        return $this->schemaJson->getDataMyProfile();
    }
}
