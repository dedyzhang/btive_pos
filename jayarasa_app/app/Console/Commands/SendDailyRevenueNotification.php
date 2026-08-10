<?php

namespace App\Console\Commands;

use App\Services\FcmService;
use Illuminate\Console\Command;

class SendDailyRevenueNotification extends Command
{
    protected $signature = 'report:send-daily-revenue';

    protected $description = "Push today's total revenue to admin devices via FCM";

    public function handle(FcmService $fcmService): int
    {
        $fcmService->notifyDailyRevenue();

        $this->info('Daily revenue notification dispatched.');

        return self::SUCCESS;
    }
}
