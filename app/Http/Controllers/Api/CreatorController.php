<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreatorRequest;
use App\Models\VideoModel;
use App\Models\VideoView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CreatorController extends Controller
{
    //

    public function submit(Request $request)
    {
        $request->validate([
            'youtube_link'    => 'required|url',
            'instagram_link'  => 'nullable|url',
            'top_video_link'  => 'required|url',
        ]);

        $user = auth()->user();

        
        if($user->role === 'creator'){
            return response()->json([
                'message' => 'You are already a creator'
            ], 409);
        }

        if($user->creatorRequest){
            return response()->json([
                'message' => 'Creator request already submitted',
                'status'  => $user->creatorRequest->status
            ], 409);
        }

        CreatorRequest::create([
            'user_id' => $user->id,
            'youtube_link' => $request->youtube_link,
            'instagram_link' => $request->instagram_link,
            'top_video_link' => $request->top_video_link,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Creator request submitted successfully'
        ]);
    }


    // check creator request active or not
    // public function status()
    // {
    //     return response()->json(
    //         auth()->user()->creatorRequest
    //     );
    // }

    public function status()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        

        $request = $user->creatorRequest;

        if (!$request) {
            return response()->json([
                'status' => false,
                'message' => 'No request found',
                'data' => null
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Creator request status',
            'data' => [
                'status' => $request->status,
            ]
        ]);
    }


    public function add_video(Request $request)
    {
        $request->validate([
            'title'       => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'keywords' => 'nullable|string|max:500',
            'video_path'  => 'required|mimes:mp4,mkv,avi,webm|max:512000',
            'thumbnail'   => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_free'     => 'required|boolean',
            'plan_id'     => 'required_if:is_free,0|nullable|exists:plans,id',
        ]);

       
        $keywords = $request->keywords;

        $cleanKeywords = null;

        if ($keywords) {
            $array = explode(',', $keywords);

            $array = array_map(function ($item) {
                return strtolower(trim($item)); 
            }, $array);

            $array = array_filter($array);

            $cleanKeywords = implode(',', $array);
        }

        
        $videoPath = $request->file('video')->store('videos', 'public');
        $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');

        $video = VideoModel::create([
            'title'       => $request->title,
            'description' => $request->description,
            'keywords'    => $cleanKeywords,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'super_subcategory_id' => $request->super_subcategory_id,
            'plan_id'     => $request->is_free ? null : $request->plan_id,
            'thumbnail'   => $thumbnailPath,
            'video_path'  => $videoPath,
            'is_free'     => $request->is_free,
            'creator_id'  => auth()->id(),
            'uploaded_by' => 'creator',
            'status'      => '0',
            'views'       => 0,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Video uploaded successfully, waiting for approval',
            'data'    => $video
        ]);
    }

    

    public function myVideos()
    {
        $videos = VideoModel::where('uploaded_by', 'creator')
                    ->where('creator_id', Auth::id())
                    ->latest()
                    ->get();

        return response()->json([
            'status' => true,
            'message' => 'Video list fetched successfully',
            'data' => $videos
        ]);
    }


    public function viewsList()
    {
        $videoIds = VideoModel::where('creator_id', Auth::id())
                        ->pluck('id');

        $totalViews = VideoView::whereIn('video_id', $videoIds)
                        ->where('is_valid', 1)
                        ->count();

        $thisMonthViews = VideoView::whereIn('video_id', $videoIds)
                            ->where('is_valid', 1)
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();

        $lastMonthViews = VideoView::whereIn('video_id', $videoIds)
                            ->where('is_valid', 1)
                            ->whereMonth('created_at', now()->subMonth()->month)
                            ->whereYear('created_at', now()->subMonth()->year)
                            ->count();

        return response()->json([
            'status' => true,

            'analytics' => [
                'total_views' => $totalViews,
                'this_month_views' => $thisMonthViews,
                'last_month_views' => $lastMonthViews,
            ]
        ]);
    }

    public function creatorPayoutSummary()
    {
        $creatorId = Auth::id();


        $currentMonthPayout = DB::table('creator_payouts')

            ->where('creator_id', $creatorId)

            ->where('month', now()->month)

            ->where('year', now()->year)

            ->sum('payout_amount');


        $lastMonthPayout = DB::table('creator_payouts')

            ->where('creator_id', $creatorId)

            ->where('month', now()->subMonth()->month)

            ->where('year', now()->subMonth()->year)

            ->sum('payout_amount');


        $totalPayout = DB::table('creator_payouts')

            ->where('creator_id', $creatorId)

            ->sum('payout_amount');


        $currentMonthWatchTime = DB::table('creator_payouts')

            ->where('creator_id', $creatorId)

            ->where('month', now()->month)

            ->where('year', now()->year)

            ->sum('total_watch_time');

        return response()->json([

            'status' => true,

            'data' => [

                'current_month_payout' =>
                    round($currentMonthPayout, 2),

                'last_month_payout' =>
                    round($lastMonthPayout, 2),

                'total_payout' =>
                    round($totalPayout, 2),

                'current_month_watch_time' =>
                    $currentMonthWatchTime,
            ]
        ]);
    }

}
