<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\FetchNewsJob;
use App\Services\NewsAggregatorService;
use Illuminate\Support\Facades\Queue;
use Mockery;

class FetchNewsJobTest extends TestCase
{
    public function test_job_dispatches_correctly()
    {
        Queue::fake();

        FetchNewsJob::dispatch();

        Queue::assertPushed(FetchNewsJob::class);
    }

    public function test_job_calls_news_aggregator_service()
    {
        $newsAggregatorMock = Mockery::mock(NewsAggregatorService::class);
        $newsAggregatorMock->shouldReceive('fetchAndStore')->once();

        $job = new FetchNewsJob();
        $job->handle($newsAggregatorMock);
    }
}

