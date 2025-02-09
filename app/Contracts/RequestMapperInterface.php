<?php

namespace App\Contracts;

use App\DTOs\InsuranceRequestData;

interface RequestMapperInterface
{
    public function map(InsuranceRequestData $data): string;
}
