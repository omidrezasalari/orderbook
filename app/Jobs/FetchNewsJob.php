<?php

namespace App\Jobs;

use App\Services\NewsAggregatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchNewsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly NewsAggregatorService $newsAggregatorService)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->newsAggregatorService->fetchAndStore();
    }
}
