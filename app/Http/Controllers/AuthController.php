<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'phone' => $fields['phone'] ?? null,
        ]);

        return response()->json([
            'message' => 'ثبت‌نام با موفقیت انجام شد',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        // چک کردن ایمیل کاربر
        $user = User::where('email', $fields['email'])->first();

        // چک کردن درستی پسورد
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'اطلاعات ورود نامعتبر است'
            ], 401);
        }

        return response()->json([
            'message' => 'ورود با موفقیت انجام شد',
            'user' => $user
        ], 200);
    }
    public function forgotPassword(Request $request)
{
    $fields = $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string|min:6'
    ]);

    // پیدا کردن کاربر با ایمیل
    $user = User::where('email', $fields['email'])->first();

    if (!$user) {
        return response()->json([
            'message' => 'کاربری با این ایمیل یافت نشد'
        ], 404);
    }

    // به‌روزرسانی رمز عبور جدید
    $user->password = Hash::make($fields['password']);
    $user->save();

    return response()->json([
        $user->name => 'رمز عبور با موفقیت تغییر یافت'
    ], 200);
}
}