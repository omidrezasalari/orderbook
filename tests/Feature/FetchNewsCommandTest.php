<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use App\Jobs\FetchNewsJob;

class FetchNewsCommandTest extends TestCase
{
    public function test_news_fetch_command_dispatches_job()
    {
        Queue::fake();

        Artisan::call('news:fetch');

        Queue::assertPushed(FetchNewsJob::class);
    }
}
