<?php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // هر پرداخت متعلق به یک رزرو است
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
}
