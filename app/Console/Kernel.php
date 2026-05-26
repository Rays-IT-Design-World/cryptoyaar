<?php

namespace App\Console;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('creator:payout')
            //  ->everyMinute();
            ->monthlyOn(1, '00:10');

        $schedule->call(function () {

            \Log::info('Plan expire cron running');

            DB::table('user_plans')

                ->where('status', 'paid')

                ->where('expire_at', '<', now())

                ->update([

                    'status' => 'expired',

                    'updated_at' => now()
                ]);

        })

        ->name('plan_expire_cron')

        ->everyMinute()

        ->withoutOverlapping();   

        $schedule->call(function () {

            WalletTransaction::where('is_locked', true)
                ->where('unlock_at', '<=', now())
                ->orderBy('id')
                ->chunk(100, function ($transactions) {

                    DB::transaction(function () use ($transactions) {

                        foreach ($transactions as $txn) {

                            $txn = WalletTransaction::where('id', $txn->id)
                                ->lockForUpdate()
                                ->first();

                            if (!$txn || !$txn->is_locked) {
                                continue;
                            }

                            $wallet = Wallet::where('user_id', $txn->user_id)
                                ->lockForUpdate()
                                ->first();

                            if ($wallet) {
                                $newLocked = max(0, $wallet->locked_balance - $txn->amount);

                                $wallet->update([
                                    'locked_balance' => $newLocked,
                                    'balance' => DB::raw("balance + {$txn->amount}")
                                ]);
                            }

                            $txn->update(['is_locked' => false]);
                        }
                    });
                });
        })
            ->name('wallet_unlock_cron') 
            ->everyMinute()
            ->withoutOverlapping();
    }
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }


}
