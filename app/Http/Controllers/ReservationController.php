<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // نمایش لیست تمام رزروها (به همراه اطلاعات کاربر و سالن مربوطه)
    public function index()
    {
        $reservations = Reservation::with(['user', 'hall', 'payment'])->get();

        return response()->json([
            'status' => true,
            'data' => $reservations
        ], 200);
    }

    // ثبت رزرو جدید
    public function store(Request $request)
    {
        // اعتبارسنجی داده‌های ارسالی
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hall_id' => 'required|exists:halls,id',
            'reservation_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_price' => 'required|numeric',
        ]);

        // ایجاد رزرو جدید در دیتابیس
        $reservation = Reservation::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'رزرو با موفقیت ثبت شد.',
            'data' => $reservation
        ], 201);
    }
}
