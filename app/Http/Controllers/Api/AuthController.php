<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Otp;
use App\Models\EventInterest;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10'
        ]);

        $otp = 123456;
        // $otp = rand(100000,999999);

        Otp::updateOrCreate(
            ['phone' => $request->phone],
            [
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5)
            ]
        );

        return response()->json([
            'status' => true,
            'otp' => $otp 
        ]);
    }

    // public function verifyOtp(Request $request)
    // {
    //     $request->validate([
    //         'phone' => 'required',
    //         'otp' => 'required'
    //     ]);

    //     $otpData = Otp::where('phone',$request->phone)
    //         ->where('otp',$request->otp)
    //         ->where('expires_at','>=',now())
    //         ->first();

    //     if(!$otpData){
    //         return response()->json(['error'=>'Invalid OTP'],401);
    //     }

    //     $user = User::firstOrCreate(
    //         ['phone'=>$request->phone],
    //         ['role'=>'user']
    //     );

    //     $token = $user->createToken('api-token')->plainTextToken;

    //     return response()->json([
    //         'token' => $token,
    //         'user' => $user
    //     ]);
    // }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required'
        ]);

        $otpData = Otp::where('phone',$request->phone)
            ->where('otp',$request->otp)
            ->where('expires_at','>=',now())
            ->first();

        if(!$otpData){
            return response()->json(['error'=>'Invalid OTP'],401);
        }

        $user = User::firstOrCreate(
            ['phone'=>$request->phone],
            ['role'=>'user']
        );

        $accessToken = $user->createToken('api-token')->plainTextToken;

        $refreshToken = Str::random(64);

        $user->update([
            'refresh_token' => hash('sha256', $refreshToken)
        ]);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => $user
        ]);
    }

    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required'
        ]);

        $user = User::where(
            'refresh_token',
            hash('sha256', $request->refresh_token)
        )->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid refresh token'
            ], 401);
        }

        $user->tokens()->delete();

        $newAccessToken = $user
            ->createToken('api-token')
            ->plainTextToken;

        return response()->json([
            'status' => true,
            'access_token' => $newAccessToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $request->user()->update([
            'refresh_token' => null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    
   


}
