<?php

namespace App\DTOs;

class InsuranceRequestData
{
    public string $holder;
    public string $prevInsuranceExists;
    public int $prevInsuranceYears;
    public ?string $occasionalDriver;

    public function __construct(
        string $holder,
        string $prevInsuranceExists,
        int $prevInsuranceYears,
        ?string $occasionalDriver = null
    ) {
        $this->holder = strtoupper($holder);
        $this->prevInsuranceExists = strtoupper($prevInsuranceExists);
        $this->prevInsuranceYears = $prevInsuranceYears;
        $this->occasionalDriver = $occasionalDriver ? strtoupper($occasionalDriver) : null;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['holder'],
            $data['prevInsurance_exists'],
            $data['prevInsurance_years'],
            $data['occasionalDriver'] ?? null
        );
    }
}

