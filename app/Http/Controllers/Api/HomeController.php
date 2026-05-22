<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Category;
use App\Models\PlanModel;
use App\Models\Event;
use App\Models\VideoView;
use App\Models\EventInterest;
use App\Models\SupCategory;
use App\Models\supSubCategory;
use App\Models\VideoModel;
use App\Models\User;
use App\Models\ReferralCommission;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{


    public function expertCategories()
    {
        $categories = Category::where('is_expert_category',1)
                        ->select('id','name','category_image')
                        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Expert Category List',
            'data' => $categories
        ]);
    }
    
    public function faqList()
    {
        $faqs = Faq::select('id','question','answer')->get();

        return response()->json([
            'status' => true,
            'message' => 'FAQ List',
            'data' => $faqs
        ]);
    }

    public function event()
    {
        $events = Event::all();

        return response()->json([
            'status' => true,
            'message' => 'Events list',
            'data' => $events
        ]);
    }

    public function Videos()
    {
        $freeVideos = VideoModel::where('is_free', '1')
                        ->latest()
                        ->get();

        $paidVideos = VideoModel::where('is_free', '0')
                        ->latest()
                        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Video List',
            'free_videos' => $freeVideos,
            'paid_videos' => $paidVideos,
        ]);
    }

     public function eventInterested(Request $request)
    {
        $request->validate([
            'event_id' => 'required',
            'user_id' => 'required'
        ]);

        $check = EventInterest::where('event_id',$request->event_id)
                    ->where('user_id',$request->user_id)
                    ->first();

        if($check){
            return response()->json([
                'status'=>false,
                'message'=>'Already Interested'
            ]);
        }

        EventInterest::create([
            'event_id'=>$request->event_id,
            'user_id'=>$request->user_id
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Interest Added Successfully'
        ]);
    }

    public function planfetch()
    {
        $plans = PlanModel::where('status', 1)->get();

        return response()->json([
            'data' => $plans
        ]);
    }

    public function storeWatchTime(Request $request)
    {
        $userId = auth()->user();

        if ($request->watch_time < 30) {
            return response()->json(['status' => false]);
        }

        $existing = DB::table('video_views')
            ->where('user_id', $userId)
            ->where('video_id', $request->video_id)
            ->first();

        if ($existing) {

            if ($request->watch_time > $existing->watch_time) {

                DB::table('video_views')
                    ->where('id', $existing->id)
                    ->update([
                        'watch_time' => $request->watch_time,
                        'updated_at' => now()
                    ]);
            }

        } else {

            DB::table('video_views')->insert([
                'user_id' => $userId,
                'video_id' => $request->video_id,
                'watch_time' => $request->watch_time,
                'is_valid' => 1,
                'created_at' => now()
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function saveUserFavourite(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id'
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $user->categories()->sync($request->category_ids);

        $user->is_profile_completed = true;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Categories saved successfully'
        ]);
    }

    public function skipCategory()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $user->is_profile_completed = true; 
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Skipped'
        ]);
    }

    public function updateUserFavourite(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id'
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $user->categories()->sync($request->category_ids);

        return response()->json([
            'status' => true,
            'message' => 'Categories updated successfully'
        ]);
    }

    public function getUserFavourite()
    {
        $user = auth()->user();

        return response()->json([
            'status' => true,
            'data' => $user->categories
        ]);
    }

    public function getCategories(Request $request)
    {
        $user = auth()->user();

        $favouriteIds = DB::table('user_favourite')
            ->where('user_id', $user->id)
            ->pluck('category_id')
            ->toArray();

        $categories = Category::select('*')
            ->orderByRaw("FIELD(id, " . implode(',', $favouriteIds ?: [0]) . ") DESC")
            ->get();

        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }

    public function subcategories($category_id)
    {
        $subcategories = Supcategory::where('category_id', $category_id)->get();

        if ($subcategories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No subcategories found',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Subcategories list',
            'data' => $subcategories
        ]);
    }

    public function Supsubcategories($category_id)
    {
        $supsubcategories = supSubCategory::where('sub_category_id', $category_id)->get();

        if ($supsubcategories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No Sup subcategories found',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => ' Sup Subcategories list',
            'data' => $supsubcategories
        ]);
    }

    public function search(Request $request)
    {
        $search = trim($request->search);

        $videos = VideoView::where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%$search%")
                    ->orWhere('keywords', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            })
            ->get();

        return response()->json([
            'status' => true,
            'data' => $videos
        ]);
    }

    public function directMembers(Request $request)
    {
        $user = auth()->user();

        $members = User::where('referred_by', $user->id)
            ->select(
                'id',
                'name',
                'created_at'
            )
            ->get()
            ->map(function ($item) {

                return [
                    'name'   => $item->name,
                    'status' => 'Active',
                    'plan'   => 'Basic Plan',
                    'date'   => date('d M Y', strtotime($item->created_at)),
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $members
        ]);
    }

    private function getTeamMembers($userId, $level, &$team)
    {
        $members = User::where('referred_by', $userId)->get();

        foreach ($members as $member) {

            $team[] = [
                'level'  => $level,
                'name'   => $member->name,
                'status' => 'Active',
                'plan'   => 'Basic Plan',
                'date'   => date('d M Y', strtotime($member->created_at)),
            ];

            $this->getTeamMembers($member->id, $level + 1, $team);
        }
    }

    public function teamMembers()
    {
        $user = auth()->user();

        $team = [];

        $this->getTeamMembers($user->id, 1, $team);

        return response()->json([
            'status' => true,
            'data'   => $team
        ]);
    }

    public function earnings()
    {
        $user = auth()->user();

        $earnings = ReferralCommission::where('user_id', $user->id)
            ->with('fromUser')
            ->latest()
            ->get()
            ->map(function ($item) {

                return [
                    'level'      => $item->level,

                    'name'       => $item->fromUser->name ?? '',

                    'plan'       => 'Basic Plan',

                    'commission' => $item->amount,

                    'date'       => date('d M Y', strtotime($item->created_at)),
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $earnings
        ]);
    }

    public function walletBalance()
    {
        $user = auth()->user();

        $wallet = Wallet::where('user_id', $user->id)->first();

        return response()->json([

            'status' => true,

            'data' => [

                'balance' => $wallet->balance ?? 0,

                'locked_balance' => $wallet->locked_balance ?? 0,
            ]
        ]);
    }



}
