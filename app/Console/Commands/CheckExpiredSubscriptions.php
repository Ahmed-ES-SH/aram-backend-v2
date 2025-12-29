<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Services\SubscriptionService;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and mark them as expired.';

    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting subscription expiration check...');

        try {
            $this->subscriptionService->checkAndExpireSubscriptions();
            $this->info('Expired subscriptions checked successfully.');
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
