<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    /**
     * ارسال کد بازیابی رمز عبور
     */
    public function sendResetCode(Request $request)
    {
        $fields = $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $phone = $fields['phone'];

        // پیدا کردن کاربر
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'کاربری با این شماره موبایل پیدا نشد.'
            ], 404);
        }

        // تولید کد ۶ رقمی
        $code = random_int(100000, 999999);

        // حذف کد قبلی
        DB::table('password_reset_codes')
            ->where('phone', $phone)
            ->delete();

        // ذخیره کد جدید
        DB::table('password_reset_codes')->insert([
            'phone' => $phone,

            // کد به صورت Hash ذخیره می‌شود
            'code' => Hash::make($code),

            'expires_at' => Carbon::now()->addMinutes(5),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        /*
         * اینجا باید کد را با سرویس SMS ارسال کنی.
         *
         * مثال:
         * SmsService::send($phone, $code);
         *
         * فعلاً برای تست، فقط پیام عمومی برمی‌گردانیم.
         */

        return response()->json([
            'message' => 'کد تأیید به شماره موبایل شما ارسال شد.'
        ], 200);
    }


    /**
     * تأیید کد بازیابی
     */
    public function verifyResetCode(Request $request)
    {
        $fields = $request->validate([
            'phone' => 'required|string|max:20',
            'code' => 'required|digits:6',
        ]);

        $phone = $fields['phone'];
        $code = $fields['code'];

        $resetCode = DB::table('password_reset_codes')
            ->where('phone', $phone)
            ->first();

        if (!$resetCode) {
            return response()->json([
                'message' => 'کد تأیید یافت نشد یا منقضی شده است.'
            ], 422);
        }

        // بررسی انقضای کد
        if (Carbon::parse($resetCode->expires_at)->isPast()) {
            DB::table('password_reset_codes')
                ->where('phone', $phone)
                ->delete();

            return response()->json([
                'message' => 'کد تأیید منقضی شده است.'
            ], 422);
        }

        // بررسی کد
        if (!Hash::check($code, $resetCode->code)) {
            return response()->json([
                'message' => 'کد تأیید اشتباه است.'
            ], 422);
        }

        return response()->json([
            'message' => 'کد تأیید صحیح است.'
        ], 200);
    }


    /**
     * تغییر رمز عبور
     */
    public function resetPassword(Request $request)
    {
        $fields = $request->validate([
            'phone' => 'required|string|max:20',
            'code' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $phone = $fields['phone'];
        $code = $fields['code'];

        // پیدا کردن کاربر
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'کاربری با این شماره موبایل پیدا نشد.'
            ], 404);
        }

        // پیدا کردن کد
        $resetCode = DB::table('password_reset_codes')
            ->where('phone', $phone)
            ->first();

        if (!$resetCode) {
            return response()->json([
                'message' => 'کد تأیید یافت نشد یا منقضی شده است.'
            ], 422);
        }

        // بررسی انقضا
        if (Carbon::parse($resetCode->expires_at)->isPast()) {
            DB::table('password_reset_codes')
                ->where('phone', $phone)
                ->delete();

            return response()->json([
                'message' => 'کد تأیید منقضی شده است.'
            ], 422);
        }
// بررسی کد
        if (!Hash::check($code, $resetCode->code)) {
            return response()->json([
                'message' => 'کد تأیید اشتباه است.'
            ], 422);
        }

        // تغییر رمز عبور
        $user->password = Hash::make($fields['password']);
        $user->save();

        // حذف کد بعد از استفاده
        DB::table('password_reset_codes')
            ->where('phone', $phone)
            ->delete();

        return response()->json([
            'message' => 'رمز عبور با موفقیت تغییر کرد.'
        ], 200);
    }
}