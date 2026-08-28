<?php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [];

    // هر نظر متعلق به یک کاربر است
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // هر نظر برای یک سالن ثبت شده است
    public function hall()
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }
}