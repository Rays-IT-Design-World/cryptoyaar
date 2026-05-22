<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PlanModel;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function purchase(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $user = User::lockForUpdate()->findOrFail($request->user_id);

                $hasPlan = DB::table('user_plans')
                    ->where('user_id', $user->id)
                    ->where('status', 'paid')
                    ->where('expire_at', '>=', now())
                    ->exists();

                if ($hasPlan) {
                    throw new \Exception('User already has an active plan');
                }

                $plan = PlanModel::findOrFail($request->plan_id);
                $subscriptionAmount = $plan->price;

                $companyRevenue = round(($subscriptionAmount * 22) / 100, 2);

                $gst = round(($subscriptionAmount * 18) / 100, 2);

                $creatorPool = round(($subscriptionAmount * 25) / 100, 2);

                $referralDistribution = round(($subscriptionAmount * 35) / 100, 2);

                DB::table('user_plans')->insert([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'payment_id' => 'admin-' . Str::random(10),
                    'expire_at' => now()->addDays(30),
                    'status' => 'paid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('subscription_revenues')->insert([
                    'user_id' => $user->id,
                    'subscription_amount' => $subscriptionAmount,
                    'company_revenue' => $companyRevenue,
                    'gst' => $gst,
                    'creator_pool' => $creatorPool,
                    'referral_amount' => $referralDistribution,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // referral code
                if (!$user->referral_code) {
                    do {
                        $code = strtoupper(Str::random(8));
                    } while (User::where('referral_code', $code)->exists());

                    $user->update(['referral_code' => $code]);
                }

                Wallet::firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0, 'locked_balance' => 0]
                );

                if ($user->parent_id) {
                    // $this->distributeCommissionTree($user->id, $plan->price);
                    $this->distributeCommissionTree($user->id, $subscriptionAmount);
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Plan purchased successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    

    private function distributeCommissionTree($userId, $planPrice)
    {
        $levels = [
            1 => 20,
            2 => 5,
            3 => 2,
            4 => 2,
            5 => 1,
            6 => 1,
            7 => 0.5,
            8 => 0.5,
            9 => 0.5,
            10 => 0.5,
            11 => 0.5,
            12 => 0.5,
            13 => 0.5,
            14 => 0.3,
            15 => 0.2,
        ];

        $scale = 35 / array_sum($levels);
        $currentId = $userId;

        for ($level = 1; $level <= 15; $level++) {

            $user = User::select('parent_id')->find($currentId);
            if (!$user || !$user->parent_id) break;

            $parent = User::select('id', 'parent_id', 'level_unlocked')
                ->find($user->parent_id);

            if (!$parent) break;

            $plan = DB::table('user_plans')
                ->where('user_id', $parent->id)
                ->where('status', 'paid')
                ->where('expire_at', '>=', now())
                ->latest()
                ->first();

            if (!$plan || $level > ($parent->level_unlocked ?? 0)) {
                $currentId = $parent->id;
                continue;
            }

            $exists = DB::table('referral_commissions')
                ->where([
                    ['user_id', $parent->id],
                    ['from_user_id', $userId],
                    ['level', $level],
                ])
                ->exists();

            if ($exists) {
                $currentId = $parent->id;
                continue;
            }

            $amount = round(($planPrice * ($levels[$level] * $scale)) / 100, 2);

            DB::table('referral_commissions')->insert([
                'user_id' => $parent->id,
                'from_user_id' => $userId,
                'level' => $level,
                'amount' => $amount,
                'is_refunded' => false,
                'created_at' => now(),
            ]);

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $parent->id],
                ['balance' => 0, 'locked_balance' => 0]
            );

            DB::table('wallets')
                ->where('id', $wallet->id)
                ->update([
                    'locked_balance' => DB::raw("locked_balance + $amount"),
                ]);

            WalletTransaction::create([
                'user_id' => $parent->id,
                'amount' => $amount,
                'type' => 'credit',
                'remark' => "Level $level commission",
                'is_locked' => true,
                'unlock_at' => now()->addDays(7),
            ]);

            $currentId = $parent->id;
        }
    }
}
