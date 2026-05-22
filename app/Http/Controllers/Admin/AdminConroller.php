<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\CommentModel;
use App\Models\ContactEnquiry;
use App\Models\CreatorRequest;
use App\Models\EventInterest;
use App\Models\PlanModel;
use App\Models\User;
use App\Models\VideoModel;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;



class AdminConroller extends Controller
{
    //
    public function index()
    {
        $totalUsers = User::count();
        $totalCategory = Category::count();
        $totalStaff = Admin::Where('role_id', '3')->count();
        $totalCompanyRevenue = DB::table('subscription_revenues')->sum('company_revenue');
        $totalGST = DB::table('subscription_revenues')->sum('gst');
        $totalCreatorPool = DB::table('subscription_revenues')->sum('creator_pool');
        $totalReferral = DB::table('subscription_revenues')->sum('referral_amount');
        return view('backend.dashboard', compact('totalUsers', 'totalCategory', 'totalStaff','totalCompanyRevenue','totalGST','totalCreatorPool','totalReferral'));
    }


    public function likeTest()
    {
        $videos = VideoModel::all();
        $comment = CommentModel::all();
        return view('backend.video.like-test', compact('videos'));
    }

    public function creatorReq()
    {
        $creators = CreatorRequest::with('user')->where('status', 'pending')->get();
        return view('backend.creator.index', compact('creators'));
    }

    public function updateStatus(Request $request, $id)
    {
        $creator = CreatorRequest::with('user')->findOrFail($id);

        $creator->status = $request->status;
        $creator->save();

        if ($request->status === 'approved') {
            $creator->user->update([
                'role' => 'creator'
            ]);
        }

        if (in_array($request->status, ['rejected', 'pending'])) {
            $creator->user->update([
                'role' => 'user'
            ]);
        }

        return back()->with('success', 'Status updated successfully');
    }

    public function enquiryList()
    {
        $enquiryData = ContactEnquiry::get();
        return view('backend.enquiry', compact('enquiryData'));
    }

    public function enquirydestroy($id)
    {
        $category = ContactEnquiry::findOrFail($id);
        $category->delete();

        return redirect()->back()
            ->with('success', 'Enquiry List deleted successfully');
    }

    public function userlist()
    {
        $usersList = User::where('role', 'user')->latest()->get();
        return view('backend.user_list', compact('usersList'));
    }

    public function usercreate()
    {
        $plan = PlanModel::all();
        return view('backend.add_user', compact('plan'));
    }

    public function myNetwork()
    {
        $users = User::with([
            'wallet',
            'parentUser',
            'referrals.referrals.referrals'
        ])
            ->whereNull('referred_by')
            ->get();

        $wallet = Wallet::where('user_id', auth()->id())->first();

        $allUserIds = $users->pluck('id')
            ->merge(
                $users->flatMap(function ($user) {
                    return $this->getAllReferralIds($user);
                })
            )
            ->unique();

        $userPlans = DB::table('user_plans')
            ->whereIn('user_id', $allUserIds)
            ->where('status', 'paid')
            ->orderByDesc('id')
            ->get()
            ->groupBy('user_id');

        $users = $this->processUsers($users, $userPlans);

        $transactions = WalletTransaction::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('backend.myNetwork', compact(
            'users',
            'wallet',
            'transactions'
        ));
    }
    private function processUsers($users, $userPlans)
    {
        return $users->map(function ($user) use ($userPlans) {

            $wallet = $user->wallet;

            $total = $wallet->balance ?? 0;
            $locked = $wallet->locked_balance ?? 0;
            $available = $total - $locked;

            $plan = $userPlans[$user->id][0] ?? null;

            $canRefund = false;
            $timeLeft = null;

            if ($plan) {
                $purchaseDate = \Carbon\Carbon::parse($plan->created_at);
                $refundLastDate = $purchaseDate->copy()->addDays(7);

                if (now()->lt($refundLastDate)) {
                    $canRefund = true;
                    $timeLeft = now()->diffForHumans($refundLastDate, true);
                }
            }

            $user->wallet_total = $total;
            $user->wallet_locked = $locked;
            $user->wallet_available = $available;

            $user->can_refund = $canRefund;
            $user->refund_time_left = $timeLeft;

            if ($user->referrals && $user->referrals->count()) {
                $user->referrals = $this->processUsers($user->referrals, $userPlans);
            }

            return $user;
        });
    }

    private function getAllReferralIds($user)
    {
        $ids = collect();

        foreach ($user->referrals as $ref) {
            $ids->push($ref->id);
            $ids = $ids->merge($this->getAllReferralIds($ref));
        }

        return $ids;
    }


    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|numeric',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'user',
            'password' => bcrypt($request->password),
        ]);
        return back()->with('success', 'User created successfully');
    }

    public function userStoreplan(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
        ]);

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

            DB::table('user_plans')->insert([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'payment_id' => 'admin-' . Str::random(10),
                'expire_at' => now()->addDays(30),
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!$user->referral_code) {

                do {
                    $code = strtoupper(Str::random(8));
                } while (User::where('referral_code', $code)->exists());

                $user->update([
                    'referral_code' => $code
                ]);
            }

            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$wallet) {
                Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                    'locked_balance' => 0
                ]);
            }

            if ($user->parent_id) {
                $this->distributeCommissionTree($user->id, $plan->price);
            }
        });

        return back()->with('success', 'Plan purchased successfully');
    }


    public function userStoreReferral(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'referred_by' => 'required|exists:users,referral_code',
        ]);

        DB::transaction(function () use ($request) {

            $user = User::lockForUpdate()->findOrFail($request->user_id);

            if ($user->parent_id) {
                throw new \Exception('User already placed');
            }

            $referrerId = User::where('referral_code', $request->referred_by)->value('id');

            $placement = $this->findFreeParent($referrerId);

            if (!$placement) {
                throw new \Exception('Tree full');
            }

            $parentId = $placement['parent_id'];

            $user->update([
                'referred_by' => $referrerId,
                'parent_id' => $parentId,
            ]);

            $parent = User::lockForUpdate()->find($parentId);

            if ($placement['position'] === 'left') {
                if ($parent->left_child) throw new \Exception('Left filled');
                $parent->left_child = $user->id;
            } else {
                if ($parent->right_child) throw new \Exception('Right filled');
                $parent->right_child = $user->id;
            }

            $parent->save();

            $this->updateLevelUnlock($parent->id);
        });

        return back()->with('success', 'User placed successfully');
    }


    private function findFreeParent($rootId)
    {
        $queue = [$rootId];

        while ($queue) {
            $current = array_shift($queue);

            $user = User::select('id', 'left_child', 'right_child')->find($current);
            if (!$user) continue;

            if (!$user->left_child) {
                return ['parent_id' => $user->id, 'position' => 'left'];
            }

            if (!$user->right_child) {
                return ['parent_id' => $user->id, 'position' => 'right'];
            }

            $queue[] = $user->left_child;
            $queue[] = $user->right_child;
        }

        return null;
    }


    private function getDepth($userId)
    {
        if (!$userId) return 0;

        $user = User::find($userId);
        if (!$user) return 0;

        return 1 + max(
            $this->getDepth($user->left_child),
            $this->getDepth($user->right_child)
        );
    }

    private function updateLevelUnlock($userId)
    {
        $level = $this->getDepth($userId);

        User::where('id', $userId)->update([
            'level_unlocked' => min($level, 15)
        ]);
    }


    public function distributeCommissionTree($userId, $planPrice)
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

    public function refund($userId)
    {
        DB::transaction(function () use ($userId) {

            $plan = DB::table('user_plans')
                ->where('user_id', $userId)
                ->where('status', 'paid')
                ->latest()
                ->first();

            if (!$plan) {
                throw new \Exception('No active plan');
            }

            $expiry = Carbon::parse($plan->created_at)->addDays(7);

            if (now()->gt($expiry)) {
                throw new \Exception('Refund expired');
            }

            $commissions = DB::table('referral_commissions')
                ->where('from_user_id', $userId)
                ->where('is_refunded', false)
                ->get();

            foreach ($commissions as $c) {

                DB::table('wallets')
                    ->where('user_id', $c->user_id)
                    ->update([
                        'locked_balance' => DB::raw("GREATEST(locked_balance - $c->amount,0)")
                    ]);

                WalletTransaction::create([
                    'user_id' => $c->user_id,
                    'amount' => $c->amount,
                    'type' => 'debit',
                    'remark' => "Refund deduction",
                    'is_locked' => false,
                ]);

                DB::table('referral_commissions')
                    ->where('id', $c->id)
                    ->update(['is_refunded' => true]);
            }

            DB::table('user_plans')
                ->where('id', $plan->id)
                ->update(['status' => 'refunded']);
        });

        return back()->with('success', 'Refund successful');
    }


    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('backend.user_edit', compact('user'));
    }
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|numeric',
            'referred_by' => 'nullable|unique:users,referred_by,' . $user->id,
            'occupation' => 'nullable|string',
            'password' => 'nullable|min:6',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'referred_by' => $request->referred_by,
            'occupation' => $request->occupation,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return redirect()->route('userlist')
            ->with('success', 'User updated successfully');
    }
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()
            ->with('success', 'User deleted successfully');
    }

    public function interested()
    {
        $interestUsers = EventInterest::with(['user', 'event'])->get();

        return view('backend.event_interest', compact('interestUsers'));
    }

    public function creatorList()
    {
        $creatorList = CreatorRequest::with(['user'])->where('status', 'approved')->get();
        return view('backend.creator.creator_list', compact('creatorList'));
    }


    public function planPurchase()
    {
        $plans = DB::table('user_plans')
            ->join('users', 'user_plans.user_id', '=', 'users.id')
            ->join('plans', 'user_plans.plan_id', '=', 'plans.id')
            ->select(
                'user_plans.*',
                'users.name as user_name',
                'plans.plan_name as plan_name',
                'plans.price'
            )
            ->latest()
            ->get();

        return view('backend.plans.purchaselist', compact('plans'));
    }
    public function revenue(){
        $revenues = DB::table('subscription_revenues')
            ->join('users', 'subscription_revenues.user_id', '=', 'users.id')
            ->select(
                'subscription_revenues.*',
                'users.name as user_name'
            )
            ->latest()
            ->get();
        return view('backend.subscription_revenue',compact('revenues'));
    
    }

    public function destroyrevenue($id)
    {
        DB::table('subscription_revenues')
            ->where('id', $id)
            ->delete();

        return redirect()->back()
            ->with('success', 'Revenue deleted successfully');
    }
}

