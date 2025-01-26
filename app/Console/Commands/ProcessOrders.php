<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class ProcessOrders extends Command
{
    public function __construct(
        private readonly OrderService $orderService
    ){
        parent::__construct();

    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes and matches buy and sell orders.';
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->info('Processing orders...');

            $this->orderService->processOrders();

            $this->info('Orders processed successfully.');

        }catch (\Exception $exception){
            //TODO we must set a logger aggregator such as Sentry
            $this->error('Error processing orders: ' . $exception->getMessage());
        }
    }


}
