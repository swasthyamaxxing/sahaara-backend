<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\WebPushNotification;
use Illuminate\Support\Facades\Log;

class SendBulkPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage in CLI: php artisan push:send-all "Optional Custom Message"
     */
    protected $signature = 'push:send-all {message? : Custom body message for the push notification}';

    /**
     * The console command description.
     */
    protected $description = 'Send a web push notification to all users who have active push subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customMessage = $this->argument('message') ?? 'This is a broadcast notification for all subscribers!';
        $title = '📢 System Announcement';
        $actionUrl = '/care-taker/dashboard';

        $this->info('Finding subscribed users...');

        // 1. Fetch only users who have at least 1 active push subscription record
        $users = $users = User::has('pushSubscriptions')->get();

        if ($users->isEmpty()) {
            $this->warn('No users with active push subscriptions were found.');
            return Command::SUCCESS;
        }

        $this->info("Sending push notification to {$users->count()} subscribed user(s)...");

        $successCount = 0;
        $failCount = 0;

        // 2. Loop through users and trigger notification
        foreach ($users as $user) {
            try {
                $user->notify(new WebPushNotification($title, $customMessage, $actionUrl));
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                Log::error("Failed to dispatch push notification to User #{$user->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Subscribed Users Found', $users->count()],
                ['Successfully Dispatched', $successCount],
                ['Failed', $failCount],
            ]
        );

        $this->info('Bulk notification command completed!');

        return Command::SUCCESS;
    }
}