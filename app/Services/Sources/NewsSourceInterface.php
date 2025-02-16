<?php

namespace App\Services\Sources;

use Illuminate\Support\Collection;

interface NewsSourceInterface
{
    public function fetch(): Collection;
}
