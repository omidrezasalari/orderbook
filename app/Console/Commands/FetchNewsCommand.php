<?php

namespace App\Console\Commands;

use App\Jobs\FetchNewsJob;
use Illuminate\Console\Command;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch news from sources';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        FetchNewsJob::dispatch();
        $this->info('News fetching job dispatched.');
    }
}
