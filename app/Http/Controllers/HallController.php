<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use Illuminate\Http\Request;

class HallController extends Controller
{
    // نمایش لیست تمام سالن‌ها به همراه امکانات و نظرات
    public function index()
    {
        $halls = Hall::with(['amenities', 'reviews'])->get();
        
        return response()->json([
            'status' => true,
            'data' => $halls
        ], 200);
    }

    // نمایش جزئیات یک سالن خاص با استفاده از ID
    public function show($id)
    {
        $hall = Hall::with(['amenities', 'reviews.user', 'reservations'])->find($id);

        if (!$hall) {
            return response()->json([
                'status' => false,
                'message' => 'سالن مورد نظر یافت نشد.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $hall
        ], 200);
    }
}
